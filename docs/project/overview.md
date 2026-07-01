# Horizon-UniStock — Overview

Sistem Distribusi Seragam & Inventory Management — Berbasis Web untuk Admin Universitas.

## 1. Tentang

**Horizon-UniStock** adalah sistem berbasis web untuk mengelola proses distribusi seragam mahasiswa. Dibangun untuk menggantikan proses manual yang sebelumnya menggunakan:

- Google Form
- Google Sheet
- Barcode manual
- Checklist manual
- Rekap Excel

**Permasalahan utama dari sistem lama:**

- Data tersebar di banyak file
- Sulit tracking siapa menerima barang apa
- Risiko double submit
- Risiko salah ukuran
- Proses hari-H lambat
- Report membutuhkan rekap manual
- Data stok belum terhubung dengan distribusi

**Solusi yang dirancang:**

```
Student Data → Size Management → QR Identity → Staff Distribution → Inventory Movement → Admin Report
```

## 2. Tujuan

1. Membuat proses distribusi Freshman lebih cepat
2. Mengurangi kesalahan manual
3. Melacak barang yang diberikan ke mahasiswa
4. Menyimpan data distribusi secara terstruktur
5. Menyediakan fondasi inventory management

## 3. Scope MVP

### Target MVP

- **Tanggal implementasi:** 20 Juli 2026
- **Fokus:** Freshman / Mahasiswa Baru

**Prioritas MVP:**

1. Mahasiswa input ukuran
2. Sistem membuat QR
3. Staff scan QR
4. Staff melakukan distribusi
5. Sistem mencatat transaksi
6. Report tersedia
7. Stock Opname Bulanan
8. GPM / Cost Analysis

### Freshman vs Continuing Student

Tidak perlu membuat dua aplikasi. Gunakan field `student_type`:

- `freshman`
- `continuing`

Perbedaan hanya pada onboarding, email, ukuran, eligible. Flow distribusi tetap sama.

### Out of Scope MVP

- Continuing full system
- POS eceran
- FIFO / VIVO cost method
- Advanced Stock Opname
- Advanced Cost Analytics
- Email automation penuh
- Integrasi SIS
- Mobile app native

## 4. Fitur Per Role

### Super Admin

| Fitur | Keterangan |
|-------|-----------|
| Kelola User & Role | CRUD user, atur role & permission (Spatie) |
| System Config | Atur setting sistem global, maintenance mode |
| Audit Log | Lihat seluruh aktivitas pengguna |
| Backup Database | Backup & restore data |
| Monitoring | Pantau semua modul |

### Admin (Admin)

| Fitur | Keterangan |
|-------|-----------|
| Import Data Mahasiswa | Upload Excel → Validasi → Preview → Commit → Import Log |
| Import Eligible Payment | Upload data pembayaran mahasiswa |
| Kelola Master Data | Fakultas, Prodi, Level, Item, Size, Kategori |
| Kelola Distribution Stages | Atur tahap distribusi |
| Create Entitlement | Atur hak barang (Prodi + Level + Period + Student Type + Stage) |
| Generate Akun Mahasiswa | Username=NIM, Password=random 12 char |
| Input Email Kampus | Isi email kampus (@krw.horizon.ac.id) |
| Stock Receive | Input barang masuk dari vendor |
| Buat Jadwal Distribusi | Pilih stage, item, lokasi & jadwal |
| Monitor Perubahan Ukuran | Lihat log perubahan ukuran |
| Monitor & Report | Export Distribution Report & Stock Report (Excel) |

### Staff

| Fitur | Keterangan |
|-------|-----------|
| Scan QR (Identitas Permanen) | QR 1x seumur hidup |
| Cari NIM Manual | Fallback jika QR gagal |
| Lihat Tahap Distribusi Aktif | System otomatis deteksi tahap |
| Lihat Data Mahasiswa | Profile, entitlement, ukuran |
| Checklist Item Tahap Ini | Centang barang tahap yang aktif |
| Edit Actual Size | Jika berbeda — dicatat log |
| Validasi Stock | Cek ketersediaan stok per size |
| Partial Pickup | Jika stok kurang, bisa kasih sebagian |
| Submit Transaksi | Simpan → Stock OUT → Balance - |

### Student / Mahasiswa

| Fitur | Keterangan |
|-------|-----------|
| Login | Username=NIM, Password=random (dari Admin) |
| Ganti Password (Wajib) | Wajib ganti password saat first login |
| Dashboard | Info akun & status |
| Profile | Data diri |
| Input Ukuran | Seragam & sepatu, lihat size chart vendor |
| Update Ukuran | Maksimal 1 kali perubahan |
| QR Identity (Permanen) | QR 1x generate, berlaku seumur hidup |
| Lihat Jadwal Per Tahap | Jadwal pengambilan per stage |
| Lupa Password | Input NIM → OTP 6 digit → Ganti password |
