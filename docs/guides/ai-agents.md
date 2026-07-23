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

### Dokumentasi Teknis Framework & Library

| No | File | Keterangan |
|----|------|-----------|
| 6 | `/docs/technical/laravel-blade.md` | Blade template, component, directive, Vite integration |
| 7 | `/docs/technical/breeze.md` | Auth scaffolding, routes, middleware, 2FA |
| 8 | `/docs/technical/spatie-permission.md` | Role & permission, seeder, middleware, blade directive |
| 9 | `/docs/technical/import-export.md` | Template import, export laporan, BaseExport styling |
| 11 | `/docs/technical/maatwebsite-excel.md` | Export/import Excel, styling, queue |
| 12 | `/docs/technical/qr-code.md` | Generate QR Code (SVG/PNG), logo, error correction |
| 13 | `/docs/technical/html5-qrcode.md` | Scan QR via kamera browser |
| 14 | `/docs/technical/mail-smtp.md` | SMTP Mail, Mailable, queue, attachment |

## Workflow AI WAJIB

Sebelum mengerjakan **task apa pun**, AI WAJIB mengikuti urutan ini:

1. **Baca docs/** — Semua file relevan di `docs/project/*`, `docs/technical/*`, `docs/guides/*`
2. **Cek kode existing** — Model, Controller, Service, Routes yang sudah ada
3. **Cek dokumentasi online** — Framework/package terkait (laravel.com, docs.laravel-excel.com, spatie.be, dll)
4. **Kerjakan** — Jika sudah jelas dari langkah 1-3, kerjakan dengan Laravel 13 + Blade best practices
5. **Buat baru** — Jika tidak ditemukan di dokumentasi manapun, buat solusi sendiri dengan best practices

## Aturan Kode

- Gunakan **Laravel 13** style (PHP 8 attributes, Enums, typed properties)
- Blade views menggunakan **Tailwind CSS**
- Semua logic bisnis di **Service Layer** (bukan di Controller)
- Setiap perubahan data tercatat di **Audit Log**
- Migration harus **idempotent** (bisa dijalankan ulang tanpa error)
- Seeder harus bisa dijalankan berulang (gunakan `firstOrCreate`)
- Gunakan **Spatie Permission** untuk role-based access control
- Format kode barang: **KATEGORI-GENDER-TIPE-VARIANT-SIZE** (contoh: `UNF-L-SCB-02-03`)
- Password harus di-hash dengan **bcrypt**
- Gunakan **Form Request** untuk validasi input
- Gunakan **Resource** untuk JSON response
- Gunakan **Route Model Binding** jika memungkinkan

## Role Definitions

| Role | Permissions | Keterangan |
|------|-------------|-----------|
| `super_admin` | Semua permission | Akses penuh ke seluruh sistem |
| `finance` | `manage-finance`, `manage-distributions` | Import data, entitlement, stock receive, stock opname, GPM, report |
| `staff` | `manage-students` | Scan QR, distribusi barang, validasi stok |
| `student` | (tanpa permission) | Login, input ukuran, lihat jadwal |

## Database Tables (Lengkap)

### Master Data
- `users` — Akun pengguna
- `faculties` — Fakultas
- `study_programs` — Program Studi
- `student_generations` — Generasi
- `students` — Mahasiswa
- `item_categories` — Kategori Barang
- `items` — Barang
- `item_variants` — Varian Ukuran Barang
- `item_prices` — Harga Barang Per Periode
- `vendors` — Vendor/Supplier

### Student Process
- `distribution_periods` — Periode Distribusi
- `eligibility_records` — Status Kelayakan
- `student_size_profiles` — Profil Ukuran Mahasiswa
- `student_size_items` — Ukuran Per Item
- `student_size_histories` — Riwayat Perubahan Ukuran

### Distribution
- `distribution_stages` — Tahap Distribusi
- `entitlements` — Hak Barang
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
- `stock_opnames` — Batch Stock Opname
- `stock_opname_items` — Detail Stock Opname
- `stock_opname_adjustments` — Adjustment Journal

### Supporting
- `import_batches` — Log Import
- `email_notifications` — Notifikasi Email
- `audit_logs` — Audit Log

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
