# Deployment UniStock — cPanel Shared Hosting

> Panduan step-by-step dari `git clone` sampai aplikasi live.
> Semua perintah ditulis dalam blok `bash` — **copy & paste langsung ke terminal SSH**.

---

## Daftar Isi

| Step | Judul | Waktu |
|------|-------|-------|
| 0 | Persyaratan Server | 2 mnt |
| 1 | Clone & Struktur Direktori | 5 mnt |
| 2 | Setup `.env` | 5 mnt |
| 3 | Composer Install | 5 mnt |
| 4 | Build Frontend (Vite) | 5 mnt |
| 5 | Buat Database di cPanel | 5 mnt |
| 6 | Migrasi & Seeder | 5 mnt |
| 7 | Directory Permissions | 2 mnt |
| 8 | Cron Job Scheduler | 3 mnt |
| 9 | Queue Worker | 5 mnt |
| 10 | Cache Optimize | 3 mnt |
| 11 | Document Root & Final Check | 5 mnt |
| 12 | Troubleshooting | — |

---

## Step 0 — Persyaratan Server

| Kebutuhan | Versi Minimum | Catatan |
|-----------|---------------|---------|
| PHP | 8.3+ | Cek: cPanel → **Select PHP Version** |
| Composer | 2.x | Biasanya sudah terinstall di shared hosting |
| Node.js / Bun | Node 18+ | Hanya untuk build (bisa di lokal) |
| MySQL | 8.0+ | Buat lewat cPanel → MySQL Databases |
| Extension PHP | mbstring, bcmath, pdo_mysql, gd, intl, openssl, tokenizer | Aktifkan di **Select PHP Version** → Extensions |

> 💡 **Penting:** SSH di cPanel biasanya perlu diaktifkan dulu dari
> **cPanel → Terminal** atau **SSH Access** → *Manage SSH Keys*.

---

## Step 1 — Clone & Struktur Direktori

Buka terminal SSH, lalu jalankan perintah berikut satu per satu:

```bash
# ============================================================
# STEP 1A: Masuk ke home directory cPanel Anda
# ============================================================
# ~ = /home/{username} — ganti username dengan akun cPanel Anda
cd ~

# ============================================================
# STEP 1B: Clone repository dari GitHub
# ============================================================
# Git akan membuat folder baru bernama Horizon-UniStock
git clone https://github.com/nirwana-tim/Horizon-UniStock.git

# Masuk ke folder project
cd Horizon-UniStock

# ============================================================
# STEP 1C: Cek hasil clone
# ============================================================
# List isi folder — harus terlihat app/, bootstrap/, config/, dll
ls -la

# Cek versi PHP di server (harus 8.3 ke atas)
php -v

# Cek composer tersedia
composer --version
```

**Struktur direktori di server:**

```
/home/{username}/
├── Horizon-UniStock/          ← Project root (clone di sini)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/                ← ⭐ Arahkan document root ke sini
│   │   ├── build/             ← Hasil build Vite
│   │   ├── index.php          ← Entry point Laravel
│   │   └── .htaccess          ← URL rewriting
│   ├── resources/
│   ├── routes/
│   ├── storage/               ← Wajib writable (Step 7)
│   └── vendor/                ← Dibuat oleh composer (Step 3)
└── public_html/               ← Document root default (biasanya di sini)
```

> 💡 **Penting:** `public/` berisi `index.php` dan `.htaccess`.
> Document root harus menunjuk ke `Horizon-UniStock/public/`,
> **BUKAN** ke `Horizon-UniStock/`. Detail di **Step 11**.

---

## Step 2 — Setup `.env`

```bash
# ============================================================
# STEP 2A: Buat file .env dari template
# ============================================================
cd ~/Horizon-UniStock

# Copy .env.example menjadi .env (file ini tidak ada di git)
cp .env.example .env

# ============================================================
# STEP 2B: Generate application encryption key (APP_KEY)
# ============================================================
# APP_KEY digunakan untuk encrypt session, cookie, dll.
# JANGAN pernah share / pindahkan APP_KEY antar server.
php artisan key:generate

# ============================================================
# STEP 2C: Edit .env sesuai konfigurasi server Anda
# ============================================================
# Ganti nilai berikut dengan editor favorit (nano / vi)
# Contoh: nano .env

# APP_URL wajib domain aktif Anda
# APP_DEBUG wajib false di production (jangan tampilkan error detail)
# DB_* diisi dari cPanel → MySQL Databases (Step 5)

nano .env
```

**Nilai wajib yang diedit di `.env`:**

```env
APP_NAME=UniStock
APP_ENV=production        # ← GANTI dari local → production
APP_DEBUG=false           # ← GANTI dari true → false
APP_URL=https://domain.com  # ← GANTI dengan domain aktif Anda

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u1234567_horizon   # ← Dari Step 5 (prefix user cPanel)
DB_USERNAME=u1234567_admin     # ← Dari Step 5
DB_PASSWORD=password_anda      # ← Dari Step 5

SESSION_DRIVER=file             # ← Sudah benar, jangan diubah
QUEUE_CONNECTION=database       # ← Sudah benar (queue pakai DB)
CACHE_STORE=database            # ← Sudah benar (cache pakai DB)

MAIL_MAILER=smtp                # ← Sesuaikan dengan SMTP provider
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email@anda.com
MAIL_PASSWORD=app_password      # ← App Password, bukan password biasa
MAIL_FROM_ADDRESS=email@anda.com
MAIL_FROM_NAME="${APP_NAME}"
```

> ⚠️ **Catatan SMTP:** Project ini punya fitur simpan SMTP di database
> (tabel `smtp_settings` via menu **Settings → SMTP**). Jika sudah diisi
> lewat UI, nilai `MAIL_*` di `.env` bisa dibiarkan sebagai fallback.
>
> ⚠️ **Gmail:** Harus pakai **App Password** (2FA aktif). SMTP host lain
> (Zoho, Mailgun, dll) sesuaikan `MAIL_HOST` dan `MAIL_PORT`.

---

## Step 3 — Composer Install

```bash
# ============================================================
# STEP 3: Install dependency PHP
# ============================================================
cd ~/Horizon-UniStock

# --no-dev                 : Skip paket development (phpunit, faker, dll)
# --prefer-dist            : Download paket dalam bentuk zip (lebih cepat)
# --optimize-autoloader    : Generate classmap untuk performa lebih baik
composer install --no-dev --prefer-dist --optimize-autoloader

# Verifikasi vendor berhasil dibuat
ls vendor/ | head -5
```

**Jika SSH tidak tersedia** — build vendor di komputer lokal lalu upload:

```bash
# Di komputer lokal (bukan di server):
composer install --no-dev --prefer-dist --optimize-autoloader

# Zip folder vendor:
zip -r vendor.zip vendor

# Upload vendor.zip via cPanel → File Manager,
# lalu Extract di dalam folder ~/Horizon-UniStock/
```

---

## Step 4 — Build Frontend (Vite)

Project ini memakai **Bun** sebagai package manager dan **Vite** untuk build.

```bash
# ============================================================
# STEP 4A: Build di komputer LOKAL (recommended)
# ============================================================
# Jalankan di komputer Anda (bukan di server), di folder project:
bun run build
# atau jika pakai npm:
npm run build

# Hasil build dihasilkan di folder public/build/:
#   public/build/manifest.json
#   public/build/assets/app-{hash}.css
#   public/build/assets/app-{hash}.js
#   public/build/assets/auto-{hash}.js

# ============================================================
# STEP 4B: Upload public/build/ ke server
# ============================================================
# Zip hasil build lalu upload:
cd public
zip -r build.zip build

# Upload build.zip via cPanel → File Manager
# Extract ke dalam ~/Horizon-UniStock/public/
# Hasil akhir: ~/Horizon-UniStock/public/build/manifest.json
```

**Alternatif: Build langsung di server (jika ada Node.js Selector):**

```bash
# cPanel → Setup Node.js App → buat app dengan Node 18
# lalu jalankan di Terminal:
cd ~/Horizon-UniStock

# Install dependency frontend
bun install
# atau: npm install

# Build production assets
bun run build
# atau: npm run build
```

> ⚠️ **Penting:** Jika `public/build/manifest.json` tidak ada,
> halaman akan tampil tanpa CSS/JS (styling rusak). File ini
> **tidak** di-commit ke git (ada di `.gitignore`), jadi wajib
> dibuild & di-upload.

---

## Step 5 — Buat Database di cPanel

Bagian ini dilakukan lewat **GUI cPanel**, bukan terminal.

```
1. Login cPanel → cari menu "MySQL® Databases"
2. Bagian "Create New Database":
   - Database Name : horizon_unistock
   - Klik [ Create Database ]
   - (Di MySQL, nama akan otomatis jadi: u1234567_horizon_unistock)
3. Bagian "Add New User":
   - Username : unistock_user
   - Password : Generate (pakai tombol Generate Password)
   - Klik [ Create User ]
4. Bagian "Add User To Database":
   - User     : unistock_user
   - Database : horizon_unistock
   - Centang  : ☑ ALL PRIVILEGES
   - Klik [ Make Changes ]
```

> ⚠️ **Penting:** Di shared hosting cPanel, nama database & user
> otomatis diberi prefix username cPanel (misal `u1234567_`).
> Gunakan nama **lengkap dengan prefix** saat mengisi `.env`.
>
> Contoh: prefix `u1234567_`
> - `DB_DATABASE=u1234567_horizon_unistock`
> - `DB_USERNAME=u1234567_unistock_user`

---

## Step 6 — Migrasi & Seeder

```bash
# ============================================================
# STEP 6A: Jalankan migration (buat semua tabel)
# ============================================================
cd ~/Horizon-UniStock

# --force : Jalan tanpa konfirmasi (dibutuhkan di production)
php artisan migrate --force

# Verifikasi jumlah tabel (harus 70+ termasuk tabel Spatie)
# Cek di cPanel → phpMyAdmin → pilih database → lihat daftar tabel
```

```bash
# ============================================================
# STEP 6B: Seed data awal (roles & permissions)
# ============================================================
# Membuat:
#   - Roles       : super_admin, admin, staff, student
#   - Permissions : manage-students, manage-distributions, manage-finance
#   - Super admin (SuperadminSeeder)
#   - Student levels (StudentLevelSeeder)
#   - Test user (UserTestSeeder) — bisa dihapus setelah login
php artisan db:seed --force

# ============================================================
# STEP 6C: Buat akun super_admin (disarankan)
# ============================================================
# SuperadminSeeder sudah otomatis jalan di STEP 6B (termasuk
# di DatabaseSeeder). Jika ingin buat ulang / jalankan sendiri:
php artisan db:seed --class=SuperadminSeeder

# Kredensial default yang dicetak di console:
#   Email    : admin@horizon-unistock.ac.id
#   Password : SuperAdmin!123

# Untuk custom email/password, tambahkan di .env SEBELUM seed:
#   SUPERADMIN_EMAIL=admin@kampus.ac.id
#   SUPERADMIN_PASSWORD=PasswordRahasia!456
# lalu jalankan ulang: php artisan db:seed --class=SuperadminSeeder
```

> 💡 Seeder memakai `firstOrCreate()`, jadi **aman dijalankan ulang**.
> Tidak akan duplikat data.
>
> ⚠️ Login di browser tetap diminta **CAPTCHA** — itu normal, selesaikan
> soal penjumlahan saat login. Setelah login, ganti password di profil.

---

## Step 7 — Directory Permissions

```bash
# ============================================================
# STEP 7: Set permission storage & bootstrap/cache
# ============================================================
cd ~/Horizon-UniStock

# storage/ berisi log, session (file), cache, file upload
chmod -R 775 storage/

# bootstrap/cache berisi config & route cache
chmod -R 775 bootstrap/cache/

# Verifikasi
ls -ld storage bootstrap/cache
```

**Jika tidak ada SSH** — via File Manager:
```
1. Klik folder storage → klik [ Permissions ]
2. Isi: 775
3. Centang [ Recurse into subdirectories ] → Apply to [ All files & dirs ]
4. Klik [ Change Permissions ]
5. Ulangi untuk folder bootstrap/cache
```

> ⚠️ Dengan `SESSION_DRIVER=file`, folder `storage/framework/sessions/`
> harus writable, atau user akan gagal login / session hilang.

---

## Step 8 — Cron Job (Scheduler)

```bash
# ============================================================
# STEP 8: Aktifkan Laravel Scheduler
# ============================================================
# Laravel scheduler menjalankan task terjadwal:
#   - logs:cleanup         (harian 02:00 — bersihkan failed_jobs & log)
#   - summaries:calculate  (harian 02:30 — materialized view)
#   - students:auto-promote (Minggu 03:00 — naikkan semester)
#
# Buka crontab:
crontab -e

# Tambahkan baris ini di akhir file:
* * * * * cd /home/{username}/Horizon-UniStock && php artisan schedule:run >> /dev/null 2>&1

# Simpan & keluar:
#   nano    : CTRL+O lalu Enter, CTRL+X
#   vi/vim  : :wq lalu Enter
```

**Alternatif GUI cPanel:**
```
1. cPanel → "Cron Jobs"
2. Common Settings: "Once Per Minute (* * * * *)"
3. Command:
   cd /home/{username}/Horizon-UniStock && php artisan schedule:run >> /dev/null 2>&1
4. Klik [ Add New Cron Job ]
```

> ⚠️ Ganti `/home/{username}` dengan path sebenarnya di server Anda.
> Jalankan `pwd` di dalam `~/Horizon-UniStock` untuk melihat path pastinya.

---

## Step 9 — Queue Worker

Project memakai `QUEUE_CONNECTION=database` — job dikirim ke tabel `jobs`,
lalu diproses oleh worker. Tanpa worker, email & job lain **tidak pernah terkirim**.

### Opsi A — Supervisor (VPS / dedicated hosting)

```bash
# ============================================================
# STEP 9A: Supervisor (hanya jika tersedia)
# ============================================================
# Buat file konfigurasi:
sudo nano /etc/supervisor/conf.d/unistock-worker.conf
```

Isi file konfigurasi supervisor:

```ini
[program:unistock-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/{username}/Horizon-UniStock/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=1
redirect_stderr=true
stdout_logfile=/home/{username}/Horizon-UniStock/storage/logs/worker.log
```

```bash
# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start unistock-worker:*

# Cek status worker
sudo supervisorctl status
```

### Opsi B — Cron Fallback (shared hosting)

Sebagian besar shared hosting **tidak punya Supervisor**. Solusinya jalankan
worker via cron setiap 5 menit. Worker otomatis berhenti setelah 1 jam
(`--max-time=3600`) agar cron bisa me-restart & mencegah memory leak.

```bash
# Buka crontab:
crontab -e

# Tambahkan baris ini:
*/5 * * * * cd /home/{username}/Horizon-UniStock && php artisan queue:work --stop-when-empty --tries=3 --max-time=3600 >> /dev/null 2>&1

# Simpan & keluar
```

> 💡 `--stop-when-empty` membuat worker berhenti setelah memproses semua job
> di antrian — cocok untuk cron (tidak menahan proses terus-menerus).
>
> ⚠️ Opsi cron ini **bukan ideal** untuk aplikasi dengan ribuan job real-time.
> Untuk scale besar, upgrade ke VPS dengan Supervisor.

---

## Step 10 — Cache Optimize

```bash
# ============================================================
# STEP 10: Optimasi production
# ============================================================
cd ~/Horizon-UniStock

# Cache config: gabung semua config/ menjadi 1 file (lebih cepat)
php artisan config:cache

# Cache routes: gabung semua routes/ menjadi 1 file
php artisan route:cache

# Cache views: compile semua Blade template
php artisan view:cache

# Cache events: gabung semua listener & subscribers
php artisan event:cache

# === ATAU jalankan sekaligus ===
php artisan optimize
```

> ⚠️ **Sangat penting di production.** Tanpa ini, setiap request harus
> membaca & parse ulang ratusan file config/route/view.
>
> ⚠️ **Catatan:** Setelah setiap `git pull` / deploy kode baru, jalankan
> ulang `php artisan optimize` agar cache ikut diperbarui.
> Gunakan script `deploy.sh` / `deploy.bat` yang sudah disediakan.

---

## Step 11 — Document Root & Final Check

### 11A. Set Document Root ke `public/`

```
Cara 1 (Recommended) — Subdomain/Domain:
1. cPanel → "Domains" → pilih domain Anda
2. Bagian "Document Root":
   Ganti dari public_html  →  Horizon-UniStock/public
   (pakai path lengkap: /home/{username}/Horizon-UniStock/public)
3. Klik [ Save ]

Cara 2 — Symlink (jika tidak bisa ganti document root):
1. Hapus isi public_html lama (backup dulu)
2. via Terminal SSH:
   ln -s /home/{username}/Horizon-UniStock/public /home/{username}/public_html
```

> ⚠️ Document root **WAJIB** ke `public/`. Jika mengarah ke folder root,
> file `app/`, `config/`, `.env` akan **terekspos publik** — bahaya keamanan!

### 11B. Aktifkan SSL (HTTPS)

```
1. cPanel → "SSL/TLS Status" → centang domain → [ Run AutoSSL ]
2. cPanel → "Domains" → aktifkan "Force HTTPS Redirect"
3. Pastikan di .env: APP_URL=https://domain.com
```

### 11C. Final Check

```bash
# Cek status aplikasi
cd ~/Horizon-UniStock

# Tampilkan log error terbaru (jika ada)
tail -f storage/logs/laravel.log
```

**Checklist verifikasi:**
- [ ] `https://domain.com` menampilkan halaman login
- [ ] CSS/JS tampil (build Vite sudah di-upload)
- [ ] Login dengan akun super_admin berhasil
- [ ] Dashboard & sidebar tampil normal
- [ ] Session tidak hilang saat pindah halaman
- [ ] Test email terkirim (jika SMTP sudah di-set)

---

## Step 12 — Troubleshooting

| Masalah | Kemungkinan Penyebab | Solusi |
|---------|---------------------|--------|
| **500 Internal Server Error** | `.env` salah / vendor belum ada / permission | Cek `.env`, jalankan `composer install`, `chmod 775 storage/` |
| **Halaman putih (blank)** | PHP fatal error, `APP_DEBUG=false` menyembunyikannya | Cek `storage/logs/laravel.log` atau aktifkan `display_errors` sementara |
| **404 Route not found** | Document root salah / `.htaccess` tidak terbaca | Set document root ke `public/`, cek `mod_rewrite` aktif |
| **CSS/JS hilang** | `public/build/manifest.json` tidak ada | Jalankan `bun run build` & upload folder `public/build/` |
| **Class not found** | Vendor belum diinstall | `composer install --no-dev` |
| **SQLSTATE connection refused** | `DB_HOST` / kredensial salah | Cek `.env`, pastikan DB dibuat di Step 5 |
| **Permission denied** | `storage/` tidak writable | `chmod -R 775 storage/ bootstrap/cache/` |
| **Session hilang saat login** | `storage/framework/sessions/` tidak writable | `chmod -R 775 storage/framework/sessions` |
| **Email tidak terkirim** | Queue worker mati / SMTP salah | Jalankan worker (Step 9), cek SMTP di Settings |
| **Token mismatch (419)** | Session file tidak writable / `SESSION_DOMAIN` salah | `chmod 775 storage/framework/sessions`, set `SESSION_DOMAIN=.domain.com` |
| **Layout maroon hilang** | Build asset belum update | Rebuild & re-upload `public/build/` |

---

## Referensi Cepat

| Perintah | Kegunaan |
|----------|----------|
| `php artisan migrate --force` | Buat tabel database |
| `php artisan db:seed --force` | Seed roles & data awal |
| `php artisan optimize` | Cache config/route/view/event |
| `php artisan schedule:run` | Jalankan scheduler (dipanggil cron) |
| `php artisan queue:work` | Proses job antrian |
| `php artisan queue:restart` | Restart worker setelah deploy |
| `bun run build` | Build frontend Vite |
| `php artisan key:generate` | Generate APP_KEY |

**File terkait di repo:**
- `deploy.sh` — script deploy otomatis (Linux/macOS)
- `deploy.bat` — script deploy otomatis (Windows)
- `.env.example` — template konfigurasi environment
