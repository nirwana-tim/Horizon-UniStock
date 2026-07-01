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
| Framework | Laravel 13 |
| Database | MySQL |
| Frontend | Blade + Tailwind CSS + Vite |
| Auth | Laravel Breeze |
| Permission | Spatie Laravel Permission |
| Excel | Maatwebsite Laravel Excel |
| QR Code | Simple QR Code (SVG/PNG) |
| QR Scanner | HTML5 QR Scanner |
| Email | Laravel Mail + SMTP |

## Aturan Kode

- Gunakan **Laravel 13** style (PHP 8 attributes, Enums, typed properties)
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
