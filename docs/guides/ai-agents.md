# AGENTS.md — Pedoman untuk AI Assistant

## Dokumentasi yang WAJIB Dibaca

Sebelum mengerjakan task apa pun di project ini, AI **WAJIB** membaca dokumentasi berikut:

### Dokumentasi Project

| No | File | Keterangan |
|----|------|-----------|
| 1 | `/docs/project/overview.md` | Gambaran umum, tujuan, scope MVP, fitur per role |
| 2 | `/docs/project/prd.md` | Product Requirements Document |
| 3 | `/docs/project/erd.md` | ERD lengkap + detail kolom semua tabel |
| 4 | `/docs/project/flowchart.md` | Flowchart lengkap semua role |
| 5 | `/docs/project/architecture.md` | Arsitektur sistem, service layer, tech stack |
| 6 | `/docs/project/security.md` | Security design, autentikasi, otorisasi, audit |
| 7 | `/docs/project/item-code.md` | Item code system |
| 8 | `/docs/project/testing.md` | Skenario testing |

### Dokumentasi Teknis Framework & Library

| No | File | Keterangan |
|----|------|-----------|
| 9 | `/docs/technical/laravel-blade.md` | Blade template, component, directive, Vite integration |
| 10 | `/docs/technical/breeze.md` | Auth scaffolding, routes, middleware, OTP |
| 11 | `/docs/technical/spatie-permission.md` | Role & permission, seeder, middleware, blade directive |
| 12 | `/docs/technical/import-export.md` | Template import, export laporan, BaseExport styling |
| 13 | `/docs/technical/maatwebsite-excel.md` | Export/import Excel (ToCollection + heading row) |
| 14 | `/docs/technical/qr-code.md` | Generate QR Code (bacon/bacon-qr-code, QR = NIM) |
| 15 | `/docs/technical/html5-qrcode.md` | Scan QR via kamera browser |
| 16 | `/docs/technical/mail-smtp.md` | SMTP Mail, Mailable, queue, attachment, dynamic SMTP |
| 17 | `/docs/technical/vite.md` | Vite + laravel-vite-plugin, kompilasi asset |
| 18 | `/docs/technical/alpinejs.md` | Alpine.js, komponen interaktif |
| 19 | `/docs/technical/tailwindcss.md` | Tailwind CSS, warna brand maroon |

## Workflow AI WAJIB

Sebelum mengerjakan **task apa pun**, AI WAJIB mengikuti urutan ini:

1. **Baca docs/** — Semua file relevan di `docs/project/*`, `docs/technical/*`, `docs/guides/*`
2. **Cek kode existing** — Model, Controller, Service, Routes yang sudah ada
3. **Cek dokumentasi online** — Framework/package terkait (laravel.com, docs.laravel-excel.com, spatie.be, dll)
4. **Kerjakan** — Jika sudah jelas dari langkah 1-3, kerjakan dengan Laravel 13 + Blade best practices
5. **Buat baru** — Jika tidak ditemukan di dokumentasi manapun, buat solusi sendiri dengan best practices

## Aturan Kode

- Gunakan **Laravel 13** style (PHP 8 attributes, Enums, typed properties)
- Blade views menggunakan **Tailwind CSS** (warna brand: `primary-700` = `#980416` maroon)
- Semua logic bisnis di **Service Layer** (bukan di Controller)
- Migration harus **idempotent** (bisa dijalankan ulang tanpa error)
- Seeder harus bisa dijalankan berulang (gunakan `firstOrCreate`)
- Gunakan **Spatie Permission** untuk role-based access control
- Format kode barang: **KATEGORI-GENDER-TIPE-VARIANT** (contoh: `UNF-L-SCB-02`); SKU varian = `code-SIZE`
- Password harus di-hash dengan **bcrypt**
- Gunakan **Form Request** untuk validasi input
- Gunakan **Resource** untuk JSON response
- Gunakan **Route Model Binding** jika memungkinkan

## Role Definitions

| Role | Permissions | Keterangan |
|------|-------------|-----------|
| `super_admin` | Semua permission | Akses penuh ke seluruh sistem + SMTP settings + user management |
| `admin` | `manage-finance`, `manage-distributions` | Import data, entitlement, stock receive, stock opname, GPM, report |
| `staff` | `manage-students` | Scan QR, distribusi barang, validasi stok |
| `student` | (tanpa permission) | Login, input ukuran, lihat jadwal |

## Database Tables (Lengkap)

### Master Data
- `users` — Akun pengguna
- `faculties` — Fakultas
- `study_programs` — Program Studi
- `student_generations` — Generasi
- `student_levels` — Level mahasiswa (`Y1S1` … `graduated`, read-only)
- `students` — Mahasiswa
- `item_categories` — Kategori Barang
- `item_types` — Tipe Barang
- `item_departments` — Departemen Barang
- `item_sizes` — Ukuran Barang
- `category_item_size` — Pivot kategori–ukuran
- `items` — Barang (code = base_code 4 segmen)
- `item_variants` — Varian Ukuran Barang (sku = code-SIZE)
- `item_prices` — Harga Barang Per Periode
- `vendors` — Vendor/Supplier

### Student Process
- `eligibility_records` — Status Kelayakan
- `student_size_profiles` — Profil Ukuran Mahasiswa
- `student_size_items` — Ukuran Per Item
- `student_size_histories` — Riwayat Perubahan Ukuran
- `size_change_events` — Event Perubahan Ukuran
- `size_event_submissions` — Submit Mahasiswa ke Event
- `student_summaries` — Ringkasan statistik mahasiswa (harian)

### Distribution
- `entitlements` — Hak Barang (per student level)
- `entitlement_items` — Detail Hak Barang
- `distribution_schedules` — Jadwal Distribusi
- `dist_schedule_items` — Item Jadwal
- `distribution_transactions` — Transaksi Distribusi
- `distribution_items` — Detail Transaksi

### Inventory
- `stock_receives` — Penerimaan Barang
- `stock_receive_items` — Detail Penerimaan
- `stock_movements` — Pergerakan Stok (IN/OUT)
- `stock_balances` — Saldo Stok
- `stock_batches` — Batch stok FIFO
- `stock_opnames` — Batch Stock Opname
- `stock_opname_items` — Detail Stock Opname
- `stock_opname_adjustments` — Adjustment Journal

### Supporting
- `import_batches` — Log Import
- `email_notifications` — Notifikasi Email
- `otp_codes` — Kode OTP (hash)
- `smtp_settings` — Setting SMTP (dinamis, dari UI)
- `document_sequences` — Sequence nomor dokumen

## Prioritas Pengerjaan

1. **Database & Migration** — Buat semua tabel sesuai ERD
2. **Model & Relationship** — Buat Eloquent Model dengan relationship
3. **Import Service** — Import Excel mahasiswa, eligible, items
4. **Master Data CRUD** — Fakultas, Prodi, Level, Item, Size
5. **Student Flow** — Login, input ukuran, QR
6. **Staff Flow** — Scan QR, distribusi, stock OUT
7. **Stock Opname** — Batch opname, variance, adjustment
8. **GPM / Cost** — HPP tracking, harga jual, laporan
9. **Report** — Export distribusi, inventory, GPM
10. **Testing** — Semua skenario di PRD