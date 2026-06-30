<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Logo">
</p>

<h1 align="center">Horizon-UniStock</h1>

<p align="center">
  Sistem Distribusi Seragam & Inventory Management — Berbasis Web untuk Finance Universitas
</p>

---

## Tentang

**Horizon-UniStock** adalah sistem berbasis web untuk mengelola proses distribusi seragam mahasiswa. Dibangun untuk menggantikan proses manual (Google Form, Google Sheet, barcode & checklist manual, rekap Excel) dengan sistem yang terintegrasi, cepat, dan terstruktur.

**Masalah sebelumnya:**
- Data tersebar di banyak file
- Sulit tracking siapa menerima barang apa
- Risiko double submit & salah ukuran
- Proses hari-H lambat
- Report harus rekap manual
- Stok tidak terhubung dengan distribusi

---

## Tujuan

1. Membuat proses distribusi Freshman lebih cepat
2. Mengurangi kesalahan manual
3. Melacak barang yang diberikan ke mahasiswa
4. Menyimpan data distribusi secara terstruktur
5. Menyediakan fondasi inventory management

---

## Fitur Berdasarkan Role

### Super Admin
- Kelola seluruh data master
- Kelola user & role (Spatie Permission)
- Audit log aktivitas sistem
- Backup & restore database

### Admin (Finance)
- Import data mahasiswa dari Excel
- Import data eligible / pembayaran
- Kelola program studi, level, item, size
- Atur entitlement (hak barang) — tanpa coding ulang
- Atur periode distribusi & jadwal
- Export report distribusi & inventory (Excel)

### Staff
- Scan QR identity mahasiswa
- Cari mahasiswa manual berdasarkan NIM
- Lihat entitlement & ukuran yang diharapkan
- Checklist item yang diberikan
- Edit actual size (jika berbeda dari input)
- Submit transaksi pengambilan

### Student
- Login ke sistem
- Lihat profil
- Input ukuran seragam
- Update ukuran (maksimal 1 kali)
- Lihat size chart vendor
- Dapatkan QR identity
- Lihat jadwal pengambilan

---

## Alur Distribusi Freshman

```
Finance Import Data
       ↓
Mahasiswa Login → Input Ukuran
       ↓
Validasi Data → Generate QR
       ↓
Jadwal Distribusi
       ↓
Staff Scan QR → Validasi Eligible
       ↓
Tampilkan Item → Checklist Barang
       ↓
Submit Pengambilan → Update Inventory
       ↓
Report
```

---

## Flowchart Lengkap Sistem

```mermaid
flowchart TD
    %% =====================
    %% START SYSTEM
    %% =====================
    A([Start System]) --> B[User membuka aplikasi]
    B --> C{Pilih Role Login}

    %% =====================
    %% MAHASISWA
    %% =====================
    C -->|Mahasiswa| M1[Login Mahasiswa]
    M1 --> M2{Akun Valid?}
    M2 -->|Tidak| M3[Tampilkan Error Login]
    M3 --> M1
    M2 -->|Ya| M4[Dashboard Mahasiswa]
    M4 --> M5[Melihat Profile]
    M5 --> M6[Input Data Diri]
    M6 --> M7[Melihat Size Chart Vendor]
    M7 --> M8[Input Ukuran Seragam & Sepatu]
    M8 --> M9{Data Lengkap?}
    M9 -->|Tidak| M10[QR Belum Dibuat]
    M10 --> M6
    M9 -->|Ya| M11[Generate QR Token]
    M11 --> M12[Melihat QR & Jadwal Pengambilan]
    M12 --> M13[Datang ke Lokasi Distribusi]
    M13 --> M14[QR Dipindai Staff]
    M14 --> M15[Status Pengambilan Update]
    M15 --> END1([Selesai])

    %% =====================
    %% STAFF
    %% =====================
    C -->|Staff| S1[Login Staff]
    S1 --> S2{Akun Valid?}
    S2 -->|Tidak| S3[Error Login]
    S3 --> S1
    S2 -->|Ya| S4[Dashboard Staff]
    S4 --> S5[Buka Halaman Scan QR]
    S5 --> S6[Scan QR Mahasiswa]
    S6 --> S7{QR Valid?}
    S7 -->|Tidak| S8[Tampilkan QR Tidak Valid]
    S8 --> S5
    S7 -->|Ya| S9[Tampilkan Data Mahasiswa]
    S9 --> S10[Cek Eligible]
    S10 --> S11{Eligible?}
    S11 -->|Tidak| S12[Pengambilan Ditolak]
    S11 -->|Ya| S13[Ambil Entitlement Mahasiswa]
    S13 --> S14[Tampilkan List Item]
    S14 --> S15[Checklist Item]
    S15 --> S16[Edit Actual Size Jika Perlu]
    S16 --> S17[Validasi Qty & Stock]
    S17 --> S18{Valid?}
    S18 -->|Tidak| S19[Tampilkan Error]
    S19 --> S15
    S18 -->|Ya| S20[Submit Pengambilan]
    S20 --> S21[Simpan Distribution Transaction]
    S21 --> S22[Simpan Distribution Item]
    S22 --> S23[Stock Movement OUT]
    S23 --> S24[Update Stock Balance]
    S24 --> END2([Selesai])

    %% =====================
    %% FINANCE ADMIN
    %% =====================
    C -->|Finance Admin| F1[Login Finance]
    F1 --> F2{Akun Valid?}
    F2 -->|Tidak| F3[Error Login]
    F3 --> F1
    F2 -->|Ya| F4[Dashboard Finance]
    F4 --> F5[Import Data Mahasiswa]
    F5 --> F6[Validasi Data]
    F6 --> F7[Simpan Master Student]
    F4 --> F8[Import Eligible Payment]
    F8 --> F9[Simpan Eligibility Record]
    F4 --> F10[Kelola Master Data]
    F10 --> F11[Kelola: Fakultas, Prodi, Level, Item, Size]
    F4 --> F12[Create Entitlement]
    F12 --> F13[Set Hak Barang: Prodi, Level, Period, Student Type]
    F4 --> F14[Create Distribution Schedule]
    F14 --> F15[Jadwal Aktif]
    F4 --> F16[Monitor Distribution]
    F16 --> F17[Melihat Report]
    F17 --> F18[Export: Distribution Report, Stock Report]
    F18 --> END3([Selesai])

    %% =====================
    %% SUPER ADMIN
    %% =====================
    C -->|Super Admin| A1[Login Super Admin]
    A1 --> A2{Akun Valid?}
    A2 -->|Tidak| A3[Error Login]
    A3 --> A1
    A2 -->|Ya| A4[Dashboard Super Admin]
    A4 --> A5[Manage User]
    A5 --> A6[Kelola: User, Role, Permission]
    A4 --> A7[Manage System Configuration]
    A7 --> A8[Kelola: Setting Sistem, Audit Log, Backup]
    A4 --> A9[Monitoring Semua Modul]
    A9 --> END4([Selesai])

    %% =====================
    %% CONNECTION
    %% =====================
    F17 -.-> S13
    A9 -.-> F4
```

---

## Tech Stack

| Teknologi | Keterangan |
|-----------|-----------|
| **Framework** | Laravel 10 |
| **PHP** | ^8.1 |
| **Database** | MySQL |
| **Frontend** | Blade + Tailwind CSS |
| **Build Tool** | Vite 5 |
| **Auth** | Laravel Breeze / Fortify |
| **Permission** | Spatie Laravel Permission |
| **QR Code** | Simple QR Code |
| **QR Scanner** | HTML5 QR Scanner |
| **Import/Export** | Laravel Excel |

---

## Instalasi

```bash
# 1. Clone project
git clone https://github.com/username/horizon-unistock.git

# 2. Masuk ke folder project
cd horizon-unistock

# 3. Install PHP dependencies
composer install

# 4. Copy environment file
copy .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Setup database di .env
# DB_DATABASE=horizon_unistock
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Jalankan migrasi
php artisan migrate --seed

# 8. Install frontend dependencies
npm install
npm run build
```

## Menjalankan Aplikasi

```bash
# Via Laragon — Start All, lalu buka:
http://localhost/Horizon-UniStock/public

# Atau via Artisan
php artisan serve
# Buka http://127.0.0.1:8000
```

---

## Lisensi

[MIT License](https://opensource.org/licenses/MIT)
