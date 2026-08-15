# Alur Admin — Via GUI Web

Flowchart ini menggambarkan alur lengkap **Admin** saat menggunakan aplikasi melalui **GUI Web** (bukan import Excel).

---

```mermaid
flowchart TD
    classDef login fill:#1e293b,color:#fff,stroke:#0f172a
    classDef master fill:#3b82f6,color:#fff,stroke:#2563eb
    classDef student fill:#06b6d4,color:#fff,stroke:#0891b2
    classDef distribution fill:#f59e0b,color:#fff,stroke:#d97706
    classDef operation fill:#10b981,color:#fff,stroke:#059669
    classDef report fill:#ef4444,color:#fff,stroke:#dc2626

    A([Login Admin]) --> DASH[Dashboard Admin]
    class A login
    class DASH login

    %% ===== 1. MASTER DATA =====
    DASH --> M[Master Data]
    class M master

    M --> M_ACA[Academic Side]
    M --> M_IT[Item Side]
    M --> M_VEN[Vendor - Independent]

    M_ACA --> M_FAC[1. Faculty]
    M_FAC --> M_SP[2. Study Program]
    M_SP --> M_SP2(pilih Faculty)
    M_FAC --> M_GEN[3. Student Generation]
    M_ACA --> M_LVL[Student Level - read only]

    M_IT --> M_CAT[4. Item Category]
    M_CAT --> M_TYPE[5. Item Type]
    M_CAT --> M_SIZE[6. Item Size]
    M_CAT --> M_DEPT[7. Item Department]

    M_TYPE & M_SIZE & M_DEPT --> M_ITEM[8. Item - isi form]
    M_ITEM --> M_CODE[Auto-generate code 4 segmen]
    M_CODE --> M_VAR[9. Variant size lain]

    %% ===== 2. STUDENT =====
    DASH --> S[Student Management]
    class S student

    S --> S_CREATE[10. Buat Student]
    S_CREATE --> S_FORM[Form: NIM, Nama, Email, Prodi, Generasi, Student Level]
    S_FORM --> S_ENT[Set Entitlement via halaman student]
    S_ENT --> S_GEN[11. Generate Akun]
    S_GEN --> S_PROC[System buat User + password random]
    S_PROC --> S_PASS[Password tampil 1x]

    %% ===== 3. DISTRIBUTION SETUP =====
    DASH --> D[Distribution Setup]
    class D distribution

    D --> D_ENT[12. Entitlement]
    D_ENT --> D_ENT_F[Pilih Student Level]
    D_ENT_F --> D_ENT_C[Isi code + deskripsi]
    D_ENT_C --> D_ENT_I[Centang item + qty]
    D_ENT_I --> D_ENT_OK[Entitlement siap]

    D --> D_ELIG[13. Eligibility]
    D_ELIG --> D_TOGGLE[Toggle per student]

    %% ===== 4. STOCK =====
    DASH --> O[Stock & Inventory]
    class O operation

    O --> O_SR[14. Stock Receive]
    O_SR --> O_VEN[Pilih Vendor]
    O_VEN --> O_ITEM[Tambah Item]
    O_ITEM --> O_SIZE[Pilih Size]
    O_SIZE --> O_QTY[Isi Qty + Harga]
    O_QTY --> O_SAVE[Simpan]
    O_SAVE --> O_MOVE[StockMovement IN]
    O_MOVE --> O_BAL[StockBalance +]

    %% ===== 5. JADWAL =====
    DASH --> J[Distribution Schedule]
    class J operation

    J --> J_NEW[15. Buat Jadwal]
    J_NEW --> J_PAR[Pilih Student Level + Prodi/Generasi]
    J_PAR --> J_ENT[System load items dari Entitlement]
    J_ENT --> J_ITEM[Centang item]
    J_ITEM --> J_DET[Isi Tanggal, Lokasi, Sesi]
    J_DET --> J_ACT[Aktifkan]

    %% ===== 6. STAFF EXECUTES =====
    J_ACT --> ST[Staff: Scan QR NIM / Cari NIM]
    class ST operation

    ST --> ST_DATA[Lihat data + ukuran]
    ST_DATA --> ST_CHECK[Centang item + edit size]
    ST_CHECK --> ST_STOK[Cek stok per size]
    ST_STOK --> ST_OK{Stok cukup?}
    ST_OK -->|Ya| ST_SUBMIT[Submit distribusi]
    ST_OK -->|Tidak| ST_PART{Partial pickup?}
    ST_PART -->|Ya| ST_QTY[Catat qty sebagian]
    ST_QTY --> ST_SUBMIT
    ST_PART -->|Tidak| ST_CANCEL[Batal]
    ST_SUBMIT --> ST_MOVE[StockMovement OUT]
    ST_MOVE --> ST_BAL[StockBalance -]
    ST_BAL --> ST_DONE[Selesai]

    %% ===== 7. REPORT =====
    DASH --> R[Monitoring & Report]
    class R report

    R --> R1[Laporan Distribusi]
    R --> R2[Laporan Stok]
    R --> R3[Kartu Stok]
    R --> R4[GPM / Laba Rugi]
    R --> R5[Rekap Ukuran]
    R --> R6[Saldo Stok]
    R --> R7[Mutasi Stok]
    R --> R8[Log Ukuran]
    R --> R9[Stock Opname]
```

---

## Urutan Pengerjaan (Topological Order)

| Level | Yang Dikerjakan | Routes |
|-------|----------------|--------|
| **0** | Faculty, Student Generation, Student Level (read-only), Item Category, Vendor | `master-data/*` |
| **1** | Study Program (butuh Faculty), Item Type (butuh Category), Item Size (butuh Category), Item Department | `master-data/*` |
| **2** | Item (butuh Category+Type+Dept+Size), Item Variant (butuh Item) | `master-data/item/*` |
| **3** | Student (butuh Prodi+Level+Generasi), Entitlement (butuh Item+Student Level) | `student/students-data/*`, `distribution/entitlement/*` |
| **4** | Stock Receive (butuh Vendor+Item+Variant), Eligibility (butuh Student) | `inventory/stock-receive/*`, `finance/eligibility` |
| **5** | Distribution Schedule (butuh Entitlement+Items) | `distribution/distribution-schedule/*` |
| **6** | **Staff** melakukan distribusi (scan/cari NIM) | `distribution/scan` |
| **7** | **Admin** monitor via Reports | `report/*` |

---

## Catatan Penting

1. **Item Code** diisi manual 4 segmen: `KATEGORI-GENDER-TIPE-VARIANT` (contoh: `UNF-L-SCB-02`). SKU varian = `code-SIZE` (5 segmen). Lihat `docs/project/item-code.md`.
2. **Entitlement Code** diisi manual (unique, max 50). Entitlement di-*match* ke student via `student.entitlement_code = entitlement.code` + `entitlement.student_level` (atau `student_level` null = berlaku semua level).
3. **Entitlement** dikelola di `distribution/entitlement/*` (bukan master-data) dan hanya bisa dibuat oleh `super_admin`/`admin`.
4. **Stock Balance** bertambah saat Stock Receive, berkurang saat Staff submit distribusi.
5. **Distribution Schedule** mengambil items dari Entitlement yang cocok (per student level + prodi/generasi).
6. **Password student** random 12 karakter, muncul 1x di flash message, student wajib ganti saat first login (`must_change_password`).
7. Semua route master/inventory/report berada di middleware `role:super_admin|admin`; route scan juga mengizinkan `staff`.