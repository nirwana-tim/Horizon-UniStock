# UniStock — Overview

Sistem Distribusi Seragam & Inventory Management — Berbasis Web untuk Admin Universitas.

## 1. Tentang

**UniStock** adalah sistem berbasis web untuk mengelola proses distribusi seragam mahasiswa. Dibangun untuk menggantikan proses manual yang sebelumnya menggunakan:

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

### Student Level

Tidak perlu membuat dua aplikasi. Gunakan field `student_level` (kode) yang nilainya dikelola lewat master data **Student Level** (`/master-data/student-level` — read-only, di-seed oleh `StudentLevelSeeder`).

Nilai default yang tersedia (kode = `deskripsi` = status):
- `Y1S1` — Year 1 Sem 1 (Freshman)
- `Y1S2` — Year 1 Sem 2 (Freshman)
- `Y2S1` — Year 2 Sem 1 (Continuing)
- `Y2S2` — Year 2 Sem 2 (Continuing)
- `Y3S1` — Year 3 Sem 1 (Continuing)
- `Y3S2` — Year 3 Sem 2 (Continuing)
- `Y4S1` — Year 4 Sem 1 (Continuing)
- `Y4S2` — Year 4 Sem 2 (Continuing)
- `graduated` — Graduated / Lulus

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
| Kelola User & Role | CRUD user, atur role & status aktif (`/admin/users`) |
| System Config — SMTP | Atur & test koneksi SMTP dari database (`/system/smtp`) |
| Monitoring | Pantau semua modul |

### Admin

| Fitur | Keterangan |
|-------|-----------|
| Import Data | Upload Excel → Validasi → Preview → Commit → Import log (`import_batches`) |
| Kelola Master Data | Fakultas, Prodi, Generasi, Level, Item, Kategori, Type, Departemen, Size, Vendor |
| Kelola Item & Varian | Item (base code 4 segmen), varian ukuran (SKU), harga per periode |
| Create Entitlement | Atur hak barang per Student Level (code + deskripsi + item + qty) |
| Kelola Jadwal Distribusi | Period, student level, fakultas/prodi/generasi (opsional), tanggal, lokasi, sesi |
| Kelola Eligibility | Toggle status kelayakan per mahasiswa (`/student/eligibility`) |
| Monitor Perubahan Ukuran | Lihat log perubahan ukuran (`size-events`) |
| Generate Akun Mahasiswa | Username=NIM, password random, export kredensial, reset password |
| Input Email Kampus | Isi email kampus via verifikasi OTP |
| Stock Receive | Input barang masuk dari vendor, upload stock opname |
| Monitor & Report | Distribution Report, Stock Report, GPM, Stock Card, Loss, Size Recap (Excel) |

### Staff

| Fitur | Keterangan |
|-------|-----------|
| Scan QR (Identitas Permanen) | QR berisi NIM, 1x seumur hidup |
| Cari NIM Manual | Fallback jika QR gagal (`/distribution/student/{nim}`) |
| Lihat Data Mahasiswa | Profile, entitlement, ukuran, stock per size |
| Checklist Item Distribusi | Centang barang + isi actual size & qty |
| Validasi Stock | Cek ketersediaan stok per size secara live |
| Partial Pickup | Jika stok kurang, transaksi tersimpan sebagai `partial` |
| Submit Transaksi | Simpan → Stock OUT → Balance - (anti double submit) |

### Student / Mahasiswa

| Fitur | Keterangan |
|-------|-----------|
| Login | Username=NIM, password (dari Admin / generate otomatis) |
| Ganti Password (Wajib) | Wajib ganti password saat first login (`must_change_password`) |
| Dashboard | Info akun & status |
| Profile | Data diri, ganti email kampus (OTP) |
| Input Ukuran | Seragam & sepatu via size event; update dibatasi change count |
| QR Identity (Permanen) | QR berisi NIM, berlaku seumur hidup |
| Lihat Jadwal | Jadwal pengambilan yang cocok dengan level/fakultas/prodi/generasi |
| Lupa Password | Input NIM → OTP 6 digit → Ganti password |
