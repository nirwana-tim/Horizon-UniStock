# Sistem Arsitektur

## Laravel Architecture

```
app
├── Models
├── Http
│   └── Controllers
│   └── Requests
├── Services
├── Imports
├── Exports

database
├── migrations
├── seeders

resources
├── views
│   ├── auth/                  # Login, register, forgot password
│   ├── components/            # Reusable Blade components
│   ├── dashboards/            # Dashboard per role
│   ├── distribution/          # Entitlement, jadwal, size monitor, scan
│   ├── import/                # Import data
│   ├── inventory/             # Stock receive, stock opname
│   ├── layouts/               # App layout, navigation
│   ├── master/                # Master data (faculty, prodi, level, item, vendor)
│   ├── report/                # Laporan & GPM
│   ├── student/               # Student self-service (size input, QR)
│   └── profile/               # User profile (Breeze)

routes
├── web.php
```

## Service Layer

| Service | Fungsi |
|---------|--------|
| `DistributionService` | Proses distribusi, validasi stok, submit transaksi |
| `EntitlementService` | Kelola hak barang, validasi eligibility |
| `StockService` | Stock receive (IN), distribution (OUT), balance |
| `ImportService` | Import Excel mahasiswa, eligible, item, stock |
| `ReportService` | Generate report distribusi & inventory |
| `QrCodeService` | Generate QR token, validasi scan |

## Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 12 |
| Database | MySQL |
| Frontend | Blade + Tailwind CSS + Vite |
| Auth | Laravel Breeze |
| Permission | Spatie Laravel Permission |
| Excel | Maatwebsite Laravel Excel |
| QR Code | Simple QR Code (SVG/PNG) |
| QR Scanner | HTML5 QR Scanner |
| Email | Laravel Mail + SMTP |

## Route Structure

| Route Prefix | Name Prefix | Controller Namespace | Middleware |
|---|---|---|---|
| `/master-data` | `master-data.*` | `Master\*` | `role:super_admin\|admin` |
| `/student` | `students.*` | `Master\StudentController` | `role:super_admin\|admin` |
| `/distribution` | `distribution.*` | `Master\EntitlementController`, etc. | `role:super_admin\|admin` |
| `/inventory` | `inventory.*` | `Finance\StockOpnameController`, `Master\StockReceiveController` | `role:super_admin\|admin` |
| `/report` | `report.*` | `ReportController`, `Finance\GpmController` | `role:super_admin\|admin` |
| `/import` | `import.*` | `ImportController` | `role:super_admin\|admin` |
| `/student` (self) | `student.*` | `Student\SizeController` | `role:student` |

## View Folder Structure

```
resources/views
├── auth/                  # Login, register, forgot password (Breeze)
├── components/            # Reusable: primary-button, danger-button, secondary-button, sidebar, etc.
├── dashboards/            # super-admin, finance, staff, student
├── distribution/          # entitlement/, distribution-schedule/, size-monitor/, scan
├── import/                # index, preview, result
├── inventory/             # stock-receive/, stock-opname/
├── layouts/               # app.blade.php, navigation
├── master/                # faculty/, study-program/, student-generation/, item/, vendor/, etc.
├── report/                # index, gpm-cost
├── student/               # size-input, qr-show (student self-service)
└── profile/               # Breeze profile
```

## Aturan Kode

- Gunakan **Laravel 12** style (PHP 8 attributes, Enums, typed properties)
- Blade views menggunakan **Tailwind CSS**
- Semua logic bisnis di **Service Layer** (bukan di Controller)
- Setiap perubahan data tercatat di **Audit Log**
- Migration harus **idempotent**
- Seeder harus bisa dijalankan berulang (`firstOrCreate`)
- Gunakan **Spatie Permission** untuk role-based access control
- Password harus di-hash dengan **bcrypt**
- Gunakan **Form Request** untuk validasi input
- Gunakan **Resource** untuk JSON response
- Gunakan **Route Model Binding** jika memungkinkan
