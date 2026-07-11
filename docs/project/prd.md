# Product Requirements Document (PRD)
# UniStock — Sistem Distribusi Seragam & Inventory Admin

---

## 1. Executive Summary

Project ini merupakan pengembangan sistem berbasis web untuk membantu Admin dalam mengelola proses distribusi seragam mahasiswa. Sistem dibuat untuk menggantikan proses manual yang sebelumnya menggunakan Google Form, Google Sheet, barcode manual, checklist manual, dan rekap Excel.

Permasalahan utama dari sistem lama adalah: data tersebar di banyak file, sulit tracking siapa menerima barang apa, risiko double submit, risiko salah ukuran, proses hari-H lambat, report membutuhkan rekap manual, dan data stok belum terhubung dengan distribusi.

Solusi yang dirancang adalah sistem terintegrasi yang mencakup: Student Data → Size Management → QR Identity → Staff Distribution → Inventory Movement → Admin Report. MVP difokuskan pada distribusi Freshman dengan target implementasi 20 Juli 2026.

---

## 2. Business Objectives

### 2.1 Tujuan Bisnis

1. Mempercepat proses distribusi seragam mahasiswa baru (target < 5 menit per mahasiswa, sebelumnya > 15 menit)
2. Menghilangkan kesalahan manual (double submit, salah ukuran, data hilang)
3. Tracking barang yang diberikan ke mahasiswa secara real-time
4. Data distribusi tersimpan terstruktur dan bisa di-export
5. Fondasi inventory management yang bisa dikembangkan ke sistem penuh

### 2.2 Target Implementasi

- MVP: **20 Juli 2026**
- Fokus: **Freshman / Mahasiswa Baru**

---

## 3. Stakeholders

| Stakeholder | Peran | Kebutuhan Utama |
|-------------|-------|-----------------|
| Admin Admin | Pengguna utama sistem | Import data, atur entitlement, buat jadwal, export report, stock opname, GPM |
| Staff Distribusi | Operator lapangan | Scan QR, distribusi barang, validasi stok, partial pickup |
| Mahasiswa | Penerima barang | Input ukuran, lihat jadwal, lihat QR, lupa password |
| Super Admin | IT/Monitoring | Kelola user, monitoring, backup, audit log |

---

## 4. User Personas

### 4.1 Admin Admin (Budi)

- **Role**: Admin Admin
- **Kebutuhan**: Import data ribuan mahasiswa dari Excel, atur hak barang per prodi, buat jadwal distribusi, monitor stok, lakukan stock opname bulanan, hitung GPM
- **Pain Point**: Proses manual di Excel memakan waktu berjam-jam, sulit tracking perubahan ukuran

### 4.2 Staff Distribusi (Sari)

- **Role**: Staff
- **Kebutuhan**: Scan QR mahasiswa, checklist barang, submit distribusi, validasi stok
- **Pain Point**: QR sering gagal, harus cari manual, tidak ada tracking real-time

### 4.3 Mahasiswa (Andi)

- **Role**: Student
- **Kebutuhan**: Login, input ukuran, lihat jadwal ambil seragam, lupa password
- **Pain Point**: Tidak tahu kapan harus ambil seragam, ukuran salah

### 4.4 Super Admin (IT)

- **Role**: Super Admin
- **Kebutuhan**: Kelola user & role, monitoring semua modul, backup database
- **Pain Point**: Sulit monitoring jika semua manual

---

## 5. Functional Requirements

### 5.1 Role: Admin Admin

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-01 | Import data mahasiswa | Upload Excel → Validasi → Preview → Commit → Import Log | High |
| FR-02 | Import eligible payment | Upload data pembayaran mahasiswa | High |
| FR-03 | Kelola master data | Fakultas, Prodi, Level, Item, Kategori, Size, Variant, Vendor | High |
| FR-04 | Kelola Distribution Stages | Buat tahap distribusi (Tahap 1, 2, 3...) | High |
| FR-05 | Create Entitlement | Atur hak barang: Prodi + Level + Period + Student Type + Item + Qty | High |
| FR-06 | Generate akun mahasiswa | Username=NIM, Password=random 12 char | High |
| FR-07 | Input email kampus | Isi email @krw.horizon.ac.id | High |
| FR-08 | Stock Receive | Input barang masuk dari vendor | High |
| FR-09 | Buat Jadwal Distribusi | Pilih stage, item, lokasi & jadwal, notifikasi | High |
| FR-10 | Monitor perubahan ukuran | Log perubahan ukuran | Medium |
| FR-11 | Stock Opname Bulanan | Upload hasil opname → Hitung variance → Adjustment | High |
| FR-12 | GPM / Cost Analysis | Laba/rugi per item, HPP vs harga jual | Medium |
| FR-13 | Export Distribution Report | Excel: sudah ambil, belum, partial | High |
| FR-14 | Export Stock/Inventory Report | Excel: stock balance, movement | High |
| FR-15 | Export GPM Report | Excel laporan GPM | Medium |

### 5.2 Role: Staff

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-16 | Login staff | Email + password | High |
| FR-17 | Scan QR mahasiswa | QR permanen, 1x seumur hidup | High |
| FR-18 | Cari NIM manual | Fallback jika QR gagal | High |
| FR-19 | Lihat data mahasiswa | Profile, entitlement, ukuran | High |
| FR-20 | Checklist item | Centang barang tahap aktif | High |
| FR-21 | Edit actual size | Jika berbeda — tercatat log | High |
| FR-22 | Validasi stok | Cek stok per size | High |
| FR-23 | Partial pickup | Jika stok kurang | High |
| FR-24 | Submit transaksi | Simpan → Stock OUT → Balance - | High |

### 5.3 Role: Mahasiswa

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-25 | Login | Username=NIM, Password dari Admin | High |
| FR-26 | Ganti password wajib | First login | High |
| FR-27 | Dashboard | Info, notifikasi, status, riwayat | High |
| FR-28 | Input/update profil & ukuran | Seragam & sepatu | High |
| FR-29 | Lihat size chart vendor | Referensi ukuran | Medium |
| FR-30 | Update ukuran | Maksimal 1x | High |
| FR-31 | Generate QR | 1x seumur hidup | High |
| FR-32 | Lihat jadwal distribusi | Per tahap | High |
| FR-33 | Lupa password | OTP 6 digit ke email | High |

### 5.4 Role: Super Admin

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-34 | Kelola user, role, permission | CRUD + Spatie | High |
| FR-35 | System config & maintenance | Setting global | Medium |
| FR-36 | Audit log | Filter, export | Medium |
| FR-37 | Backup & restore database | Download / restore | Medium |
| FR-38 | Monitoring semua modul | Dashboard | Low |

### 5.5 Stock Opname

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-39 | Buat batch opname | Tanggal, periode, status | High |
| FR-40 | Upload hasil opname | Excel hasil hitung fisik | High |
| FR-41 | Hitung variance otomatis | System vs Physical | High |
| FR-42 | Adjustment journal | Surplus/shortage | High |
| FR-43 | Riwayat opname per item | History bulanan | Medium |
| FR-44 | Export hasil opname | Excel | Medium |

### 5.6 GPM / Cost Analysis

| ID | Fitur | Keterangan | Prioritas |
|----|-------|-----------|-----------|
| FR-45 | Tracking HPP per batch | Harga Pokok Pembelian | Medium |
| FR-46 | Set harga jual per item | Selling price | Medium |
| FR-47 | Laporan GPM otomatis | (Harga Jual - HPP) × Qty | Medium |
| FR-48 | Laporan laba/rugi | Per item, kategori, periode | Medium |
| FR-49 | Export laporan GPM | Excel | Medium |

---

## 6. Non-Functional Requirements

| ID | Requirement | Target |
|----|-------------|--------|
| NFR-01 | Waktu response halaman | < 3 detik |
| NFR-02 | Concurrent users saat hari-H | 50+ staff |
| NFR-03 | QR scan response | < 2 detik |
| NFR-04 | Sistem availability | 99% (07.00-17.00) |
| NFR-05 | Audit logging | Semua transaksi tercatat |
| NFR-06 | Data stok real-time | Update < 5 detik |
| NFR-07 | Password hashing | bcrypt |
| NFR-08 | Access control | Role-based (Spatie) |
| NFR-09 | Backup frequency | Daily automated |

---

## 7. Data Requirements

### 7.1 Entity List

**Master Data:**
- Users, Faculties, Study Programs, Program Levels
- Students, Student Size Profiles, Student Size Items, Student Size Histories
- Item Categories, Items, Item Variants, Vendors

**Distribution:**
- Distribution Periods, Distribution Stages
- Eligibility Records
- Entitlements, Entitlement Items
- Distribution Schedules, Dist Schedule Items
- Distribution Transactions, Distribution Items

**Inventory:**
- Stock Receives, Stock Receive Items
- Stock Movements, Stock Balances
- Stock Opnames, Stock Opname Items, Stock Opname Adjustments

**Supporting:**
- Import Batches
- Email Notifications
- Audit Logs

---

## 8. System Architecture

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

---

## 9. Assumptions & Constraints

1. Data mahasiswa sudah ada di Excel
2. Setiap mahasiswa memiliki email kampus @krw.horizon.ac.id
3. Distribusi dilakukan dalam tahapan (stage)
4. Stock opname dilakukan bulanan
5. HPP dihitung per batch penerimaan (rata-rata)
6. QR code bersifat permanen
7. Update ukuran maksimal 1x

---

## 10. Risks & Mitigation

| Risiko | Dampak | Mitigation |
|--------|--------|------------|
| Data Excel tidak konsisten | Import gagal | Validation + preview + error log |
| QR gagal saat hari-H | Distribusi terhambat | Manual search NIM + backup Excel |
| Sistem lambat | Antrian panjang | Caching + fallback procedure |
| Stock opname selisih besar | Rugi tidak tercatat | Adjustment journal dengan approval |
| Double submit | Data duplikat | Sistem tolak transaksi sudah ada |
| Stok kurang | Mahasiswa tidak kebagian | Partial pickup + notifikasi |

---

## 11. Success Metrics

| Metrik | Target | Cara Ukur |
|--------|--------|-----------|
| Waktu distribusi per mahasiswa | < 5 menit | Log timestamp |
| Double submit | 0% | Audit log |
| Tracking barang ke mahasiswa | 100% | Distribution items table |
| Stock opname variance | < 2% | Stock opname report |
| Laporan GPM tersedia | Real-time | GPM dashboard |
| User satisfaction | > 4/5 | Survey |

---

## 12. Out of Scope (Fase Lanjutan)

- Continuing Student full system
- POS eceran
- FIFO / VIVO cost method
- Cost accounting penuh
- Revenue dashboard
- Advanced Stock Opname
- Advanced Cost Analytics
- Email automation penuh
- Integrasi SIS
- Mobile app native
- Multi warehouse
- Dashboard Admin real-time

---

## 13. Timeline Development

| Minggu | Fokus | Deliverable |
|--------|-------|-------------|
| Minggu 1 | Setup | Laravel, Database, Auth (Breeze), Role (Spatie) |
| Minggu 2 | Master Data | Model, Migration, Import Excel |
| Minggu 3 | Core | Input ukuran, QR, Entitlement, Stock Receive |
| Minggu 4 | Distribution | Staff scan, Checklist, Stock OUT, Stock Opname |
| Minggu 5 | Report | Distribution, Inventory, GPM Report |
| Minggu 6 | Final | Testing, Deployment, UAT |

---

## 14. Testing Scenarios

| No | Skenario | Expected Result |
|----|----------|-----------------|
| 1 | Import duplicate NIM | Error, tidak boleh duplikat |
| 2 | QR tidak valid | Error, izinkan cari NIM |
| 3 | Mahasiswa tidak eligible | Pengambilan ditolak |
| 4 | Double submit | Sistem tolak |
| 5 | Actual size berbeda | Simpan actual size, log perubahan |
| 6 | Stok kurang | Tawarkan partial pickup |
| 7 | Partial pickup | Simpan qty sebagian |
| 8 | Export report | File Excel |
| 9 | Lupa password | OTP terkirim, reset berhasil |
| 10 | Import format salah | Error handling |
| 11 | Update ukuran kedua | Tolak, maks 1x |
| 12 | Email duplikat | Hanya 1x per mahasiswa |
| 13 | Variance positif | Surplus, adjustment |
| 14 | Variance negatif | Shortage, adjustment |
| 15 | GPM calculation | (Harga Jual - HPP) × Qty |
| 16 | Import stock opname | Variance otomatis |
