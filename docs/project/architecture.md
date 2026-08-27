# Sistem Arsitektur

## Laravel Architecture

```
app
├── Models                  # ±40 model (Eloquent)
├── Http
│   ├── Controllers         # Master\, Finance\, Inventory\, Staff\, Student\, System\, Profile\, Auth\
│   │                       # + DashboardController, ReportController, ImportController, TemplateController
│   └── Requests            # ±19 Form Request (validasi)
├── Services                # ±28 service (logika bisnis) — Master\, Finance\, System + root
├── Imports                 # Importer Excel (ToCollection + WithHeadingRow)
├── Exports                 # Exporter Excel + template download (BaseExport styling)
├── Console/Commands        # AutoPromoteStudents, CalculateStudentSummaries, CleanupOldLogs

database
├── migrations/             # 55 migrasi bersih (1 tabel = 1 file), portabel MySQL & PostgreSQL
├── seeders                 # DatabaseSeeder, RolePermissionSeeder, StudentLevelSeeder,
│                           # SuperadminSeeder, FakeDataSeeder, TestDistributionSeeder, UserTestSeeder

resources
├── views
│   ├── auth/               # Login, forgot password, reset password
│   ├── components/         # Reusable Blade components (28)
│   ├── dashboards/         # Dashboard per role (super-admin, admin, staff, student)
│   ├── distribution/       # Entitlement, jadwal, size monitor, size events, scan
│   ├── emails/             # Template email notifikasi
│   ├── errors/             # Halaman error
│   ├── finance/            # Eligibility, GPM
│   ├── import/             # Import data
│   ├── inventory/          # Stock receive, stock balance, stock movement, stock opname
│   ├── layouts/            # App layout, sidebar, bottom-nav
│   ├── master/             # Master data (faculty, study-program, item, vendor, dll)
│   ├── profile/            # User profile (Breeze)
│   ├── report/             # Laporan & GPM
│   ├── student/            # Student self-service (sizes, QR, items)
│   └── system/             # SMTP settings, user management

routes
├── web.php                 # Semua route aplikasi (selain auth)
├── auth.php                # Route Breeze auth (login, forgot, reset)
└── console.php             # Scheduler (logs:cleanup, queue:prune-failed, summaries, auto-promote)
```

## Service Layer

| Service | Fungsi |
|---------|--------|
| `DistributionService` | Proses distribusi (validasi stok, anti double submit, stock OUT, balance) |
| `DistributionScheduleService` | Jadwal distribusi & scope jadwal per mahasiswa |
| `EntitlementService` | Kelola hak barang per student level |
| `StockService` | Stock receive (IN), distribution (OUT), balance |
| `StockOpnameService` | Upload & proses stock opname |
| `GpmService` | Perhitungan GPM / cost |
| `ImportService` | Import Excel (mahasiswa, item, entitlement, stock receive, stock opname, dll) |
| `ReportService` | Generate report distribusi & inventory |
| `QrCodeService` | Generate QR PNG berisi NIM (bacon/bacon-qr-code) |
| `StudentSizeService` | Profil ukuran mahasiswa & resolve size value |
| `NotificationService` | Notifikasi email |
| `MailSettingsService` | Kelola setting SMTP dari database |
| `Master\*Service` | Logika bisnis per master data (ItemService, StudentService, dll) |
| `Finance\EligibilityService` | Status kelayakan mahasiswa |

## Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 13 |
| Database | MySQL 8 / PostgreSQL 18 (migrasi portabel dual-driver) |
| Frontend | Blade + Tailwind CSS + Alpine.js + Vite |
| Auth | Laravel Breeze (login/password, register & 2FA nonaktif) |
| Permission | Spatie Laravel Permission (role-based, via middleware `role:`) |
| Excel | Maatwebsite Laravel Excel (`ToCollection`) |
| QR Code | `bacon/bacon-qr-code` (PNG data URL, berisi NIM) |
| QR Scanner | `html5-qrcode` (scan via kamera) |
| Chart | `chart.js` (dashboard) |
| Email | Laravel Mail + SMTP (setting dari DB) |
| Font | `@fontsource/inter` (Inter) |

## Route Structure

Semua route dilindungi `auth` + `password.changed` (kecuali `password/change` & dashboard sudah memakai auth). Authorization via middleware `role:`. Tidak ada middleware `permission:` di route (permission Spatie hanya untuk role assignments).

| Route Prefix | Name Prefix | Controller Namespace | Role |
|---|---|---|---|
| `/master-data` | `master-data.*` | `Master\*` | `super_admin\|admin` |
| `/distribution` | `distribution.*` | `Master\Entitlement|DistributionSchedule`, `Staff\Scan` | `super_admin\|admin` / `+staff` (scan) |
| `/inventory` | `inventory.*` | `Master\StockReceive`, `Inventory\StockBalance|StockMovement`, `Finance\StockOpname` | `super_admin\|admin` |
| `/report` | `report.*` | `ReportController`, `Finance\GpmController` | `super_admin\|admin` |
| `/templates/{type}/download` | `templates.download` | `TemplateController` | `super_admin\|admin` |
| `/import` | `import.*` | `ImportController` | `super_admin\|admin` |
| `/student` | `finance.eligibility.*` | `Finance\EligibilityController` | `super_admin\|admin` |
| `/student` | `students.*` | `Master\StudentController` | `super_admin\|admin` |
| `/student` (self) | `student.*` | `Student\SizeController`, `Auth\EmailVerificationOtpController` | `student` |
| `/system` | `system.*` | `System\SmtpSettingController` | `super_admin` |
| `/admin/users` | `admin.user.*` | `System\UserController` | `super_admin\|admin` |
| `/password/change` | `password.change*` | `Auth\PasswordChangeController` | `auth` |
| `/profile` | `profile.*` | `ProfileController`, `Profile\EmailController` | `auth` |

## View Folder Structure

```
resources/views
├── auth/                  # Login, forgot, reset (Breeze)
├── components/            # Reusable: alert, badge, stat-card, page-header, empty-state,
│                          # sidebar, bottom-nav, buttons, inputs, dll (28 komponen)
├── dashboards/            # super-admin, admin, staff, student
├── distribution/          # entitlement/, distribution-schedule/, size-monitor/, size-events/, scan
├── emails/                # Template email (notifikasi, kredensial, OTP)
├── errors/                # Halaman error
├── finance/               # eligibility/, gpm/
├── import/                # index, preview, result
├── inventory/             # stock-receive/, stock-balance/, stock-movement/, stock-opname/
├── layouts/               # app.blade.php + sidebar (admin) & bottom-nav (staff/student)
├── master/                # faculty/, study-program/, student-generation/, student-level/,
│                          # item/, item-category/, item-type/, item-department/, item-size/,
│                          # item-price/, vendor/, student/ (students-data, credentials, promote)
├── profile/               # Breeze profile + email change
├── report/                # index + halaman per laporan
├── student/               # sizes/, qr, items (student self-service)
└── system/                # smtp/, users/
```

## Aturan Kode

- Gunakan **Laravel 13** style (PHP 8 attributes, Enums, typed properties)
- Blade views menggunakan **Tailwind CSS**
- Semua logic bisnis di **Service Layer** (bukan di Controller)
- Migration harus **idempotent**
- Seeder harus bisa dijalankan berulang (`firstOrCreate`)
- Gunakan **Spatie Permission** untuk role-based access control
- Password harus di-hash dengan **bcrypt**
- Gunakan **Form Request** untuk validasi input
- Gunakan **Resource** untuk JSON response
- Gunakan **Route Model Binding** jika memungkinkan
