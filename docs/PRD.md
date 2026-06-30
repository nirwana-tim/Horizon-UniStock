# Product Requirements Document (PRD)
# Horizon-UniStock — Sistem Distribusi Seragam & Inventory Finance

---

## 1. Executive Summary

Project ini merupakan pengembangan sistem berbasis web untuk membantu Finance dalam mengelola proses distribusi seragam mahasiswa. Sistem dibuat untuk menggantikan proses manual yang sebelumnya menggunakan Google Form, Google Sheet, barcode manual, checklist manual, dan rekap Excel.

Permasalahan utama dari sistem lama adalah: data tersebar di banyak file, sulit tracking siapa menerima barang apa, risiko double submit, risiko salah ukuran, proses hari-H lambat, report membutuhkan rekap manual, dan data stok belum terhubung dengan distribusi.

Solusi yang dirancang adalah sistem terintegrasi yang mencakup: Student Data → Size Management → QR Identity → Staff Distribution → Inventory Movement → Finance Report. MVP difokuskan pada distribusi Freshman dengan target implementasi 20 Juli 2026.

---

## 2. Business Objectives

### 2.1 Tujuan Bisnis

1. Mempercepat proses distribusi seragam mahasiswa baru (target < 5 menit per mahasiswa, sebelumnya > 15 menit)
2. Menghilangkan kesalahan manual (double submit, salah ukuran, data hilang)
3. Tracking barang yang diberikan ke mahasiswa secara real-time
4. Data distribusi tersimpan terstruktur dan bisa di-export
5. Fondasi inventory management yang bisa dikembangkan ke sistem penuh

### 2.2 Target Implementasi

- MVP: **20 Juli 2026**
- Fokus: **Freshman / Mahasiswa Baru**

---

## 3. Stakeholders

| Stakeholder | Peran | Kebutuhan Utama |
|-------------|-------|-----------------|
| Finance Admin | Pengguna utama sistem | Import data, atur entitlement, buat jadwal, export report, stock opname, GPM |
| Staff Distribusi | Operator lapangan | Scan QR, distribusi barang, validasi stok, partial pickup |
| Mahasiswa | Penerima barang | Input ukuran, lihat jadwal, lihat QR, lupa password |
| Super Admin | IT/Monitoring | Kelola user, monitoring, backup, audit log |

---

## 4. User Personas

### 4.1 Finance Admin (Budi)

- **Role**: Finance Admin
- **Kebutuhan**: Import data ribuan mahasiswa dari Excel, atur hak barang per prodi, buat jadwal distribusi, monitor stok, lakukan stock opname bulanan, hitung GPM
- **Pain Point**: Proses manual di Excel memakan waktu berjam-jam, sulit tracking perubahan ukuran

### 4.2 Staff Distribusi (Sari)

- **Role**: Staff
- **Kebutuhan**: Scan QR mahasiswa, checklist barang, submit distribusi, validasi stok
- **Pain Point**: QR sering gagal, harus cari manual, tidak ada tracking real-time

### 4.3 Mahasiswa (Andi)

- **Role**: Student
- **Kebutuhan**: Login, input ukuran, lihat jadwal ambil seragam, lupa password
- **Pain Point**: Tidak tahu kapan harus ambil seragam, ukuran salah

### 4.4 Super Admin (IT)

- **Role**: Super Admin
- **Kebutuhan**: Kelola user & role, monitoring semua modul, backup database
- **Pain Point**: Sulit monitoring jika semua manual

---

## 5. Functional Requirements

### 5.1 Role: Finance Admin

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-01 | Import data mahasiswa | Upload Excel → Validasi → Preview → Commit → Import Log | High |
| FR-02 | Import eligible payment | Upload data pembayaran mahasiswa | High |
| FR-03 | Kelola master data | Fakultas, Prodi, Level, Item, Kategori, Size, Variant, Vendor | High |
| FR-04 | Kelola Distribution Stages | Buat tahap distribusi (Tahap 1, 2, 3...) dengan nama, periode, tanggal, item | High |
| FR-05 | Create Entitlement | Atur hak barang: Prodi + Level + Period + Student Type + Item + Qty | High |
| FR-06 | Generate akun mahasiswa | Username=NIM, Password=random 12 char, kirim ke email pribadi | High |
| FR-07 | Input email kampus | Isi email @krw.horizon.ac.id untuk setiap mahasiswa | High |
| FR-08 | Stock Receive | Input barang masuk dari vendor → Stock IN → Balance + | High |
| FR-09 | Buat Jadwal Distribusi | Pilih stage → Lihat stok ready → Pilih item → Isi lokasi & jadwal → Notifikasi | High |
| FR-10 | Monitor perubahan ukuran | Lihat log: siapa, dari/ke ukuran, staff, tanggal | Medium |
| FR-11 | **Stock Opname Bulanan** | Upload hasil opname → Hitung variance → Buat adjustment journal | High |
| FR-12 | **GPM / Cost Analysis** | Lihat laba/rugi per item, HPP vs harga jual | Medium |
| FR-13 | Export Distribution Report | Export ke Excel: sudah ambil, belum ambil, partial, detail item | High |
| FR-14 | Export Stock/Inventory Report | Export ke Excel: stock balance, movement history | High |
| FR-15 | **Export GPM Report** | Export laporan GPM ke Excel | Medium |

### 5.2 Role: Staff

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-16 | Login staff | Email + password | High |
| FR-17 | Scan QR mahasiswa | QR permanen, 1x seumur hidup | High |
| FR-18 | Cari NIM manual | Fallback jika QR gagal | High |
| FR-19 | Lihat data mahasiswa | Profile + entitlement per tahap + ukuran | High |
| FR-20 | Checklist item | Centang barang tahap yang aktif | High |
| FR-21 | Edit actual size | Jika berbeda dari expected — tercatat log (staff, old, new) | High |
| FR-22 | Validasi stok | Cek ketersediaan stok per size sebelum submit | High |
| FR-23 | Partial pickup | Jika stok kurang, bisa kasih sebagian | High |
| FR-24 | Submit transaksi | Simpan → Stock OUT → Balance - | High |

### 5.3 Role: Mahasiswa

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-25 | Login | Username=NIM, Password dari Finance | High |
| FR-26 | Ganti password wajib | First login harus ganti password | High |
| FR-27 | Dashboard | Info email kampus, notifikasi jadwal, status ambil, riwayat | High |
| FR-28 | Input/update profil & ukuran | Seragam & sepatu, lihat size chart vendor | High |
| FR-29 | Lihat size chart vendor | Referensi ukuran dari vendor | Medium |
| FR-30 | Update ukuran | Maksimal 1x perubahan | High |
| FR-31 | Generate QR | Setelah data lengkap, QR 1x generate seumur hidup | High |
| FR-32 | Lihat jadwal distribusi | Jadwal per tahap (Tahap 1, 2, 3) | High |
| FR-33 | Lupa password | Input NIM → OTP 6 digit ke email kampus → Ganti password | High |

### 5.4 Role: Super Admin

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-34 | Kelola user, role, permission | CRUD user, atur role & permission (Spatie) | High |
| FR-35 | System config & maintenance mode | Setting global, maintenance mode | Medium |
| FR-36 | Audit log | Filter, export log aktivitas | Medium |
| FR-37 | Backup & restore database | Download / restore | Medium |
| FR-38 | Monitoring semua modul | Pantau dari satu dashboard | Low |

### 5.5 Stock Opname

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-39 | Buat batch opname | Tanggal, periode, notes, status (draft/completed) | High |
| FR-40 | Upload hasil opname | Upload Excel hasil hitung fisik | High |
| FR-41 | Hitung variance otomatis | System = stok sistem, Physical = stok fisik, Variance = selisih | High |
| FR-42 | Buat adjustment journal | Jurnal penyesuaian untuk selisih (surplus/shortage) | High |
| FR-43 | Riwayat opname per item | Lihat riwayat opname bulanan per item | Medium |
| FR-44 | Export hasil opname | Export ke Excel | Medium |

### 5.6 GPM / Cost Analysis

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-45 | Tracking HPP per batch | Harga Pokok Pembelian per batch penerimaan | Medium |
| FR-46 | Set harga jual per item | Harga jual per item | Medium |
| FR-47 | Laporan GPM otomatis | (Harga Jual - HPP) × Qty Terjual | Medium |
| FR-48 | Laporan laba/rugi | Per item, kategori, periode | Medium |
| FR-49 | Export laporan GPM | Export ke Excel | Medium |

---

## 6. Non-Functional Requirements

| ID | Requirement | Target |
|----|-------------|--------|
| NFR-01 | Waktu response halaman | < 3 detik |
| NFR-02 | Concurrent users saat hari-H | 50+ staff |
| NFR-03 | QR scan response | < 2 detik |
| NFR-04 | Sistem availability saat jam distribusi | 99% (07.00-17.00) |
| NFR-05 | Audit logging | Semua transaksi tercatat |
| NFR-06 | Data stok real-time | Update < 5 detik setelah transaksi |
| NFR-07 | Password hashing | bcrypt |
| NFR-08 | Access control | Role-based (Spatie Permission) |
| NFR-09 | Backup frequency | Daily automated backup |

---

## 7. Data Requirements

### 7.1 Entity List

**Master Data:**
- Users, Faculties, Study Programs, Program Levels
- Students, Student Size Profiles, Student Size Items, Student Size Histories
- Item Categories, Items, Item Variants, Vendors

**Distribution:**
- Distribution Periods, Distribution Stages
- Eligibility Records
- Entitlements, Entitlement Items
- Distribution Schedules, Dist Schedule Items
- Distribution Transactions, Distribution Items

**Inventory:**
- Stock Receives, Stock Receive Items
- Stock Movements, Stock Balances
- **Stock Opnames, Stock Opname Items, Stock Opname Adjustments** (NEW)

**Supporting:**
- Import Batches
- Email Notifications
- Audit Logs

### 7.2 Item Code System

**Format:** `KATEGORI-GENDER-TIPE-NOMOR`

| Komponen | Kode | Arti |
|----------|------|------|
| **KATEGORI** | UNF | Uniform |
| | SHO | Shoes |
| | KTM | Kartu Mahasiswa |
| | KIT | Kit (Nursing/Midwifery) |
| **GENDER** | L | Laki-laki |
| | P | Perempuan |
| | U | Unisex |
| **TIPE** | SCB | Scrub Suit |
| | CLC | Clinical Uniform |
| | ALM | Almamater |
| | CLG | College Uniform |
| | COM | Community Uniform |
| | LAB | Laboratory Gown |
| | YDH | Lanyard & Holder |
| | NUR | Nursing Kit |
| | MID | Midwifery Kit |
| **NOMOR** | 01, 02, 03... | Variasi ukuran |

**Contoh:**
- `UNF-L-SCB-02-03` = Uniform Scrub Laki-Laki STIK ukuran 03
- `SHO-P-CLC-02-41` = Shoes Clinical Perempuan STIKES ukuran 41
- `KTM-U-KTM-01-01` = KTM Kartu Mahasiswa Unisex
- `KIT-U-NUR` = Kit Nursing Unisex

---

## 8. System Architecture

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 13 |
| Database | MySQL |
| Frontend | Blade + Tailwind CSS + Vite |
| Auth | Laravel Breeze |
| Permission | Spatie Laravel Permission |
| Excel | Maatwebsite Laravel Excel |
| QR Code | Simple QR Code (SVG/PNG) |
| QR Scanner | HTML5 QR Scanner |
| Email | Laravel Mail + SMTP |

---

## 9. Assumptions & Constraints

1. Data mahasiswa sudah ada di Excel (siap import)
2. Setiap mahasiswa memiliki email kampus @krw.horizon.ac.id
3. Distribusi dilakukan dalam tahapan (stage) yang sudah ditentukan Finance
4. Stock opname dilakukan bulanan oleh Finance
5. HPP dihitung per batch penerimaan (rata-rata)
6. QR code bersifat permanen (1x generate seumur hidup)
7. Update ukuran mahasiswa maksimal 1x

---

## 10. Risks & Mitigation

| Risiko | Dampak | Mitigation |
|--------|--------|------------|
| Data Excel tidak konsisten | Import gagal | Import validation + preview + error log per baris |
| QR gagal saat hari-H | Distribusi terhambat | Manual search NIM (fallback) + backup Excel |
| Sistem lambat saat hari-H | Antrian panjang | Database backup + caching + fallback procedure |
| Stock opname selisih besar | Rugi/rugi tidak tercatat | Adjustment journal dengan approval |
| Double submit | Data duplikat | Sistem tolak transaksi sudah ada |
| Stok kurang saat distribusi | Mahasiswa tidak kebagian | Partial pickup + notifikasi ke Finance |

---

## 11. Success Metrics

| Metrik | Target | Cara Ukur |
|--------|--------|-----------|
| Waktu distribusi per mahasiswa | < 5 menit | Log timestamp transaksi |
| Double submit | 0% | Audit log |
| Tracking barang ke mahasiswa | 100% | Distribution items table |
| Stock opname variance | < 2% | Stock opname report |
| Laporan GPM tersedia | Real-time | GPM dashboard |
| User satisfaction | > 4/5 | Survey setelah hari-H |

---

## 12. Out of Scope (Fase Lanjutan)

Tidak termasuk dalam MVP:

- Continuing Student full system
- POS eceran (pembelian tambahan)
- FIFO / VIVO cost method
- Cost accounting penuh
- Revenue dashboard
- Advanced Stock Opname (multi-lokasi, cycle counting)
- Advanced Cost Analytics (FIFO/VIVO)
- Email automation penuh (auto-send, auto-reminder)
- Integrasi SIS (Sistem Informasi Student)
- Mobile app native
- Multi warehouse
- Dashboard Finance real-time

---

## 13. Timeline Development

| Minggu | Fokus | Deliverable |
|--------|-------|-------------|
| Minggu 1 | Setup | Laravel, Database, Auth (Breeze), Role (Spatie) |
| Minggu 2 | Master Data | Model, Migration, Import Excel (Mahasiswa, Eligible, Items) |
| Minggu 3 | Core | Input ukuran, QR, Entitlement, Stock Receive |
| Minggu 4 | Distribution | Staff scan, Checklist, Stock OUT, Stock Opname |
| Minggu 5 | Report | Distribution Report, Inventory Report, GPM Report |
| Minggu 6 | Final | Testing semua skenario, Deployment, UAT |

---

## 14. Testing Scenarios

| No | Skenario | Expected Result |
|----|----------|-----------------|
| 1 | Import duplicate NIM | Error, tidak boleh duplikat |
| 2 | QR tidak valid | Tampilkan error, izinkan cari NIM |
| 3 | Mahasiswa tidak eligible | Pengambilan ditolak |
| 4 | Double submit (staf yang sama) | Sistem tolak, transaksi sudah ada |
| 5 | Actual size berbeda dari expected | Simpan actual size, log perubahan |
| 6 | Stok kurang dari yang diminta | Tawarkan partial pickup |
| 7 | Partial pickup dipilih | Simpan qty sebagian, update stok sebagian |
| 8 | Export report distribusi | File Excel berisi semua data distribusi |
| 9 | Lupa password | OTP terkirim, validasi, reset berhasil |
| 10 | Import Excel format salah | Error handling, tampilkan pesan jelas |
| 11 | Update ukuran kedua kalinya | Tolak, maksimal 1 kali |
| 12 | Jadwal distribusi - email duplikat | Hanya dikirim 1x per mahasiswa per jadwal |
| 13 | **Stock opname - variance positif** | Surplus tercatat, adjustment journal dibuat |
| 14 | **Stock opname - variance negatif** | Shortage tercatat, adjustment journal dibuat |
| 15 | **GPM calculation** | (Harga Jual - HPP) × Qty Terjual = Laba/Rugi |
| 16 | **Import stock opname** | Variance dihitung otomatis per item |
