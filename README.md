<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Horizon-UniStock

Sistem manajemen distribusi logistik untuk mahasiswa — berbasis Laravel 13 Blade dengan fitur QR Code, Role & Permission, Import/Export Excel, dan autentikasi lengkap.

## Fitur

| Fitur | Keterangan |
|-------|------------|
| **Auth** | Login, Register, Forgot/Reset Password, Email Verification, 2FA |
| **Role & Permission** | 4 role: super_admin, finance, staff, student |
| **QR Code Identity** | Generate & scan QR Code untuk identifikasi mahasiswa |
| **Import/Export Excel** | Import data mahasiswa & export report distribusi via Maatwebsite Excel |
| **SMTP Mail** | Kirim notifikasi & kredensial akun via email |

## Akun Login

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `superadmin@horizon-unistock.test` | `password` |

## Tech Stack

- **Framework:** Laravel 13 Blade Vanilla
- **Database:** MySQL
- **Auth:** Laravel Breeze (Blade + Alpine.js + Tailwind CSS)
- **Role & Permission:** Spatie Laravel Permission v8
- **QR Code:** f9webltd/simple-qrcode v5 + HTML5 QR Scanner
- **Excel:** Maatwebsite Laravel Excel
- **Build Tool:** Vite

## Installasi

**Prasyarat:** PHP ^8.3, Composer, Node.js, MySQL

```bash
# 1. Clone project
git clone https://github.com/nirwana-tim/Horizon-UniStock.git
cd Horizon-UniStock

# 2. Copy environment & atur database
copy .env.example .env
# Edit .env: atur DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 3. Install PHP dependencies
composer install

# 4. Generate app key
php artisan key:generate

# 5. Buat database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS horizon_unistock"

# 6. Migration & seeder (user + role)
php artisan migrate --seed

# 7. Frontend
npm install
npm run build

# 8. Jalankan
php artisan serve
# Buka http://127.0.0.1:8000
```

## Login

Buka `http://127.0.0.1:8000/login` dan masuk dengan:
- **Email:** `superadmin@horizon-unistock.test`
- **Password:** `password`

## Dokumentasi

Dokumentasi lengkap tiap package ada di folder `docs/`:

| File | Isi |
|------|-----|
| `docs/laravel-13-blade.md` | Blade template, component, directive, Vite |
| `docs/breeze.md` | Auth scaffolding, routes, middleware, 2FA |
| `docs/spatie-permission.md` | Role & permission, seeder, middleware, blade directive |
| `docs/maatwebsite-excel.md` | Export/import Excel, styling, queue |
| `docs/qr-code.md` | Generate QR Code (SVG/PNG), logo, error correction |
| `docs/html5-qrcode.md` | Scan QR via kamera browser |
| `docs/mail-smtp.md` | SMTP Mail, Mailable, queue, attachment |

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
