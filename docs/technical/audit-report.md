# Laporan Audit Horizon-UniStock

- **Tanggal audit:** 14 Agustus 2026
- **Ruang lingkup:** Seluruh aplikasi — models (41), migrations (85), services (30), controllers, imports/exports, seeders, routes, 168 blade views, assets JS/CSS
- **Metode:** Pemeriksaan kode langsung (manual) + pengecekan silang terhadap `docs/project/erd.md`, `docs/project/architecture.md`, `docs/guides/desain.md`, `AGENTS.md`, dan best practices Laravel
- **Status:** 7 temuan CRITICAL sudah diperbaiki (commit `aa9ddb7` & lanjutannya) — dokumen ini terus diperbarui setelah setiap perbaikan

---

## 1. Ringkasan Eksekutif

| Severity | Jumlah | Kelompok utama |
|----------|--------|----------------|
| 🔴 CRITICAL | 7 | Login lockout dead code, default password super admin, OTP plaintext, import mahasiswa selalu gagal, otorisasi event size, konflik unique constraint cancel/retry, relasi model broken |
| 🟠 MAJOR | ±28 | Otorisasi/permission, perhitungan stok & GPM, validasi distribusi, import, seeder/schedule, item code |
| 🟡 MINOR | ±40 | Konsistensi UI/design-system, dead code, ERD stale, optimasi query |

**Temuan yang paling berdampak operasional:**
1. **Import mahasiswa selalu gagal** bila file template resmi dipakai (kolom "Tipe" diisi).
2. **Akun super admin pakai password default publik** (`SuperAdmin!123`) dan tanpa wajib ganti password.
3. **OTP email disimpan plaintext** di database.
4. **Perhitungan stok & GPM tidak konsisten** (HPP/price current vs captured, partial excluded, opening balance salah).
5. **Promote students menulis id generasi (int) ke kolom `student_level` (string kode)** — merusak semua fitur yang bergantung level.
6. **Distribusi staff tidak memvalidasi item terhadap jadwal/entitlement** — item/kuantitas sembarang bisa dibagikan.

---

## 2. 🔴 CRITICAL

### C1. Login lockout per-akun tidak pernah aktif (dead code) ✅ DIPERBAIKI
- **Lokasi:** `app/Http/Requests/Auth/LoginRequest.php:87-97`
- **Masalah:** `ensureIsNotRateLimited()` menghitung `$seconds`/`$minutes` tetapi **tidak pernah `throw`**. Satu-satunya proteksi adalah `throttle:5,1` di route (per-IP), sehingga brute force terdistribusi per-akun bisa lolos.
- **Dampak:** Akun pengguna rentan terhadap serangan brute force terdistribusi.
- **Perbaikan:** ✅ `throw ValidationException::withMessages(['email' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds/60)])])` sudah ditambahkan.

### C2. Password default super admin dikenal publik ✅ DIPERBAIKI
- **Lokasi:** `database/seeders/SuperadminSeeder.php:15,25,34-37`
- **Masalah:** Fallback password `SuperAdmin!123` dipakai bila `SUPERADMIN_PASSWORD` tidak di-set (dan `.env.example` tidak mendokumentasikannya), dicetak ke console saat seeder jalan, dan `must_change_password=false`.
- **Dampak:** Deployment tanpa set env var menghasilkan akun super admin dengan password publik yang tetap berlaku.
- **Perbaikan:** ✅ Hapus fallback publik (password acak dibuat bila env kosong), baca via `config('superadmin.*')` (aman `config:cache`), `must_change_password=true`, password tidak dicetak ke console. Tambah `config/superadmin.php` + dokumentasi `.env.example`.

### C3. OTP email disimpan plaintext ✅ DIPERBAIKI
- **Lokasi:** `app/Http/Controllers/Auth/EmailVerificationOtpController.php:35-42`, `app/Http/Controllers/Profile/EmailController.php:68-74`
- **Masalah:** Kode OTP disimpan apa adanya di tabel `otp_codes`.
- **Dampak:** Siapa pun dengan akses DB (atau lewat SQLi) bisa membaca kode OTP dan lolos verifikasi email / ganti email.
- **Perbaikan:** ✅ Simpan hanya `hash_hmac('sha256', $code, config('app.key'))`; verifikasi membandingkan hash.

### C4. Import mahasiswa selalu gagal saat kolom "Tipe" terisi ✅ DIPERBAIKI
- **Lokasi:** `app/Imports/StudentImport.php:192-194`
- **Masalah:** Setelah memetakan `student_level` (mis. `"Year 1 Sem 1"` → `Y1S1`), kode memvalidasi `str_contains($rawType, $record['student_level'])`. Untuk semua nilai real hasilnya `false` (kasus & konten tidak pernah cocok), sehingga `Failure` ditambahkan untuk **setiap baris** yang mengisi kolom "Tipe". Template resmi (`MahasiswaTemplateExport`) mengisi kolom ini → `validateRecords()` selalu mengembalikan `$failures` → `collection()` melempar `ValidationException` → import 100% gagal.
- **Dampak:** Fitur import mahasiswa (jalur utama input data) tidak berfungsi dengan template resmi.
- **Perbaikan:** ✅ Normalisasi `strtolower` untuk match, dan failure hanya muncul saat nilai benar-benar tidak dikenali (bukan salah bandingkan output vs input).

### C5. Submit ukuran (POST) lolos otorisasi event ✅ DIPERBAIKI
- **Lokasi:** `app/Http/Controllers/Student/SizeController.php:70-91`, `app/Services/StudentSizeService.php:153-236`
- **Masalah:** `store()` hanya memvalidasi `event_id exists`; `saveSizes()` mengecek `max_changes`/`allow_reedit` tetapi **tidak pernah memanggil `$event->isApplicableToStudent($student)`** (status aktif, window tanggal, fakultas/prodi/generasi/level). Endpoint GET `input` mengecek ini (403), tapi POST bisa langsung submit event non-aktif / milik fakultas lain.
- **Dampak:** Mahasiswa bisa mengirim ukuran ke event yang salah / tidak berlaku dan menghabiskan kuota `max_changes`.
- **Perbaikan:** ✅ `saveSizes()` kini memanggil `$event->isApplicableToStudent($student)` dan throw `RuntimeException` bila tidak berlaku.

### C6. Konflik unique constraint vs alur cancel/retry distribusi ✅ DIPERBAIKI
- **Lokasi:** `database/migrations/2026_08_06_000001_fix_logic_integrity.php:18` (unique `(student_id, schedule_id)`); `app/Services/DistributionService.php:107-115`
- **Masalah:** Unique index berlaku untuk semua status. Guard di service memblokir transaksi non-`cancelled`, namun status `cancelled` tidak pernah dihasilkan oleh kode mana pun (tidak ada alur cancel/void). Jika sebuah baris `cancelled` pernah ada, transaksi baru untuk `(student, schedule)` yang sama akan melanggar unique key — pengambilan ulang tidak mungkin.
- **Dampak:** Desain bertentangan; salah-scan/double-submit mengunci mahasiswa selamanya dari jadwal tersebut; status `cancelled` = dead code.
- **Perbaikan:** ✅ Unique `(student_id, schedule_id)` di-drop (migration `2026_08_14_100004`); proteksi anti-duplikat diandalkan pada guard + index `(student_id, schedule_id, status)` yang sudah ada.

### C7. Relasi model mereferensikan kolom FK yang tidak ada ✅ DIPERBAIKI
- **Lokasi:** `app/Models/StudyProgram.php:26-29`, `app/Models/StudentGeneration.php:30-33`
- **Masalah:** `hasMany(Entitlement::class)` dan `hasMany(Entitlement::class, 'generation_id')` mereferensikan kolom `study_program_id`/`generation_id` di tabel `entitlements`, tetapi migration `2026_07_02_100020_create_entitlements_table.php` hanya membuat `code, student_level, description, is_active`.
- **Dampak:** Query pada relasi ini akan melempar SQL error. Saat ini tidak tereksekusi, tapi menjadi bom waktu.
- **Perbaikan:** ✅ Kedua relasi dihapus (kolom FK memang tidak ada; entitlement berbasis `code + student_level`).

---

## 3. 🟠 MAJOR

### 3.1 Keamanan & Otorisasi

| # | Lokasi | Temuan | Perbaikan |
|---|--------|--------|-----------|
| M1 | `routes/web.php:44` | `/dashboard` hanya `auth`, bukan `password.changed` → user bisa melewati wajib ganti password dan melihat dashboard penuh | Tambah middleware `password.changed` |
| M2 | `routes/auth.php:15-18` | Register publik terbuka → akun tanpa role (spam DB, siswa 404) | Nonaktifkan/gate dengan invite token |
| M3 | `routes/web.php:81-163`, `RolePermissionSeeder.php` | Permission Spatie (`manage-*`) di-seed tapi tidak pernah di-enforce; `EntitlementPolicy` terdaftar tapi tak pernah dipanggil — akses murni berbasis `role:` | Enforce `permission:` middleware atau hapus scaffolding yang tak terpakai |
| M4 | `ScanController.php:99-112`, `DistributionService.php:128-196` | Staff bisa distribusi item/kuantitas sembarang tanpa validasi terhadap `schedule->items` atau entitlement | Validasi tiap item terhadap jadwal + entitlement (cap quantity) |
| M5 | `bootstrap/app.php:14` | `trustProxies(at:'*')` → spoof `X-Forwarded-For` melewati semua throttle & audit IP | Batasi ke CIDR proxy yang dikenal |
| M6 | `System/UserController.php`, `routes/web.php:190-198` | Admin bisa buat/edit akun admin lain (escalation) | Mutasi user dibatasi `super_admin` |
| M7 | `routes/web.php:170` | `POST /student/sizes` tanpa throttle (gabung C5) | Tambah `throttle:5,1` |

### 3.2 Logika Stok & Opname

| # | Lokasi | Temuan | Perbaikan |
|---|--------|--------|-----------|
| M8 | `ReportService.php:125-141` | Predikat filter bulan/tahun ada **di dalam ON** join `distribution_transactions` → "Unit Sold" per kategori menghitung all-time | Pindahkan filter ke `where()` atau join subquery pra-filter |
| M9 | `GpmReportExport.php:47-53`, `ReportService.php:87,162,431,472`, `GpmService.php:26,32` | Revenue dihitung dari `items.selling_price` **current**, bukan `selling_price_at_distribution` → mengubah harga sekarang merombak history GPM | Gunakan kolom captured `selling_price_at_distribution` |
| M10 | `GpmService.php:34-39,97` | Hanya filter `status='completed'`; transaksi `partial` (real, terkirim) dikeluarkan → GPM under-report | Gunakan `whereIn('status', ['completed','partial'])` |
| M11 | `StockCardReport.php:20,69-90` | Running balance mulai 0 (tanpa opening balance), HPP memakai `item->hpp` current bukan `movement->hpp`, `max(0,..)` menyembunyikan stok negatif, `max($in,$out)` double count | Seed opening balance, pakai `movement->hpp`, hapus clamp, `$in ?: $out` |
| M12 | `StockReport.php:106-109` | "Stok Awal" dan "Stok Akhir" sama-sama `quantity` current → Awal == Akhir selalu | `Stok Awal = quantity - total_in + total_out` |
| M13 | `StockOpnameService.php:82-176` | Branch OUT memanggil `deductStockFifo` (nested transaction); jika stok berubah sejak count, seluruh batch approval rollback → 500 | Hitung variance ulang terhadap saldo saat approval; sesuaikan ke stok tersedia |
| M14 | `StockOpnameController.php:68-83` | Re-upload opname menumpuk duplikat item (tidak hapus item lama) + uncaught exception → 500 | Hapus item lama per batch, wrap try/catch |

### 3.3 Distribusi & Entitlement

| # | Lokasi | Temuan | Perbaikan |
|---|--------|--------|-----------|
| M15 | `DistributionSchedule.php:35-41` | `scopeForStudent` tidak menyaring `generation_id` → schedule per-angkatan ditawarkan ke angkatan lain | Tambah `whereNull('generation_id')->orWhere('generation_id', $student->generation_id)` |
| M16 | `DistributionService.php:152-171` | Cek saldo tanpa lock, lalu `deductStockFifo` lock ulang → race condition: "Stok tidak mencukupi" palsu | Cek di dalam transaksi terkunci yang sama |
| M17 | `DistributionService.php:307-336` | `logSizeChange` mengubah `size` tapi tidak pernah `increment('change_count')` → fitur tampil 0 permanen | Tambahkan increment `change_count` |
| M18 | `ReportService.php:209-253` | Subquery eligible tidak scoped per periode, tapi received di-filter periode → "not_received" menyesatkan | Scope eligible ke periode yang sama |

### 3.4 Import

| # | Lokasi | Temuan | Perbaikan |
|---|--------|--------|-----------|
| M19 | `ImportController.php:37-61` | **Path traversal**: `str_starts_with('imports/')` + `Storage::path()` → `imports/../../.env` dapat dibuka/diparse | Validasi `realpath` tetap di dalam `storage/app/imports` |
| M20 | `ImportService.php:38-53` | `success_rows`/`total_rows` salah: `$collection->count()` menghitung baris judul; importer tanpa `getImportedCount` (mis. `StockOpnameImport`) dianggap semua sukses | Implementasikan `getImportedCount()` konsisten & hitung dari record nyata |
| M21 | Template A4 vs importer heading 1 | `StockReceiveTemplateExport`/`StockOpnameTemplateExport`/`KatalogTemplateExport`/`HargaTemplateExport`/`HakBarangTemplateExport`/`DpLunasTemplateExport` menulis header di baris 4 (`startCell A4`), tapi `StockReceiveImport`/`StockOpnameImport`/`ItemImport` memakai `headingRow():1` | Set `headingRow(): 4` pada ketiga importer |
| M22 | `EntitlementImport.php:54-84` | `updateOrCreate` hanya menambah/update; item yang dihapus dari file tidak pernah dihapus → sisa hak usang | Hapus `EntitlementItem` yang tidak ada di file sebelum upsert |

### 3.5 Seeder, Schedule, Command

| # | Lokasi | Temuan | Perbaikan |
|---|--------|--------|-----------|
| M23 | `routes/console.php:18` | `AutoPromoteStudents --semester=Y1S2` hanya mempromosikan kohort `Y1S2` sekali; sisanya tak pernah dipromosikan | Hapus `--semester` atau ubah semantik menjadi target level |
| M24 | `AutoPromoteStudents.php:64-66` vs `StudentService.php:139-142,148-185` | Preview menghitung semester dari `current_semester` saja, tapi `promoteStudents()` mendahulukan `student_level` → preview berbeda dari hasil aktual | Gunakan resolusi "effective semester" yang sama |
| M25 | `StudentService.php:196-198` | **`promoteStudents` menulis id generasi (int) ke kolom `student_level` (string kode)** → entitlement code, `scopeForStudent`, `isFreshman`, import level semuanya rusak | Tulis kode `StudentLevel` (mis. `Y2S1`); simpan kenaikan generasi di `generation_id` |
| M26 | `CalculateStudentSummaries.php:37-46` | `COUNT(*)` atas join `distribution_items` menghitung baris item, bukan transaksi | `COUNT(DISTINCT distribution_transactions.id)` |
| M27 | `SuperadminSeeder.php:14-15,25` | `env()` dipakai langsung (gagal setelah `config:cache`); `must_change_password=false` bertentangan dengan security.md | Baca via `config()`, wajibkan ganti password |

### 3.6 Item Code & Data Integrity

| # | Lokasi | Temuan | Perbaikan |
|---|--------|--------|-----------|
| M28 | `ItemService.php:24,52` vs `ItemImport.php:241-252,98,110` | Dua generator item code berbeda: manual 4 segmen (`UNF-L-SCB-02`), import 5-6 segmen (`UNF-L-SCB-01-01-04`). Keduanya menyimpang dari `docs/project/item-code.md` | Pilih satu skema kanonik dan seragamkan |
| M29 | `docs/project/security.md:28` | Dokumen menyebut role `finance`, kode memakai `admin` | Sinkronkan dokumen |
| M30 | `.env.example` | `SUPERADMIN_EMAIL`/`SUPERADMIN_PASSWORD` tidak terdokumentasi | Tambahkan ke `.env.example` |

---

## 4. 🟡 MINOR (terpilih)

### Logika & data
- `StockReceiveImport.php:214-220` — `parseDecimal` merusak pemisah ribuan Indonesia (`"1.250,50"` → ±1.25); `importedCount` menghitung baris yang di-skip; tidak ada cek duplikat `reference_number`.
- `EligibilityService.php:27-48` — `toggle` menghapus record bila ada; tidak bisa mengekspresikan "Belum Lunas" secara persisten; race double-click → unique violation.
- `ItemImport.php:241-252,72-83` — auto code bisa bertabrakan setelah soft-delete; `effective_date` memakai `now()->startOfYear()` bukan tahun akademik file.
- `StockOpnameImport.php:33-51` — baris tak match item/variant di-skip diam-diam (admin mengira semua tercatat).
- `DistributionService.php:182-190` — `distribution_items.unit_price` tidak pernah diisi (kolom ganda ambigu).
- `QrCodeService.php:13-20` — QR hanya encode NIM (tanpa token); NIM bisa ditebak → klaim identitas.
- `StockOpnameService.php:24`, `StockService.php:366` — sequence reference number pakai `count()+1` (race collision).
- `CleanupOldLogs.php:25-49` — `TRUNCATE failed_jobs`/`job_batches` menghancurkan pekerjaan retry; `File::put($log,'')` memotong log aktif.
- `AutoPromoteStudents.php:105-120` — generasi tahun berikutnya tak ditemukan → `null`, siswa dapat `generation_id` usang.
- `GenerateResolverService.php:40-43` — regex fallback salah ambil pasangan tahun (NIM `2401234567` → 1213 bukan 2425); auto-create generasi bogus.

### Model/Migration/ERD
- ERD mendokumentasikan tabel yang **tidak ada di schema final**: `distribution_periods`, `distribution_stages`.
- Kolom ERD yang **tidak pernah di-migrate**: `students.qr_token`/`qr_generated_at`, `entitlements.study_program_id`/`generation_id`/`period_id`, `item_prices.period_id`.
- Nama file migration menyesatkan: `2026_07_22_100002` "create_student_types" → bikin `student_levels`; `2026_07_02_100002` "create_program_levels" → bikin `student_generations`.
- `StudentLevel` memakai `kode` sebagai primary key non-increment sementara migration juga punya `id` auto-increment.
- Index redundan: `(student_id, schedule_id, status)` vs unique `(student_id, schedule_id)`; `(item_id, variant_id, type)` vs `(item_id, variant_id)`.
- Unique longgar: `entitlements.unique(code, student_level)` dan `item_prices.unique(item_id, effective_date)` dengan kolom nullable → banyak NULL diperbolehkan.
- ERD stale pada banyak kolom (28 item tercatat): `items`, `item_variants`, `stock_movements.hpp/stock_batch_id`, `stock_opnames.status` (draft/counted/approved), dsb.
- Model duplikat: `Student::programLevel()` = alias `generation()`; `Student::sizeProfiles()` `hasMany` vs constraint `unique student_id` (harus `hasOne`).

### UI / Frontend
- `resources/views/components/bottom-sheet.blade.php:14` — **syntax error PHP**: `md5 microtime(true)` (kurang kurung) → halaman yang memuat komponen ini error.
- `master/item-price/_table.blade.php:19` — tombol Edit pakai `wire:click` (Livewire) padahal Livewire **tidak di-install** dan modal `item-price-edit` tidak ada → tombol mati.
- `distribution/distribution-schedule/index.blade.php:24` — `x-model="period"` tidak didukung `serverTable` (tak ada properti `period`) → filter period diam-diam tidak jalan.
- `layouts/app.blade.php` — slot `<x-slot name="header">` tidak pernah di-render → judul ~48 halaman hilang.
- Violasi design system: focus ring `blue-500` di `study-program/{create,edit}`; `x-badge type="info"` biru; tile laporan warna-warni vs monokrom maroon.
- `auth/verify-email-otp.blade.php:18` — link "Skip" melewati verifikasi email.
- `student/sizes-index.blade.php:17`, `size-input:17` — hardcoded URL gambar Google eksternal.
- Inline `onsubmit="return confirm"` menggantikan `<x-delete-modal>`: `size-events/index:64`, `student/credentials:67,81,191`, `student/generate:26`, `stock-opname/show:87`.
- Judul halaman pakai `h2/h3` inline (bukan `<x-page-header>`): dashboard staff, faculty/index, item/index, entitlement/index, distribution-schedule/index, eligibility/index, item-price/index.
- Empty-state alert tak bersyarat (muncul terus): `faculty/{create,edit,show}`, `scan`, `distribution`, `import/result`.
- `inventory/stock-balance/_table.blade.php:13-17` — query `DB::table` per baris (N+1).

### Konfigurasi / Lainnya
- `f9webltd/simple-qrcode` di composer.json tapi tidak terpakai (QR pakai `bacon/bacon-qr-code`); `ext-gd` tidak terdokumentasi.
- `FakeDataSeeder` tidak di-wire ke `DatabaseSeeder` (akan abort bila master data kosong); key acak → duplikat saat re-run.
- `ItemSizeSeeder.php:108` — `sync()` merusak pivot `category_item_size` saat re-seed.
- SMTP password/api_key di-render ke input HTML (`smtp-settings.blade.php:114,155`); config terverifikasi disimpan di session plaintext (file driver).
- `SmtpSettingsServiceProvider.php:13-29` — query DB tiap request; sebaiknya cache.
- `Session/ENV`: `APP_ENV=local`, `APP_DEBUG=true`; `SESSION_DRIVER=file` tanpa encrypt.
- `EmailVerificationOtpController.php:83-102` — counter OTP session-only, reset by re-send; domain email kampus tidak divalidasi `@horizon.ac.id`.
- `PasswordResetLinkController.php:36-46` — NIM enumeration oracle (pesan berbeda untuk NIM valid/tidak).
- `UserTestSeeder` password `'password'` tanpa guard `APP_ENV !== production`.
- `StudentSummary` dihitung harian tapi **tidak pernah dibaca** view/controller mana pun.

---

## 5. Prioritas Perbaikan yang Disarankan

**Fase 1 — CRITICAL (paling mendesak)**
1. C2 — Hapus password default super admin + `must_change_password=true`
2. C3 — Hash OTP sebelum disimpan
3. C1 — Aktifkan login lockout (throw ValidationException)
4. C4 — Perbaiki validasi `student_type_raw` di `StudentImport`
5. C5 — Enforce `isApplicableToStudent` pada POST ukuran
6. C6 — Selesaikan konflik unique constraint vs alur cancel
7. C7 — Perbaiki/hapus relasi broken di model

**Fase 2 — MAJOR (keandalan data & keamanan)**
- M3/M4 — Enforce permission Spatie + validasi item distribusi
- M9/M10 — Konsistensi price capture & partial di GPM
- M25 — Perbaiki `promoteStudents` (jangan tulis id ke `student_level`)
- M11/M12 — Perbaiki StockCard & StockReport
- M19/M20/M21 — Path traversal, accounting import, heading row importer
- M23/M26 — Auto-promote & `COUNT(DISTINCT)`

**Fase 3 — MINOR & housekeeping**
- Perbaiki syntax error `bottom-sheet`, tombol edit item-price, filter period
- Bersihkan dead code & relasi model, singkronkan ERD
- Konsistensi item code, design-system, pagination

---

## 6. Catatan

- Dokumen ini adalah **baseline audit**. Setelah setiap perbaikan dieksekusi, laporan ini harus diperbarui (centang item yang sudah selesai).
- Banyak temuan MAJOR bersumber dari **ERD yang tidak sinkron dengan implementasi** (fitur diimplementasikan ulang tanpa memperbarui dokumen). Disarankan audit dokumen `erd.md`, `item-code.md`, `security.md` setelah perbaikan kode.