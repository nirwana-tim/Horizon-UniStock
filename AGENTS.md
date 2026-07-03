# AGENTS.md — Pedoman untuk AI Assistant

> **Full version available at:** [`docs/guides/ai-agents.md`](docs/guides/ai-agents.md)

## Workflow AI WAJIB

Sebelum mengerjakan **task apa pun**, AI WAJIB mengikuti urutan ini:

1. **Baca docs/** — Semua file relevan di `docs/project/*`, `docs/technical/*`, `docs/guides/*`
2. **Cek kode existing** — Model, Controller, Service, Routes yang sudah ada
3. **Cek dokumentasi online** — Framework/package terkait (laravel.com, docs.laravel-excel.com, spatie.be, dll)
4. **Kerjakan** — Jika sudah jelas dari langkah 1-3, kerjakan dengan Laravel 13 + Blade best practices
5. **Buat baru** — Jika tidak ditemukan di dokumentasi manapun, buat solusi sendiri dengan best practices

## Dokumentasi yang WAJIB Dibaca

| # | File | Keterangan |
|---|------|-----------|
| 1 | `docs/project/overview.md` | Gambaran umum, tujuan, scope MVP, fitur per role |
| 2 | `docs/project/prd.md` | Product Requirements Document |
| 3 | `docs/project/erd.md` | ERD + detail kolom semua tabel |
| 4 | `docs/project/flowchart.md` | Flowchart lengkap semua role |
| 5 | `docs/project/architecture.md` | Arsitektur, service layer, tech stack |
| 6 | `docs/project/security.md` | Security design |
| 7 | `docs/project/item-code.md` | Item code system |
| 8 | `docs/technical/import-export.md` | Template import, export laporan, BaseExport styling |
| 9 | `docs/technical/laravel-blade.md` | Blade template, component, Vite |
| 10 | `docs/technical/breeze.md` | Auth scaffolding |
| 11 | `docs/technical/spatie-permission.md` | Role & permission |
| 12 | `docs/technical/maatwebsite-excel.md` | Export/import Excel |
| 13 | `docs/technical/qr-code.md` | Generate QR Code |
| 14 | `docs/technical/html5-qrcode.md` | Scan QR via kamera |
| 15 | `docs/technical/mail-smtp.md` | SMTP Mail |

## Aturan Kode

- **Laravel 13** style (PHP 8 attributes, Enums, typed properties)
- Logic bisnis di **Service Layer**, bukan Controller
- Setiap perubahan data tercatat di **Audit Log**
- Migration **idempotent**, Seeder pake `firstOrCreate`
- **Spatie Permission** untuk RBAC
- Format kode barang: `KATEGORI-GENDER-TIPE-VARIANT-SIZE` (contoh: `UNF-L-SCB-02-03`)
- Password **bcrypt**, validasi pake **Form Request**
- JSON response pake **Resource**
- **Route Model Binding** jika memungkinkan

## Role & Permission

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

1. Database & Migration → 2. Model & Relationship → 3. Import Service → 4. Master Data CRUD → 5. Student Flow → 6. Staff Flow → 7. Stock Opname → 8. GPM / Cost → 9. Report → 10. Testing
