# Alur Admin (Finance) — Via GUI Web

Flowchart ini menggambarkan alur lengkap **Admin/Finance** saat menggunakan aplikasi melalui **GUI Web** (bukan import Excel).

---

```mermaid
flowchart TD
    classDef login fill:#1e293b,color:#fff,stroke:#0f172a
    classDef master fill:#3b82f6,color:#fff,stroke:#2563eb
    classDef student fill:#06b6d4,color:#fff,stroke:#0891b2
    classDef distribution fill:#f59e0b,color:#fff,stroke:#d97706
    classDef operation fill:#10b981,color:#fff,stroke:#059669
    classDef report fill:#ef4444,color:#fff,stroke:#dc2626

    A([Login Admin Finance]) --> DASH[Dashboard Admin]
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
    M_FAC --> M_PL[3. Program Level]

    M_IT --> M_CAT[4. Item Category]
    M_CAT --> M_TYPE[5. Item Type]
    M_CAT --> M_SIZE[6. Item Size]
    M_CAT --> M_DEPT[7. Item Department]

    M_TYPE & M_SIZE & M_DEPT --> M_ITEM[8. Item - isi form]
    M_ITEM --> M_CODE[Auto-generate code]
    M_CODE --> M_VAR[9. Variant size lain]

    %% ===== 2. STUDENT =====
    DASH --> S[Student Management]
    class S student

    S --> S_CREATE[10. Buat Student]
    S_CREATE --> S_FORM[Form: NIM, Nama, Prodi, Level]
    S_FORM --> S_CODE[Auto-set entitlement_code]
    S_CODE --> S_GEN[11. Generate Akun]
    S_GEN --> S_PROC[System buat User + password random]
    S_PROC --> S_PASS[Password tampil 1x]

    %% ===== 3. DISTRIBUTION SETUP =====
    DASH --> D[Distribution Setup]
    class D distribution

    D --> D_STAGE[12. Distribution Stage]
    D --> D_ENT[13. Entitlement]
    D_ENT --> D_ENT_F[Pilih Prodi + Level]
    D_ENT_F --> D_ENT_C[Auto-generate code]
    D_ENT_C --> D_ENT_I[Centang item + qty]
    D_ENT_I --> D_ENT_OK[Entitlement siap]

    D --> D_ELIG[14. Eligibility]
    D_ELIG --> D_TOGGLE[Toggle per student]

    %% ===== 4. STOCK =====
    DASH --> O[Stock & Inventory]
    class O operation

    O --> O_SR[15. Stock Receive]
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

    J --> J_NEW[16. Buat Jadwal]
    J_NEW --> J_STG[Pilih Stage]
    J_STG --> J_PAR[Pilih Prodi + Level]
    J_PAR --> J_ENT[System load items dari Entitlement]
    J_ENT --> J_ITEM[Centang item]
    J_ITEM --> J_DET[Isi Tanggal, Lokasi, Jam]
    J_DET --> J_ACT[Aktifkan]

    %% ===== 6. STAFF EXECUTES =====
    J_ACT --> ST[Staff: Scan QR / Cari NIM]
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
| **0** | Faculty, Program Level, Item Category, Vendor | `master-data/*` |
| **1** | Study Program (butuh Faculty), Item Type (butuh Category), Item Size (butuh Category), Item Department (butuh Faculty) | `master-data/*` |
| **2** | Item (butuh Category+Type+Dept+Size), Item Variant (butuh Item) | `master-data/item/*` |
| **3** | Student (butuh Prodi+Level), Distribution Stage, Entitlement (butuh Item+Prodi+Level) | `admin/students/*`, `distribution/*` |
| **4** | Stock Receive (butuh Vendor+Item+Variant), Eligibility (butuh Student) | `inventory/stock-receive/*`, `finance/eligibility` |
| **5** | Distribution Schedule (butuh Stage+Entitlement+Items) | `distribution/distribution-schedule/*` |
| **6** | **Staff** melakukan distribusi (scan/cari NIM) | `distribution/scan` |
| **7** | **Admin** monitor via Reports | `report/*` |

---

## Catatan Penting

1. **Item Code** auto-generate dari kombinasi: `CATEGORY-GENDER-TYPE-DEPT-SIZE`
2. **Entitlement Code** auto-generate dari: `LEVEL_CODE + FACULTY_CODE + PRODI_CODE`
3. **Student entitlement_code** auto-set saat create student berdasarkan Prodi + Level
4. **Stock Balance** bertambah saat Stock Receive, berkurang saat Staff submit distribusi
5. **Distribution Schedule** mengambil items dari Entitlement yang cocok (dicocokkan via code)
6. **Password student** random 12 karakter, muncul 1x di flash message, student wajib ganti saat first login
