# AGENTS.md — Pedoman untuk AI Assistant

## Dokumentasi yang WAJIB Dibaca

Sebelum mengerjakan task apa pun di project ini, AI **WAJIB** membaca dokumentasi berikut:

### Dokumentasi Project

| No | File | Keterangan |
|----|------|-----------|
| 1 | `/SEMENTARA_HORIZON_README.md` | Dokumentasi utama sistem — tujuan, scope, database design (ERD), flowchart, arsitektur, testing, timeline |
| 2 | `/docs/PRD.md` | Product Requirements Document — semua fitur, requirement per role, non-functional requirements |

### Dokumentasi Teknis Framework & Library

| No | File | Keterangan |
|----|------|-----------|
| 3 | `/docs/laravel-13-blade.md` | Blade template, component, directive, Vite integration |
| 4 | `/docs/breeze.md` | Auth scaffolding, routes, middleware, 2FA |
| 5 | `/docs/spatie-permission.md` | Role & permission, seeder, middleware, blade directive |
| 6 | `/docs/maatwebsite-excel.md` | Export/import Excel, styling, queue |
| 7 | `/docs/qr-code.md` | Generate QR Code (SVG/PNG), logo, error correction |
| 8 | `/docs/html5-qrcode.md` | Scan QR via kamera browser |
| 9 | `/docs/mail-smtp.md` | SMTP Mail, Mailable, queue, attachment |

## Workflow AI

1. **Baca kedua dokumen project** (SEMENTARA_HORIZON_README.md + docs/PRD.md) untuk memahami konteks
2. **Baca dokumentasi teknis** yang relevan dengan task yang akan dikerjakan
3. **Ikuti aturan kode** di bawah ini
4. **Eksekusi task** sesuai urutan yang sudah ditentukan
5. **Verifikasi** hasil kerja sebelum menyerahkan

## Aturan Kode

- Gunakan **Laravel 13** style (PHP 8 attributes, Enums, typed properties)
- Blade views menggunakan **Tailwind CSS**
- Semua logic bisnis di **Service Layer** (bukan di Controller)
- Setiap perubahan data tercatat di **Audit Log**
- Migration harus **idempotent** (bisa dijalankan ulang tanpa error)
- Seeder harus bisa dijalankan berulang (gunakan `firstOrCreate`)
- Gunakan **Spatie Permission** untuk role-based access control
- Format kode barang: **KATEGORI-GENDER-TIPE-NOMOR** (contoh: `UNF-L-SCB-02-03`)
- Password harus di-hash dengan **bcrypt**
- Gunakan **Form Request** untuk validasi input
- Gunakan **Resource** untuk JSON response
- Gunakan **Route Model Binding** jika memungkinkan

## Role Definitions

| Role | Permissions | Keterangan |
|------|-------------|-----------|
| `super_admin` | Semua permission | Akses penuh ke seluruh sistem |
| `admin` | `manage-finance`, `manage-distributions` | Import data, entitlement, stock receive, stock opname, GPM, report |
| `staff` | `manage-students` | Scan QR, distribusi barang, validasi stok |
| `student` | (tanpa permission) | Login, input ukuran, lihat jadwal |

## Database Tables (Lengkap)

### Master Data
- `users` — Akun pengguna
- `faculties` — Fakultas
- `study_programs` — Program Studi
- `program_levels` — Level/Angkatan
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
