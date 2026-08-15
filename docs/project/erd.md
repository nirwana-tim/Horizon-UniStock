# Database Design — ERD

> Diperbarui otomatis mengikuti skema aktual (8 tabel legacy `distribution_periods`, `distribution_stages`, `period_id`, `qr_token` sudah dihapus/diganti).

## Legend Relasi

| Simbol | Arti | Contoh |
|--------|------|--------|
| `||--||` | 1 : 1 | User ↔ Student |
| `||--o{` | 1 : M (zero or more) | Faculty → Study Programs |
| `||--|{` | 1 : M (one or more) | Entitlement → Entitlement Items |
| `}o--o{` | M : M | — |
| `}o--|{` | M : M (mandatory) | — |

## Tipe Data

| Tipe | Keterangan |
|------|-----------|
| `int` | Integer |
| `string` | Teks variable length |
| `text` | Teks panjang |
| `decimal` | Angka desimal |
| `boolean` | true/false |
| `date` | Tanggal saja |
| `datetime` | Tanggal + waktu |
| `json` | Data JSON |
| `FK` | Foreign Key |
| `PK` | Primary Key |
| `UK` | Unique Key |

---

## ERD Lengkap

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        string password
        timestamp email_verified_at
        timestamp last_login_at
        boolean must_change_password
        boolean is_active
    }

    FACULTIES {
        bigint id PK
        string name
        string code UK
    }

    STUDY_PROGRAMS {
        bigint id PK
        string name
        string code UK
        bigint faculty_id FK
    }

    STUDENT_GENERATIONS {
        bigint id PK
        string name
        string code UK
    }

    STUDENT_LEVELS {
        bigint id PK
        string kode UK
        string deskripsi
        string status
    }

    STUDENTS {
        bigint id PK
        bigint user_id FK
        string nim UK
        string name
        string email_kampus UK
        string email_pribadi
        bigint study_program_id FK
        bigint generation_id FK
        string student_level
        enum status
        string current_semester
        string entitlement_code
        timestamp email_verified_at
    }

    ITEM_CATEGORIES {
        bigint id PK
        string code UK
        string label
    }

    ITEM_TYPES {
        bigint id PK
        string code UK
        string label
    }

    ITEM_DEPARTMENTS {
        bigint id PK
        string code UK
        string label
    }

    ITEM_SIZES {
        bigint id PK
        string code UK
        string label
    }

    ITEMS {
        bigint id PK
        string name
        string code UK
        string base_code
        string gender
        bigint category_id FK
        bigint type_id FK
        bigint department_id FK
        string unit
        decimal selling_price
        decimal hpp
        boolean is_active
    }

    ITEM_VARIANTS {
        bigint id PK
        bigint item_id FK
        bigint size_id FK
        string size
        string size_label
        string sku UK
        decimal weight
    }

    CATEGORY_ITEM_SIZE {
        bigint item_category_id PK, FK
        bigint item_size_id PK, FK
    }

    ITEM_PRICES {
        bigint id PK
        bigint item_id FK
        decimal selling_price
        decimal hpp
        date effective_date
    }

    VENDORS {
        bigint id PK
        string name
        string email
        string contact
        string phone
    }

    ELIGIBILITY_RECORDS {
        bigint id PK
        bigint student_id FK
        boolean is_eligible
        string payment_status
    }

    STUDENT_SIZE_PROFILES {
        bigint id PK
        bigint student_id FK
        boolean is_filled
        datetime filled_at
        string baju_size
        string sepatu_size
    }

    STUDENT_SIZE_ITEMS {
        bigint id PK
        bigint size_profile_id FK
        bigint item_id FK
        string size
        int change_count
    }

    STUDENT_SIZE_HISTORIES {
        bigint id PK
        bigint size_item_id FK
        string old_size
        string new_size
        bigint changed_by FK
        datetime changed_at
    }

    SIZE_CHANGE_EVENTS {
        bigint id PK
        string title
        text description
        datetime start_date
        datetime end_date
        bigint faculty_id FK
        bigint study_program_id FK
        bigint generation_id FK
        string student_level
        int max_changes
        json baju_size_options
        json sepatu_size_options
        boolean is_active
        boolean allow_reedit
        bigint created_by FK
    }

    SIZE_EVENT_SUBMISSIONS {
        bigint id PK
        bigint student_id FK
        bigint event_id FK
        int submission_count
    }

    ENTITLEMENTS {
        bigint id PK
        string code
        string student_level
        string description
        boolean is_active
    }

    ENTITLEMENT_ITEMS {
        bigint id PK
        bigint entitlement_id FK
        bigint item_id FK
        int quantity
    }

    DISTRIBUTION_SCHEDULES {
        bigint id PK
        string name
        string period
        string student_level
        date date
        string location
        string session
        bigint generation_id FK
        bigint faculty_id FK
        bigint study_program_id FK
        boolean is_active
    }

    DIST_SCHEDULE_ITEMS {
        bigint id PK
        bigint schedule_id FK
        bigint item_id FK
    }

    DISTRIBUTION_TRANSACTIONS {
        bigint id PK
        bigint student_id FK
        bigint schedule_id FK
        bigint staff_id FK
        enum status
        datetime pickup_time
        text notes
    }

    DISTRIBUTION_ITEMS {
        bigint id PK
        bigint transaction_id FK
        bigint item_id FK
        bigint variant_id FK
        string expected_size
        string actual_size
        int quantity
        decimal hpp
        decimal selling_price_at_distribution
        decimal unit_price
    }

    STOCK_RECEIVES {
        bigint id PK
        string reference_number UK
        bigint vendor_id FK
        date receive_date
        enum status
        text notes
    }

    STOCK_RECEIVE_ITEMS {
        bigint id PK
        bigint stock_receive_id FK
        bigint item_id FK
        bigint variant_id FK
        int quantity
        decimal unit_price
        decimal hpp
    }

    STOCK_BATCHES {
        bigint id PK
        bigint item_id FK
        bigint variant_id FK
        int quantity_remaining
        decimal unit_hpp
        date received_date
        bigint stock_receive_item_id FK
    }

    STOCK_MOVEMENTS {
        bigint id PK
        bigint item_id FK
        bigint variant_id FK
        enum type
        int quantity
        decimal hpp
        bigint stock_batch_id FK
        string reference_type
        bigint reference_id
        text notes
    }

    STOCK_BALANCES {
        bigint id PK
        bigint item_id FK
        bigint variant_id FK
        int quantity
        int reserved
        decimal last_hpp
    }

    IMPORT_BATCHES {
        bigint id PK
        string import_type
        string file_name
        int total_rows
        int success_rows
        int failed_rows
        enum status
        json error_log
        bigint imported_by FK
    }

    EMAIL_NOTIFICATIONS {
        bigint id PK
        bigint student_id FK
        bigint schedule_id FK
        string type
        string status
        datetime sent_at
        text error_message
    }

    AUDIT_LOGS {
        bigint id PK
        bigint user_id FK
        string action
        string model_type
        bigint model_id
        json old_values
        json new_values
        string ip_address
    }

    OTP_CODES {
        bigint id PK
        bigint user_id FK
        string nim
        string email
        string code
        string type
        datetime expires_at
        datetime used_at
    }

    SMTP_SETTINGS {
        bigint id PK
        string mailer
        enum scheme
        string host
        int port
        string username
        text password
        boolean verify_peer
        text api_key
        string from_address
        string from_name
        boolean is_active
        bigint created_by FK
    }

    DOCUMENT_SEQUENCES {
        bigint id PK
        string type
        string period
        int value
    }

    STUDENT_SUMMARIES {
        bigint id PK
        bigint student_id FK UK
        int total_transactions
        int total_items_received
        decimal total_spend
        datetime last_distribution_at
        datetime last_calculated_at
    }

    STOCK_OPNAMES {
        bigint id PK
        string reference_number UK
        date opname_date
        string period
        text notes
        enum status
        bigint created_by FK
    }

    STOCK_OPNAME_ITEMS {
        bigint id PK
        bigint stock_opname_id FK
        bigint item_id FK
        bigint variant_id FK
        int system_quantity
        int physical_quantity
        int computed_variance
        text notes
    }

    STOCK_OPNAME_ADJUSTMENTS {
        bigint id PK
        bigint stock_opname_id FK
        bigint stock_movement_id FK
        enum type
        int quantity
        text reason
        bigint approved_by FK
        datetime approved_at
    }

    USERS ||--o| STUDENTS : "fk.user_id -> id"
    FACULTIES ||--o{ STUDY_PROGRAMS : "fk.faculty_id -> id"
    STUDY_PROGRAMS ||--o{ STUDENTS : "fk.study_program_id -> id"
    STUDENT_GENERATIONS ||--o{ STUDENTS : "fk.generation_id -> id"
    STUDENTS }o--|| STUDENT_LEVELS : "fk.student_level -> kode"
    ITEM_CATEGORIES ||--o{ ITEMS : "fk.category_id -> id"
    ITEM_TYPES ||--o{ ITEMS : "fk.type_id -> id"
    ITEM_DEPARTMENTS ||--o{ ITEMS : "fk.department_id -> id"
    ITEMS ||--o{ ITEM_VARIANTS : "fk.item_id -> id"
    ITEM_SIZES ||--o{ ITEM_VARIANTS : "fk.size_id -> id"
    ITEM_CATEGORIES ||--o{ CATEGORY_ITEM_SIZE : "fk.item_category_id -> id"
    ITEM_SIZES ||--o{ CATEGORY_ITEM_SIZE : "fk.item_size_id -> id"
    ITEMS ||--o{ ITEM_PRICES : "fk.item_id -> id"
    VENDORS ||--o{ STOCK_RECEIVES : "fk.vendor_id -> id"

    STUDENTS ||--o{ ELIGIBILITY_RECORDS : "fk.student_id -> id"
    STUDENTS ||--o| STUDENT_SIZE_PROFILES : "fk.student_id -> id"
    STUDENT_SIZE_PROFILES ||--o{ STUDENT_SIZE_ITEMS : "fk.size_profile_id -> id"
    STUDENT_SIZE_ITEMS }o--|| ITEMS : "fk.item_id -> id"
    STUDENT_SIZE_ITEMS ||--o{ STUDENT_SIZE_HISTORIES : "fk.size_item_id -> id"
    STUDENT_SIZE_HISTORIES }o--o| USERS : "fk.changed_by -> id"

    SIZE_CHANGE_EVENTS }o--o| FACULTIES : "fk.faculty_id -> id"
    SIZE_CHANGE_EVENTS }o--o| STUDY_PROGRAMS : "fk.study_program_id -> id"
    SIZE_CHANGE_EVENTS }o--o| STUDENT_GENERATIONS : "fk.generation_id -> id"
    SIZE_CHANGE_EVENTS }o--o| USERS : "fk.created_by -> id"
    SIZE_CHANGE_EVENTS ||--o{ SIZE_EVENT_SUBMISSIONS : "fk.event_id -> id"
    STUDENTS ||--o{ SIZE_EVENT_SUBMISSIONS : "fk.student_id -> id"

    ENTITLEMENTS ||--o{ ENTITLEMENT_ITEMS : "fk.entitlement_id -> id"
    ENTITLEMENT_ITEMS }o--|| ITEMS : "fk.item_id -> id"

    DISTRIBUTION_SCHEDULES ||--o{ DIST_SCHEDULE_ITEMS : "fk.schedule_id -> id"
    DIST_SCHEDULE_ITEMS }o--|| ITEMS : "fk.item_id -> id"
    DISTRIBUTION_SCHEDULES ||--o{ DISTRIBUTION_TRANSACTIONS : "fk.schedule_id -> id"
    STUDENTS ||--o{ DISTRIBUTION_TRANSACTIONS : "fk.student_id -> id"
    USERS ||--o{ DISTRIBUTION_TRANSACTIONS : "fk.staff_id -> id"
    DISTRIBUTION_TRANSACTIONS ||--o{ DISTRIBUTION_ITEMS : "fk.transaction_id -> id"
    DISTRIBUTION_ITEMS }o--|| ITEMS : "fk.item_id -> id"
    DISTRIBUTION_ITEMS }o--o| ITEM_VARIANTS : "fk.variant_id -> id"

    DISTRIBUTION_SCHEDULES ||--o{ EMAIL_NOTIFICATIONS : "fk.schedule_id -> id"
    EMAIL_NOTIFICATIONS }o--|| STUDENTS : "fk.student_id -> id"

    STOCK_RECEIVES ||--o{ STOCK_RECEIVE_ITEMS : "fk.stock_receive_id -> id"
    STOCK_RECEIVE_ITEMS }o--|| ITEMS : "fk.item_id -> id"
    STOCK_RECEIVE_ITEMS }o--|| ITEM_VARIANTS : "fk.variant_id -> id"
    STOCK_BATCHES }o--|| ITEMS : "fk.item_id -> id"
    STOCK_BATCHES }o--|| ITEM_VARIANTS : "fk.variant_id -> id"
    STOCK_BATCHES }o--o| STOCK_RECEIVE_ITEMS : "fk.stock_receive_item_id -> id"
    STOCK_MOVEMENTS }o--|| ITEMS : "fk.item_id -> id"
    STOCK_MOVEMENTS }o--|| ITEM_VARIANTS : "fk.variant_id -> id"
    STOCK_MOVEMENTS }o--o| STOCK_BATCHES : "fk.stock_batch_id -> id"
    STOCK_BALANCES }o--|| ITEMS : "fk.item_id -> id"
    STOCK_BALANCES }o--|| ITEM_VARIANTS : "fk.variant_id -> id"

    STOCK_OPNAMES ||--o{ STOCK_OPNAME_ITEMS : "fk.stock_opname_id -> id"
    STOCK_OPNAME_ITEMS }o--|| ITEMS : "fk.item_id -> id"
    STOCK_OPNAME_ITEMS }o--|| ITEM_VARIANTS : "fk.variant_id -> id"
    STOCK_OPNAMES ||--o{ STOCK_OPNAME_ADJUSTMENTS : "fk.stock_opname_id -> id"
    STOCK_OPNAME_ADJUSTMENTS }o--o| STOCK_MOVEMENTS : "fk.stock_movement_id -> id"

    IMPORT_BATCHES }o--|| USERS : "fk.imported_by -> id"
    AUDIT_LOGS }o--o| USERS : "fk.user_id -> id"
    OTP_CODES }o--o| USERS : "fk.user_id -> id"
    SMTP_SETTINGS }o--o| USERS : "fk.created_by -> id"
    STUDENT_SUMMARIES ||--|| STUDENTS : "fk.student_id -> id"
```

---

## Detail Tabel

### `users`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik user |
| `name` | string | Nama lengkap |
| `email` | string (UK) | Email login |
| `password` | string | Password ter-hash (bcrypt) |
| `email_verified_at` | datetime | Waktu email terverifikasi |
| `last_login_at` | datetime | Waktu login terakhir |
| `must_change_password` | boolean | Wajib ganti password (default false) |
| `is_active` | boolean | Status aktif (default true) |
| `remember_token` | string | Token "remember me" |
| `created_at` / `updated_at` | datetime | Timestamp |

### `faculties`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `name` | string | Nama fakultas |
| `code` | string (UK) | Kode fakultas (FKIP, FEB) |
| `deleted_at` | datetime | Soft delete |

### `study_programs`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `name` | string | Nama program studi |
| `code` | string (UK) | Kode prodi |
| `faculty_id` | bigint (FK) | Fakultas induk |
| `deleted_at` | datetime | Soft delete |

### `student_generations`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `name` | string | Nama generasi (Semester 1, Angkatan 2024) |
| `code` | string (UK) | Kode generasi |
| `deleted_at` | datetime | Soft delete |

### `student_levels`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `kode` | string (UK) | Identifier internal (`Y1S1`, `Y1S2`, ...) |
| `deskripsi` | string | Deskripsi level |
| `status` | string | Status opsional |

### `students`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `user_id` | bigint (FK, nullable) | Relasi ke akun login |
| `nim` | string (UK) | Nomor Induk Mahasiswa |
| `name` | string | Nama lengkap |
| `email_kampus` | string (UK, nullable) | Email @krw.horizon.ac.id |
| `email_pribadi` | string | Email pribadi |
| `study_program_id` | bigint (FK) | Program studi |
| `generation_id` | bigint (FK) | Generasi |
| `student_level` | string (default `Y1S1`) | Level mahasiswa (ref `student_levels.kode`) |
| `status` | enum | `active` / `leave` / `graduated` / `non_active` |
| `current_semester` | string | Semester berjalan (default `Y1S1`) |
| `entitlement_code` | string (nullable) | Kode entitlement aktif |
| `email_verified_at` | datetime | Waktu verifikasi email |
| `deleted_at` | datetime | Soft delete |

### `item_categories`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `code` | string (UK, 3) | Kode kategori (`UNF`, `UNM`, `UWK`) |
| `label` | string | Nama kategori |
| `deleted_at` | datetime | Soft delete |

### `item_types`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `code` | string (UK, 3) | Kode tipe (`SCB`, `PTS`) |
| `label` | string | Nama tipe |
| `deleted_at` | datetime | Soft delete |

### `item_departments`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `code` | string (UK, 2) | Kode departemen (`01`) |
| `label` | string | Nama departemen |
| `deleted_at` | datetime | Soft delete |

### `item_sizes`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `code` | string (UK, 10) | Kode ukuran (`S`, `M`, `L`, `XL`, `40`, `42`) |
| `label` | string | Label ukuran |
| `deleted_at` | datetime | Soft delete |

### `items`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `name` | string | Nama item |
| `code` | string (UK) | Kode item = `base_code` (4 segmen: `KATEGORI-GENDER-TIPE-VARIANT`, contoh `UNF-U-ALM-10`) |
| `base_code` | string (index) | Kode dasar, sama dengan `code` (4 segmen) |
| `gender` | char | L / P / U |
| `category_id` | bigint (FK) | Kategori item |
| `type_id` | bigint (FK, nullable) | Tipe item |
| `department_id` | bigint (FK, nullable) | Departemen item |
| `unit` | string | Satuan (pcs, pasang, set) |
| `selling_price` | decimal(15,2) | Harga jual |
| `hpp` | decimal(15,2) | HPP |
| `is_active` | boolean | Status aktif |
| `deleted_at` | datetime | Soft delete |

> Format kode barang: `KATEGORI-GENDER-TIPE-VARIANT` (4 segmen). SKU varian = `code` + `-SIZE` (5 segmen) di tabel `item_variants`. Detail lengkap: [item-code.md](item-code.md).

### `item_variants`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `item_id` | bigint (FK) | Item induk |
| `size_id` | bigint (FK, nullable) | Ref `item_sizes` |
| `size` | string | Ukuran (S, M, L, XL, 40, 42) |
| `size_label` | string | Label ukuran |
| `sku` | string (UK) | Stock Keeping Unit (`code-SIZE`, contoh `UNF-U-ALM-10-M`) |
| `weight` | decimal(8,2) | Berat item (opsional) |
| `deleted_at` | datetime | Soft delete |

### `category_item_size`

Pivot (M : M) antara `item_categories` dan `item_sizes` — ukuran yang tersedia per kategori.

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `item_category_id` | bigint (PK, FK) | Kategori (ref `item_categories.id`) |
| `item_size_id` | bigint (PK, FK) | Ukuran (ref `item_sizes.id`) |

> Primary key gabungan `(item_category_id, item_size_id)`.

### `item_prices`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `item_id` | bigint (FK) | Item terkait |
| `selling_price` | decimal(15,2) | Harga jual |
| `hpp` | decimal(15,2) | HPP |
| `effective_date` | date (nullable) | Tanggal efektif |

### `vendors`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `name` | string | Nama vendor |
| `email` | string | Email vendor |
| `contact` | string | Kontak person |
| `phone` | string | No telepon |
| `deleted_at` | datetime | Soft delete |

### `eligibility_records`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `student_id` | bigint (FK) | Mahasiswa terkait |
| `is_eligible` | boolean | Status kelayakan (default false) |
| `payment_status` | string | Status pembayaran (default `belum`) |

### `student_size_profiles`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `student_id` | bigint (FK) | Mahasiswa terkait |
| `is_filled` | boolean | Sudah isi ukuran? |
| `filled_at` | datetime | Waktu isi pertama |
| `baju_size` | string | Ukuran baju |
| `sepatu_size` | string | Ukuran sepatu |

### `student_size_items`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `size_profile_id` | bigint (FK) | Profil ukuran induk |
| `item_id` | bigint (FK) | Item yg dipilihkan ukuran |
| `size` | string | Ukuran yg dipilih |
| `change_count` | int | Jumlah perubahan |

### `student_size_histories`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `size_item_id` | bigint (FK) | Item ukuran terkait |
| `old_size` | string | Ukuran sebelum |
| `new_size` | string | Ukuran setelah |
| `changed_by` | bigint (FK, nullable) | Student=null, Staff=user_id |
| `changed_at` | datetime | Waktu perubahan |

### `size_change_events`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `title` | string | Judul event |
| `description` | text | Deskripsi |
| `start_date` | datetime | Mulai |
| `end_date` | datetime | Selesai |
| `faculty_id` | bigint (FK, nullable) | Filter fakultas |
| `study_program_id` | bigint (FK, nullable) | Filter prodi |
| `generation_id` | bigint (FK, nullable) | Filter generasi |
| `student_level` | string | Filter level |
| `max_changes` | tinyint | Maksimal perubahan (default 1) |
| `baju_size_options` | json | Pilihan ukuran baju |
| `sepatu_size_options` | json | Pilihan ukuran sepatu |
| `is_active` | boolean | Status aktif |
| `allow_reedit` | boolean | Izinkan edit ulang |
| `created_by` | bigint (FK) | Pembuat |

### `size_event_submissions`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `student_id` | bigint (FK) | Mahasiswa |
| `event_id` | bigint (FK) | Event |
| `submission_count` | tinyint | Jumlah submit |

### `entitlements`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `code` | string | Kode entitlement |
| `student_level` | string | Level mahasiswa target |
| `description` | string | Deskripsi hak barang |
| `is_active` | boolean | Status aktif |
| `deleted_at` | datetime | Soft delete |

### `entitlement_items`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `entitlement_id` | bigint (FK) | Entitlement induk |
| `item_id` | bigint (FK) | Item yang diberikan |
| `quantity` | int | Jumlah item |

### `distribution_schedules`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `name` | string | Nama jadwal |
| `period` | string (nullable) | Periode distribusi |
| `student_level` | string (nullable) | Level target |
| `date` | date | Tanggal distribusi |
| `location` | string | Lokasi distribusi |
| `session` | string | Sesi/jam |
| `generation_id` | bigint (FK, nullable) | Filter generasi |
| `faculty_id` | bigint (FK, nullable) | Filter fakultas |
| `study_program_id` | bigint (FK, nullable) | Filter prodi |
| `is_active` | boolean | Status aktif |
| `deleted_at` | datetime | Soft delete |

### `dist_schedule_items`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `schedule_id` | bigint (FK) | Jadwal distribusi |
| `item_id` | bigint (FK) | Item yang dibagikan |

### `distribution_transactions`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `student_id` | bigint (FK) | Mahasiswa |
| `schedule_id` | bigint (FK) | Jadwal |
| `staff_id` | bigint (FK) | Staff pelayanan |
| `status` | enum | `completed` / `partial` / `cancelled` |
| `pickup_time` | datetime | Waktu pengambilan |
| `notes` | text | Catatan |
| `deleted_at` | datetime | Soft delete |

> Unique index `(student_id, schedule_id)` — satu transaksi per mahasiswa per jadwal.

### `distribution_items`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `transaction_id` | bigint (FK) | Transaksi induk |
| `item_id` | bigint (FK) | Item yang diambil |
| `variant_id` | bigint (FK, nullable) | Varian/ukuran |
| `expected_size` | string | Ukuran input mahasiswa |
| `actual_size` | string | Ukuran yang diberikan |
| `quantity` | int | Jumlah |
| `hpp` | decimal(15,2) | HPP saat transaksi |
| `selling_price_at_distribution` | decimal(15,2) | Harga jual captured |
| `unit_price` | decimal(15,2) | Harga satuan |

### `stock_receives`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `reference_number` | string (UK) | No referensi (`SR-PERIODE-XXXX`) |
| `vendor_id` | bigint (FK) | Vendor |
| `receive_date` | date | Tanggal terima |
| `status` | enum | `pending` / `received` / `cancelled` |
| `notes` | text | Catatan |
| `deleted_at` | datetime | Soft delete |

### `stock_receive_items`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `stock_receive_id` | bigint (FK) | Penerimaan induk |
| `item_id` | bigint (FK) | Item |
| `variant_id` | bigint (FK) | Varian/ukuran |
| `quantity` | int | Jumlah |
| `unit_price` | decimal(15,2) | Harga satuan |
| `hpp` | decimal(15,2) | HPP per batch |

### `stock_batches`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `item_id` | bigint (FK) | Item |
| `variant_id` | bigint (FK) | Varian/ukuran |
| `quantity_remaining` | int | Sisa stok batch (FIFO) |
| `unit_hpp` | decimal(15,2) | HPP satuan |
| `received_date` | date | Tanggal terima |
| `stock_receive_item_id` | bigint (FK, nullable) | Sumber receive item |
| `deleted_at` | datetime | Soft delete |

### `stock_movements`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `item_id` | bigint (FK) | Item |
| `variant_id` | bigint (FK) | Varian/ukuran |
| `type` | enum | `IN` / `OUT` |
| `quantity` | int | Jumlah |
| `hpp` | decimal(15,2) | HPP saat transaksi |
| `stock_batch_id` | bigint (FK, nullable) | Batch FIFO |
| `reference_type` | string | `stock_receive` / `distribution` / dll |
| `reference_id` | bigint | ID referensi |
| `notes` | text | Catatan |
| `deleted_at` | datetime | Soft delete |

### `stock_balances`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `item_id` | bigint (FK) | Item |
| `variant_id` | bigint (FK) | Varian/ukuran |
| `quantity` | int | Saldo stok |
| `reserved` | int | Stok di-reserve |
| `last_hpp` | decimal(15,2) | HPP terakhir |

### `stock_opnames`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `reference_number` | string (UK) | No referensi (`SO-PERIODE-XXXX`) |
| `opname_date` | date | Tanggal opname |
| `period` | string | Periode ("2026/2027") |
| `notes` | text | Catatan |
| `status` | enum | `draft` / `counted` / `approved` |
| `created_by` | bigint (FK) | Pembuat batch |

### `stock_opname_items`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `stock_opname_id` | bigint (FK) | Batch opname |
| `item_id` | bigint (FK) | Item |
| `variant_id` | bigint (FK) | Varian/ukuran |
| `system_quantity` | int | Stok sistem |
| `physical_quantity` | int | Stok fisik |
| `computed_variance` | int | Selisih (VIRTUAL GENERATED) |
| `notes` | text | Catatan |

### `stock_opname_adjustments`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `stock_opname_id` | bigint (FK) | Batch opname |
| `stock_movement_id` | bigint (FK, nullable) | Stock movement |
| `type` | enum | `surplus` / `shortage` |
| `quantity` | int | Jumlah adjustment |
| `reason` | text | Alasan |
| `approved_by` | bigint (FK, nullable) | Approver |
| `approved_at` | datetime | Waktu approve |

### `import_batches`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `import_type` | string | students / eligible / items / item_prices / stock_receive |
| `file_name` | string | Nama file |
| `total_rows` | int | Total baris |
| `success_rows` | int | Berhasil |
| `failed_rows` | int | Gagal |
| `status` | enum | `processing` / `completed` / `failed` |
| `error_log` | json | Log error per baris |
| `imported_by` | bigint (FK) | User pengimport |

### `email_notifications`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `student_id` | bigint (FK) | Penerima |
| `schedule_id` | bigint (FK, nullable) | Jadwal terkait |
| `type` | string | event_invite / credentials / password_reset |
| `status` | string | pending / sent / failed |
| `sent_at` | datetime | Waktu terkirim |
| `error_message` | text | Error jika gagal |

### `audit_logs`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `user_id` | bigint (FK, nullable) | User pelaku |
| `action` | string | create / update / delete / activate / get_password / dll |
| `model_type` | string | Model terpengaruh |
| `model_id` | bigint | ID model |
| `old_values` | json | Data sebelum |
| `new_values` | json | Data setelah |
| `ip_address` | string(45) | IP address |

### `otp_codes`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `user_id` | bigint (FK, nullable) | User terkait |
| `nim` | string (nullable) | NIM pemohon |
| `email` | string | Email tujuan |
| `code` | string(64) | Hash kode OTP |
| `type` | string | password_reset / dll |
| `expires_at` | datetime | Kedaluwarsa |
| `used_at` | datetime | Waktu dipakai |

### `smtp_settings`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `mailer` | string | smtp / api / sendmail / log |
| `scheme` | enum | `tls` / `ssl` / `null` |
| `host` | string | Host SMTP |
| `port` | int | Port |
| `username` | string | Username |
| `password` | text | Password ter-encrypt |
| `verify_peer` | boolean | Nonaktifkan verifikasi SSL |
| `api_key` | text | API key ter-encrypt |
| `from_address` | string | Pengirim |
| `from_name` | string | Nama pengirim |
| `is_active` | boolean | Status aktif |
| `created_by` | bigint (FK) | Pembuat |

### `document_sequences`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `type` | string(4) | `SR` / `SO` |
| `period` | string | Periode ("2026/2027") |
| `value` | int | Nomor urut terakhir |

> Unique index `(type, period)` — dipakai `StockService::nextSequence()` dengan `LAST_INSERT_ID(value + 1)` (atomic).

### `student_summaries`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Identifier unik |
| `student_id` | bigint (FK, UK) | Mahasiswa (unik) |
| `total_transactions` | int | Total transaksi |
| `total_items_received` | int | Total barang diterima |
| `total_spend` | decimal(15,2) | Total pengeluaran |
| `last_distribution_at` | datetime | Distribusi terakhir |
| `last_calculated_at` | datetime | Terakhir dihitung |