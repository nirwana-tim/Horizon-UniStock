<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<h1 align="center">Horizon-UniStock</h1>

<p align="center">
  Sistem Distribusi Seragam & Inventory Management — Berbasis Web untuk Admin Universitas
</p>

---

**Horizon-UniStock** adalah sistem berbasis web untuk mengelola proses distribusi seragam mahasiswa. Menggantikan proses manual (Google Form, Google Sheet, barcode manual, checklist manual, rekap Excel) dengan sistem terintegrasi.

**Alur utama:** Student Data → Size Management → QR Identity → Staff Distribution → Inventory Movement → Admin Report

# Tech Stack

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

# Quick Start

## 1. Clone project
Clone repositori ini ke komputer Anda:
```bash
git clone https://github.com/nirwana-tim/Horizon-UniStock.git
```

## 2. Masuk folder project
```bash
cd Horizon-UniStock
```

## 3. Copy environment & atur database
Salin `.env.example` menjadi `.env`, lalu edit `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`:
```bash
copy .env.example .env
```

## 4. Install PHP dependencies
```bash
composer install
```

## 5. Generate app key
```bash
php artisan key:generate
```

## 6. Jalankan migrasi & seeder
```bash
php artisan migrate --seed
```

## 7. Install frontend dependencies
```bash
npm install && npm run build
```

## 8. Buat storage link
Untuk QR code dan upload file:
```bash
php artisan storage:link
```

## 9. Jalankan server
```bash
php artisan serve
```
Buka http://127.0.0.1:8000

### Login Default
| Role | Email | Password |
|------|-------|----------|
| Super Admin | `superadmin@horizon-unistock.test` | `password` |

## Dokumentasi

Seluruh dokumentasi tersedia di folder [`docs/`](docs/):

| Kategori | Isi |
|----------|-----|
| [Project](docs/project/) | Overview, PRD, ERD, flowchart, arsitektur, item code, security, risks, timeline, testing |
| [Technical](docs/technical/) | Blade, Breeze, Spatie, Excel, QR Code, QR Scanner, Mail SMTP |
| [Guides](docs/guides/) | Instalasi, kontribusi, pedoman AI |

## Lisensi

[MIT License](https://opensource.org/licenses/MIT)
