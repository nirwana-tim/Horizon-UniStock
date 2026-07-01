# Flowchart Lengkap Sistem

## Kode Warna Role

| Warna | Role |
|-------|------|
| Ungu | Super Admin |
| Biru | Admin Admin |
| Oranye | Staff |
| Hijau | Student |

---

## 5.1 Flow Start System — Pilih Role

```mermaid
flowchart TD
    classDef startEnd fill:#1e293b,color:#fff,stroke:#0f172a,stroke-width:2px

    START([Start System]) --> B[User membuka aplikasi]
    B --> C{Pilih Role Login}

    C -->|Super Admin| SA([Super Admin])
    C -->|Admin Admin| FA([Admin Admin])
    C -->|Staff| ST([Staff])
    C -->|Student| SU([Student])

    class START startEnd
    class SA,FA,ST,SU startEnd
```

---

## 5.2 Flow Student / Mahasiswa

```mermaid
flowchart TD
    classDef student fill:#16a34a,color:#fff,stroke:#15803d,stroke-width:2px
    classDef decision fill:#fbbf24,color:#000,stroke:#f59e0b,stroke-width:2px
    classDef error fill:#ef4444,color:#fff,stroke:#dc2626,stroke-width:2px
    classDef startEnd fill:#1e293b,color:#fff,stroke:#0f172a,stroke-width:2px
    classDef process fill:#22c55e,color:#fff,stroke:#16a34a,stroke-width:1px
    classDef dashboard fill:#065f46,color:#fff,stroke:#047857,stroke-width:2px
    classDef warning fill:#d97706,color:#fff,stroke:#b45309,stroke-width:2px

    M1[Login NIM + Password] --> M1X{Percobaan Login > 3?}
    M1X -->|Ya| M1Y[Akun Terkunci 15 Menit]
    M1Y --> M1
    M1X -->|Tidak| M2{Akun Valid?}

    M2 -->|Tidak| M3[Error Login]
    M2 -->|Ya| M4{First Login?}

    M4 -->|Ya| M5[Ganti Password Wajib]
    M5 --> M6[Login Ulang]
    M6 --> M1

    M4 -->|Tidak| M7[Dashboard Mahasiswa]

    M7 --> M7A[Info: Email Kampus Terdaftar]
    M7A --> M7B[Notifikasi: Ada Jadwal Baru?]
    M7B --> M7C[Status: Belum Ambil / Sudah Ambil]
    M7C --> M7D[Riwayat Transaksi]

    M7 --> M25{Lengkapi Data?}
    M25 -->|Logout| E1([Logout])
    M25 -->|Lupa Password| M19
    M25 -->|Input Profile| M8

    M8[Input Profile & Data Diri] --> M9[Input Ukuran Seragam & Sepatu]
    M9 --> M10{Sudah Pernah Update?}
    M10 -->|Ya| M11[Notifikasi: Maks 1x Update]
    M11 --> M13
    M10 -->|Tidak| M12[Simpan Ukuran]

    M12 --> M13{Data Lengkap?}
    M13 -->|Tidak| M8
    M13 -->|Ya| M14[Generate QR Token]
    M14 --> M15[Lihat QR & Jadwal]
    M15 --> M7

    M19[Lupa Password] --> M20[Input NIM]
    M20 --> M21[Kirim OTP 6 Digit ke Email Kampus]
    M21 --> M22{OTP Benar?}
    M22 -->|Tidak| M23[Error OTP]
    M23 --> M20
    M22 -->|Ya| M24[Ganti Password Baru]
    M24 --> M1

    class M1,M6,M8,M9,M12,M14,M15,M20,M21,M24 student
    class M2,M4,M10,M13,M22,M25 decision
    class M3,M11,M23,M1X error
    class M7,M7A,M7B,M7C,M7D dashboard
    class E1 startEnd
    class M5,M19 process
    class M1Y,M7C warning
```

---

## 5.3 Flow Staff

```mermaid
flowchart TD
    classDef staff fill:#ea580c,color:#fff,stroke:#c2410c,stroke-width:2px
    classDef decision fill:#fbbf24,color:#000,stroke:#f59e0b,stroke-width:2px
    classDef error fill:#ef4444,color:#fff,stroke:#dc2626,stroke-width:2px
    classDef startEnd fill:#1e293b,color:#fff,stroke:#0f172a,stroke-width:2px
    classDef process fill:#f97316,color:#fff,stroke:#ea580c,stroke-width:1px
    classDef stage fill:#a16207,color:#fff,stroke:#854d0e,stroke-width:2px

    S1[Login Staff] --> S2{Akun Valid?}
    S2 -->|Tidak| S3[Error Login]
    S3 --> S1
    S2 -->|Ya| S4[Dashboard Staff]

    S4 --> S5{Pilih Metode Cari}
    S5 -->|Scan QR Permanen| S6[Scan QR Mahasiswa]
    S5 -->|Cari NIM| S7[Cari Manual NIM]

    S6 --> S8{QR Valid?}
    S8 -->|Tidak| S9[QR Tidak Valid]
    S9 --> S5
    S8 -->|Ya| S10[Tampilkan Data Mahasiswa]

    S7 --> S10

    S10 --> S10A[System Deteksi Tahap Aktif]
    S10A --> S10B[Tampilkan Status Tahap: 1 / 2 / 3]

    S10B --> S11[Cek Eligible per Tahap]
    S11 --> S12{Eligible?}
    S12 -->|Tidak| S13[Pengambilan Ditolak]
    S13 --> S4
    S12 -->|Ya| S14[Ambil Entitlement Tahap Ini]

    S14 --> S15[Tampilkan List Item + Ukuran Expected]
    S15 --> S16[Checklist Item]
    S16 --> S17[Edit Actual Size Jika Perlu]
    S17 --> S17A[Log Perubahan: Staff, Old Size, New Size]

    S17A --> S18[Validasi Qty & Stock]

    S18 --> S19{Stok Cukup?}
    S19 -->|Tidak| S20{Partial Pickup?}
    S20 -->|Ya, Kasih Sebagian| S21[Catat Qty Sebagian]
    S20 -->|Tidak| S22[Batal]
    S22 --> S16

    S19 -->|Ya| S21
    S21 --> S23[Submit Pengambilan]

    S23 --> S24[Simpan Distribution Transaction + Stage]
    S24 --> S25[Simpan Distribution Items]
    S25 --> S26[Stock Movement OUT]
    S26 --> S27[Update Stock Balance -]

    S27 --> E2([Logout])

    class S1,S4,S6,S7,S10,S14,S15,S16,S17,S17A,S18,S21,S23,S24,S25,S26,S27 staff
    class S2,S5,S8,S12,S19,S20 decision
    class S3,S9,S13,S22 error
    class E2 startEnd
    class S11 process
    class S10A,S10B stage
```

---

## 5.4 Flow Admin Admin

```mermaid
flowchart TD
    classDef finance fill:#2563eb,color:#fff,stroke:#1d4ed8,stroke-width:2px
    classDef decision fill:#fbbf24,color:#000,stroke:#f59e0b,stroke-width:2px
    classDef error fill:#ef4444,color:#fff,stroke:#dc2626,stroke-width:2px
    classDef startEnd fill:#1e293b,color:#fff,stroke:#0f172a,stroke-width:2px
    classDef process fill:#3b82f6,color:#fff,stroke:#2563eb,stroke-width:1px
    classDef success fill:#22c55e,color:#fff,stroke:#16a34a,stroke-width:1px

    F1[Login Admin] --> F2{Akun Valid?}
    F2 -->|Tidak| F3[Error Login]
    F3 --> F1
    F2 -->|Ya| F4[Dashboard Admin]

    F4 --> F5[Import Data Mahasiswa]
    F5 --> F6[Upload Excel]
    F6 --> F7[Validasi Data]
    F7 --> F8[Preview Hasil]
    F8 --> F9{Konfirmasi?}
    F9 -->|Tidak| F10[Batal & Log Error]
    F9 -->|Ya| F11[Commit ke Database]
    F11 --> F12[Simpan Import Log]

    F4 --> F13[Import Eligible Payment]
    F13 --> F14[Upload Validasi Preview Commit Log]

    F4 --> F15[Kelola Master Data]
    F15 --> F15A[Fakultas, Prodi, Level]
    F15 --> F15B[Item, Kategori, Size, Variant]

    F4 --> F15C[Kelola Distribution Stages]
    F15C --> F15D[Buat Tahap: Nama, Periode, Tanggal, Item]
    F15D --> F15E[Simpan Stage]

    F4 --> F16[Create Entitlement]
    F16 --> F16A[Pilih Stage]
    F16A --> F17[Set: Prodi + Level + Period + Student Type]
    F17 --> F18[Atur Item & Qty per Stage]
    F18 --> F19[Simpan Entitlement]

    F4 --> F20[Generate Akun Mahasiswa]
    F20 --> F21[Buat: Username=NIM, Password=Random 12 Char]
    F21 --> F22[Isi Email Kampus]
    F22 --> F23[Kirim Kredensial ke Email Pribadi]

    F4 --> F24[Input Barang Masuk]
    F24 --> F25[Pilih Vendor & Item]
    F25 --> F26[Input Qty & Detail]
    F26 --> F27[Simpan Stock Receive]
    F27 --> F28[Stock Movement IN]
    F28 --> F29[Update Stock Balance +]

    F4 --> F30[Buat Jadwal Distribusi]
    F30 --> F30A[Pilih Stage Aktif]
    F30A --> F30B[System Tampilkan Stok Per Item & Size]
    F30B --> F30C[Pilih Item untuk Jadwal Ini]
    F30C --> F30D[Isi Info: Nama, Lokasi, Tanggal, Jam]
    F30D --> F31[Simpan Schedule + Schedule Items]
    F31 --> F32[System Cari Eligible + Email Kampus]
    F32 --> F33{Sudah Kirim?}
    F33 -->|Ya, Skip| F34[Anti Duplikat]
    F33 -->|Tidak, Kirim| F35[Kirim Notifikasi ke Email Kampus]

    F4 --> F36[Create Distribution Schedule per Stage]
    F36 --> F37[Jadwal Aktif]

    F4 --> F38[Monitor Perubahan Ukuran]
    F38 --> F38A[Lihat Log: Student, From, To, Staff, Date]

    F4 --> F39[Monitor Distribution]
    F39 --> F40[Lihat Report per Stage]
    F40 --> F41[Export Excel: Distribusi & Stock]

    F12 --> E3([Logout])
    F14 --> E3
    F15A --> E3
    F15B --> E3
    F15E --> E3
    F19 --> E3
    F23 --> E3
    F29 --> E3
    F34 --> E3
    F35 --> E3
    F37 --> E3
    F38A --> E3
    F41 --> E3

    class F1,F4,F5,F6,F7,F8,F11,F12,F13,F14,F15,F15A,F15B,F15C,F15D,F15E,F16,F16A,F17,F18,F19,F20,F21,F22,F23,F24,F25,F26,F27,F28,F29,F30,F30A,F30B,F30C,F30D,F31,F32,F35,F36,F37,F38,F38A,F39,F40,F41 finance
    class F2,F9,F33 decision
    class F3,F10,F34 error
    class E3 startEnd
    class F23,F35 success
```

---

## 5.5 Flow Super Admin

```mermaid
flowchart TD
    classDef superAdmin fill:#7c3aed,color:#fff,stroke:#5b21b6,stroke-width:2px
    classDef decision fill:#fbbf24,color:#000,stroke:#f59e0b,stroke-width:2px
    classDef error fill:#ef4444,color:#fff,stroke:#dc2626,stroke-width:2px
    classDef startEnd fill:#1e293b,color:#fff,stroke:#0f172a,stroke-width:2px
    classDef process fill:#8b5cf6,color:#fff,stroke:#7c3aed,stroke-width:1px

    A1[Login Super Admin] --> A2{Akun Valid?}
    A2 -->|Tidak| A3[Error Login]
    A3 --> A1
    A2 -->|Ya| A4[Dashboard Super Admin]

    A4 --> A5[Manage User]
    A5 --> A6[CRUD User, Role, Permission]

    A4 --> A7[System Config]
    A7 --> A8[Setting Sistem, Maintenance Mode]

    A4 --> A9[Audit Log]
    A9 --> A10[Filter & Export Log]

    A4 --> A11[Backup Database]
    A11 --> A12[Download / Restore]

    A4 --> A13[Monitoring Semua Modul]

    A6 --> E4([Logout])
    A8 --> E4
    A10 --> E4
    A12 --> E4
    A13 --> E4

    class A1,A4,A5,A6,A7,A8,A9,A10,A11,A12,A13 superAdmin
    class A2 decision
    class A3 error
    class E4 startEnd
```

---

## 5.6 Koneksi Antar Role

```mermaid
flowchart LR
    classDef finance fill:#2563eb,color:#fff,stroke:#1d4ed8,stroke-width:2px
    classDef staff fill:#ea580c,color:#fff,stroke:#c2410c,stroke-width:2px
    classDef student fill:#16a34a,color:#fff,stroke:#15803d,stroke-width:2px
    classDef superAdmin fill:#7c3aed,color:#fff,stroke:#5b21b6,stroke-width:2px

    FA[Admin] -->|Entitlement| ST[Staff]
    FA -->|Generate Akun| SU[Student]
    FA -->|Email Kampus| SU
    FA -->|Notif Jadwal| SU
    ST -->|Transaksi Distribusi| FA
    SA[Super Admin] -.->|Monitor| FA
    SA -.->|Monitor| ST
    SA -.->|Monitor| SU

    class FA finance
    class ST staff
    class SU student
    class SA superAdmin
```

---

## 6. Penjelasan Flowchart

### 6.1 Alur Mahasiswa

| Langkah | Detail |
|---------|--------|
| Login | Username = NIM, Password = 12 char random dari Admin |
| Batas Login Gagal | Maks 3x, akun terkunci 15 menit |
| First Login | Wajib ganti password |
| Dashboard | Info email, notifikasi, status, riwayat |
| Profile Lengkap | Data diri & ukuran seragam |
| Update Ukuran | Maksimal 1x |
| QR Token | Generate otomatis setelah data lengkap |
| Lupa Password | OTP 6 digit ke email kampus |

### 6.2 Alur Staff

| Langkah | Detail |
|---------|--------|
| Metode Cari | Scan QR atau Cari NIM (fallback) |
| Deteksi Jadwal | System deteksi jadwal aktif |
| Eligible | Cek status pembayaran |
| Actual Size | Staff bisa edit — dicatat log |
| Cek Stok | Validasi sebelum konfirmasi |
| Partial Pickup | Jika stok kurang |
| Transaksi | Simpan, kurangi stok, update balance |

### 6.3 Alur Admin

| Langkah | Detail |
|---------|--------|
| Import | Upload → Validasi → Preview → Commit → Log |
| Stock Receive | Input barang masuk dari vendor |
| Entitlement | Atur hak barang |
| Generate Akun | NIM + password random |
| Buat Jadwal | Pilih stage, item, lokasi |
| Notifikasi | Anti duplikat |
| Report | Export Excel per stage |

### 6.4 Alur Super Admin

| Langkah | Detail |
|---------|--------|
| Manage User | CRUD user, role & permission |
| System Config | Setting global |
| Audit Log | Pantau aktivitas |
| Backup | Backup & restore database |
| Monitoring | Pantau semua modul |
