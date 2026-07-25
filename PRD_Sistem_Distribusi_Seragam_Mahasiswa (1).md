# PRODUCT REQUIREMENTS DOCUMENT
## Sistem Manajemen & Distribusi Seragam Mahasiswa
*Dokumen Perbaikan (Revisi) — Modul Inventory, Harga, Entitlement, Distribusi & Akun Mahasiswa*

| | |
|---|---|
| **Nomor Dokumen** | PRD-UNIFORM-2026-01 |
| **Versi** | 1.0 |
| **Tanggal Penyusunan** | 25 Juli 2026 |
| **Status** | Draft — Menunggu Persetujuan (Sign-off) |
| **Target Kesiapan Fitur Kritis** | 28 Juli 2026 (kedatangan seragam) |
| **Sumber Masukan** | Catatan tertulis tim, transkrip rapat (voice-to-text), rangkuman analisis Gemini & ChatGPT |

---

## Daftar Isi

1. [Ringkasan Eksekutif](#1-ringkasan-eksekutif)
2. [Latar Belakang & Kondisi Saat Ini](#2-latar-belakang--kondisi-saat-ini)
3. [Tujuan](#3-tujuan)
4. [Ruang Lingkup](#4-ruang-lingkup)
5. [Kebutuhan Fungsional](#5-kebutuhan-fungsional)
6. [Model Data (Ringkasan Relasi)](#6-model-data-ringkasan-relasi)
7. [Skenario Khusus & Aturan Bisnis yang Perlu Disepakati](#7-skenario-khusus--aturan-bisnis-yang-perlu-disepakati)
8. [Fitur pada Tahap Trial / Pengembangan Lanjutan](#8-fitur-pada-tahap-trial--pengembangan-lanjutan)
9. [Keputusan yang Masih Diperlukan (Open Decisions)](#9-keputusan-yang-masih-diperlukan-open-decisions)
10. [Ringkasan Bug yang Ditemukan Saat Trial](#10-ringkasan-bug-yang-ditemukan-saat-trial)
11. [Prioritas & Rencana Implementasi](#11-prioritas--rencana-implementasi)
12. [Kriteria Penerimaan (Acceptance Criteria)](#12-kriteria-penerimaan-acceptance-criteria--fitur-prioritas-critical)
13. [Lampiran: Catatan Operasional Tambahan](#13-lampiran-catatan-operasional-tambahan)

---

## 1. Ringkasan Eksekutif

Dokumen ini merangkum seluruh temuan, revisi, dan kebutuhan perbaikan terhadap Sistem Manajemen & Distribusi Seragam Mahasiswa, berdasarkan catatan tertulis tim dan hasil rapat evaluasi (transkrip voice-to-text). Materi mentah tersebut sebelumnya telah dirapikan secara terpisah menggunakan dua alat bantu AI (Gemini dan ChatGPT); dokumen ini menyatukan, menstrukturkan ulang, dan menyelaraskan kedua hasil tersebut ke dalam satu Product Requirements Document (PRD) yang konsisten dan siap dijadikan acuan pengembangan.

Terdapat tenggat operasional penting: seragam fisik diperkirakan mulai diterima pada **28 Juli 2026**. Karena itu, modul Inventory (FIFO), Master Data Harga, dan validasi Stok menjadi prioritas kritis yang harus stabil sebelum tanggal tersebut, diikuti oleh modul Entitlement, Distribusi, dan Manajemen Akun.

**Cakupan revisi utama:**

- Penerapan metode FIFO pada penerimaan dan pengeluaran stok, dengan pemisahan logika harga per tahun.
- Pembuatan basis data riwayat harga beli/jual dan penambahan fitur edit harga.
- Penyederhanaan pengisian ukuran seragam dari basis per-SKU menjadi berbasis kategori (Baju, Sepatu, Merchandise).
- Penguatan modul Entitlement (kaitan Mahasiswa–Entitlement–Stock, status Locked, pencarian, rincian hak).
- Perbaikan alur akun mahasiswa (pengiriman kredensial via email pribadi vs. email kampus) dan halaman login.
- Perbaikan bug pada modul Distribusi (ganti ukuran, error scan, pencarian jadwal, sesi login bersamaan).

---

## 2. Latar Belakang & Kondisi Saat Ini

Sistem saat ini sudah dapat menjalankan alur dasar: pendaftaran data mahasiswa, pembuatan akun, pengisian ukuran seragam per SKU, pengaturan entitlement, penerimaan stok, dan penjadwalan distribusi. Namun, hasil pengujian (trial) dan diskusi tim menemukan sejumlah celah proses bisnis dan bug yang perlu diperbaiki sebelum sistem digunakan secara penuh pada periode pembagian seragam.

### 2.1 Masalah Utama yang Teridentifikasi

- Harga pembelian barang berfluktuasi dari waktu ke waktu, sementara sistem penerimaan stok belum memiliki mekanisme FIFO maupun riwayat harga per tahun, sehingga nilai stok dan HPP berisiko tidak akurat.
- Halaman Master Data → Item Price belum memiliki fitur edit harga.
- Mahasiswa diwajibkan memilih ukuran satu per satu untuk setiap SKU, padahal cukup dikelompokkan per kategori (Baju, Sepatu, Merchandise).
- Field Email Kampus bersifat wajib (mandatory) pada pendataan mahasiswa baru, padahal mahasiswa baru umumnya belum memiliki email kampus (baru tersedia menjelang P2KK).
- Belum ada fitur pencarian pada halaman Entitlement dan Add Schedule, menyulitkan admin memeriksa data satu per satu.
- Belum ada indikator batas stok (minimum/low/high) maupun peringatan otomatis ketika permintaan (demand) melebihi stok.
- Beberapa bug ditemukan saat trial: event ganti ukuran mahasiswa baru hanya bisa dilakukan 1 kali, pesan error yang membingungkan saat scan melewati batas waktu, item pada Entitlement tidak dapat diedit, serta satu akun tidak dapat digunakan oleh dua sesi login secara bersamaan.
- Belum ada kejelasan proses bisnis untuk kasus lintas tahun anggaran, misalnya mahasiswa yang berhak mengambil barang pada tahun tertentu namun stok tahun tersebut telah habis.

---

## 3. Tujuan

- Memastikan pencatatan stok dan harga akurat melalui penerapan metode FIFO serta riwayat harga tahunan yang terpisah dari logika pergerakan stok.
- Menyederhanakan dan mempercepat proses pengisian ukuran seragam oleh mahasiswa.
- Menjadikan modul Entitlement sebagai pusat kendali hak mahasiswa yang terhubung secara konsisten dengan data Mahasiswa dan Stock Balance.
- Meningkatkan efisiensi kerja admin melalui fitur pencarian pada Entitlement dan Add Schedule.
- Menghilangkan hambatan pembuatan akun mahasiswa baru akibat ketiadaan email kampus.
- Memperbaiki seluruh bug kritis yang ditemukan selama trial sebelum tanggal 28 Juli 2026.

---

## 4. Ruang Lingkup

### 4.1 Termasuk dalam Ruang Lingkup

- Modul Inventory: Stock Receive (FIFO), Stock (history/report).
- Modul Master Data: Item Price (riwayat & edit harga), Item Size (kategori ukuran).
- Modul Entitlement & Mahasiswa (Student Data).
- Modul Distribution: Schedule, Entitlement mapping, proses scan.
- Modul Akun & Autentikasi: pembuatan akun, halaman login, pengiriman kredensial.

### 4.2 Di Luar Ruang Lingkup Dokumen Ini

- Perancangan ulang infrastruktur hosting secara menyeluruh (hanya dicatat sebagai catatan operasional pada bagian Lampiran).
- Perubahan proses pembayaran/keuangan di luar keterkaitannya dengan status Entitlement (Locked).
- Desain ulang visual (UI/UX) di luar perubahan struktur formulir dan halaman yang disebutkan secara eksplisit.

---

## 5. Kebutuhan Fungsional

### 5.1 Modul Inventory — Penerapan FIFO
*Modul: `/inventory/stock-receive/create`*

- Setiap transaksi penerimaan barang (stock receive) disimpan sebagai batch tersendiri, lengkap dengan tanggal masuk dan harga beli pada saat itu.
- Barang keluar (distribusi) wajib mengambil stok dari batch dengan tanggal masuk paling awal (First-In-First-Out); apabila batch pertama habis, sistem otomatis melanjutkan ke batch berikutnya.
- Sistem tidak diperbolehkan mengambil batch yang lebih baru selama batch yang lebih lama masih tersedia.
- Perhitungan Harga Pokok Penjualan (HPP) mengikuti rumus: **HPP = Jumlah Stok × Harga Beli batch yang digunakan**.

**Contoh Ilustrasi**

| Batch | Qty Masuk | Harga Beli |
|---|---:|---:|
| Januari 2026 | 100 | Rp 90.000 |
| Februari 2026 | 200 | Rp 95.000 |

Jika terjadi distribusi sejumlah 120 pcs, sistem mengambil 100 pcs dari batch Januari dan sisanya 20 pcs dari batch Februari, masing-masing tercatat dengan harga belinya sendiri.

### 5.2 Master Data Harga — Item Price
*Modul: `/master-data/item-price`*

- Menambahkan fitur edit pada Harga Beli dan Harga Jual, yang saat ini belum tersedia (hanya bisa dibuat, belum bisa diubah).
- Harga disimpan dalam tabel riwayat harga terpisah dari tabel item utama, sehingga histori harga tidak hilang saat terjadi perubahan.
- Konsep harga dipisahkan dari konsep FIFO stok: FIFO mengatur pergerakan fisik barang, sedangkan harga berlaku secara tahunan (per tahun anggaran).
- Apabila admin tidak melakukan pembaruan harga pada tahun berjalan, sistem secara otomatis menggunakan harga tahun sebelumnya sebagai nilai berlaku.
- Apabila admin memperbarui harga, nilai baru berlaku untuk transaksi penerimaan stok mulai tahun berjalan, tanpa mengubah histori transaksi tahun-tahun sebelumnya.

**Struktur Tabel yang Diusulkan: `item_prices`**

| Field | Tipe / Keterangan |
|---|---|
| id | Primary key |
| item_id | Referensi ke master item |
| purchase_price | Harga beli berlaku |
| selling_price | Harga jual berlaku (jika ada penjualan) |
| year | Tahun anggaran berlakunya harga |
| effective_date | Tanggal harga mulai berlaku |
| created_by / updated_by | Audit trail perubahan harga |

### 5.3 Monitoring & Validasi Stok

- Menambahkan konfigurasi ambang batas stok per item: Minimum Stock, Low Stock, dan High Stock.
- Sistem menampilkan indikator status stok (mis. label/warna) berdasarkan posisi stok terhadap ambang batas tersebut.
- Sistem memberikan peringatan (trigger) otomatis apabila jumlah permintaan (demand) melebihi jumlah stok yang tersedia, dan proses distribusi terkait tidak dapat dilanjutkan sebelum kondisi ini ditindaklanjuti.
- Modul Stock yang tampil kepada mahasiswa bersifat read-only — hanya menampilkan riwayat/inventory (history) dan laporan; tidak memiliki fitur import maupun edit data.

### 5.4 Entitlement & Data Mahasiswa

- Relasi inti sistem terdiri dari tiga entitas: **Mahasiswa, Entitlement, dan Stock**. Stock Balance dihitung berdasarkan kombinasi data mahasiswa yang telah mengisi ukuran dan konfigurasi entitlement yang berlaku baginya.
- Contoh penerapan: mahasiswa Program Studi S1 Keperawatan memiliki entitlement atas Seragam Komunitas, Almamater, dan Seragam Kuliah. Saat entitlement diatur untuk "Seragam Komunitas S1 Keperawatan", kebutuhan stok almamater dihitung khusus dari jumlah mahasiswa S1 Keperawatan yang telah mengisi data ukuran.
- Halaman Entitlement menampilkan rincian item apa saja yang menjadi hak setiap kelompok mahasiswa/fakultas, tidak hanya data ringkasan.
- Menambahkan kolom pencarian pada Distribution → Entitlement (misalnya berdasarkan Fakultas, Program Studi, Angkatan, atau Kategori) agar admin tidak perlu memeriksa data satu per satu.
- Perbaikan bug: item pada Entitlement saat ini terkunci (tidak bisa diedit); ke depan item entitlement harus dapat ditambah, diubah, maupun dihapus oleh admin sesuai kebutuhan.

**Status Locked pada Entitlement**

- Selama entitlement belum berstatus Locked, perubahan (tambah/ubah/hapus hak) masih diperbolehkan dan tetap dapat disinkronkan dengan tagihan.
- Setelah mahasiswa melakukan pembayaran dan entitlement berstatus Locked, entitlement tersebut tidak dapat dibatalkan, dipindahkan, maupun dihapus, karena telah terhubung langsung dengan bukti pembayaran.
- Business rule ini perlu diimplementasikan sebagai validasi sistem, bukan hanya kesepakatan operasional, untuk mencegah inkonsistensi data pembayaran.

### 5.5 Penyederhanaan Pengisian Ukuran Seragam

- Pengisian ukuran oleh mahasiswa diubah dari basis per-SKU menjadi basis kategori: **Baju, Sepatu, dan Merchandise**.
- Setiap SKU dipetakan ke salah satu kategori pada Master Data → Item Size; ukuran yang dipilih mahasiswa pada suatu kategori otomatis berlaku untuk seluruh SKU dalam kategori tersebut.

| Kategori | Contoh Pilihan Ukuran |
|---|---|
| Baju | S, M, L, XL, XXL, XXXL |
| Sepatu | 34, 35, 36, …, 46 |
| Merchandise | All Size |

- Formulir pendataan (termasuk versi Google Form bila digunakan sebagai kanal alternatif) cukup memuat: Nama, Ukuran Baju, Ukuran Sepatu, dan Email (opsional).

### 5.6 Distribusi & Penjadwalan

- Menambahkan kolom pencarian pada halaman Add Schedule agar admin tidak perlu memeriksa daftar jadwal satu per satu.
- Field Date dan Session pada suatu event distribusi berfungsi sebagai patokan waktu pengiriman notifikasi massal (blast) kepada mahasiswa, bukan sebagai pembatas waktu pelaksanaan scan di lapangan.
- Batas waktu scan tidak dibatasi ketat per sesi; secara default dapat berlaku fleksibel hingga satu semester penuh, kecuali ditentukan lain oleh admin.
- Perbaikan bug: pesan error yang muncul saat scan dilakukan setelah waktu/sesi berakhir saat ini tidak informatif dan membingungkan pengguna; pesan perlu diperjelas agar mahasiswa/petugas memahami penyebabnya.
- Perbaikan bug: event ganti ukuran untuk mahasiswa baru saat ini hanya dapat dilakukan 1 kali dan menimbulkan error pada percobaan berikutnya; perlu disepakati aturan bisnis jumlah maksimal penggantian ukuran yang diizinkan, lalu diperbaiki agar sesuai aturan tersebut.
- Perbaikan mapping data pada modul Distribution (ID Student, SKU, Program, Item Department) yang saat ini belum sepenuhnya sesuai.
- Sistem tetap dapat digunakan untuk membuat jadwal distribusi jangka panjang (misalnya satu semester hingga satu tahun ke depan).

### 5.7 Manajemen Akun & Autentikasi

- Alur pembuatan akun tetap: data mahasiswa didaftarkan terlebih dahulu → akun digenerate → mahasiswa dapat login.
- Login mahasiswa menggunakan Student ID / NIM, bukan email. Label pada halaman login perlu disesuaikan dengan salah satu dari dua opsi (lihat bagian [9. Keputusan yang Masih Diperlukan](#9-keputusan-yang-masih-diperlukan-open-decisions)).
- Field Email Kampus tidak lagi bersifat wajib (mandatory) untuk mahasiswa baru (freshman), karena email kampus baru tersedia menjelang kegiatan P2KK.
- Kredensial akun mahasiswa baru (freshman) dikirim ke email pribadi yang didaftarkan, karena mereka belum memiliki email kampus.
- Kredensial akun mahasiswa lanjutan (continuing) tetap dikirim ke email kampus resmi, karena email kampus mereka sudah aktif.
- Setelah email kampus tersedia bagi mahasiswa baru, data akun dapat diperbarui agar email kampus tercatat sebagai identitas resmi.
- Perbaikan bug: satu akun (baik mahasiswa maupun staf) saat ini tidak dapat digunakan oleh dua sesi login secara bersamaan; pengguna harus menunggu sesi sebelumnya logout. Perlu dipastikan apakah pembatasan single-session ini merupakan kebijakan yang diinginkan atau keterbatasan teknis yang perlu diperbaiki.

---

## 6. Model Data (Ringkasan Relasi)

Alur relasi inti antar entitas utama sistem:

```
Mahasiswa  →  Entitlement  →  Stock Balance
```

- **Mahasiswa**: menyimpan identitas dan data ukuran seragam per kategori.
- **Entitlement**: menentukan hak/daftar item yang berlaku untuk kelompok mahasiswa tertentu (mis. berdasarkan program studi/fakultas), termasuk status Locked.
- **Item Price**: riwayat harga beli dan harga jual per item, per tahun.
- **Stock (Batch FIFO)**: saldo fisik barang per batch penerimaan, menjadi dasar perhitungan Stock Balance dan HPP.
- **Stock Balance** = agregasi kebutuhan/keluar-masuk stok, dihitung dari kombinasi data Mahasiswa yang memenuhi syarat entitlement tertentu.

---

## 7. Skenario Khusus & Aturan Bisnis yang Perlu Disepakati

### 7.1 Pengambilan Barang Lintas Tahun Anggaran

Skenario: mahasiswa berhak mengambil suatu item pada tahun berjalan, namun stok tahun tersebut telah habis, dan pengambilan baru dapat dilakukan pada tahun berikutnya (dengan kemungkinan harga beli yang berbeda).

- Stok tetap mengikuti urutan FIFO sesuai batch yang tersedia saat pengambilan dilakukan.
- Harga yang tercatat pada transaksi mengikuti harga batch FIFO yang benar-benar digunakan, bukan harga entitlement awal.
- Master harga (Item Price) yang berlaku mengikuti tahun saat transaksi benar-benar terjadi, kecuali diputuskan lain oleh kebijakan keuangan kampus.
- Perlu keputusan eksplisit dari pemilik proses (product owner/keuangan): apakah mahasiswa yang tertunda tetap dikenakan harga tahun entitlement awal, atau mengikuti harga tahun realisasi pengambilan.

### 7.2 Perhitungan HPP dan Perubahan Harga Antar Tahun

- HPP dihitung per batch: Stok × Harga Beli batch tersebut, sehingga nilai persediaan tetap akurat meskipun harga berubah dari tahun ke tahun.
- Histori transaksi lama tidak boleh berubah nilainya hanya karena ada pembaruan harga pada tahun berjalan.

---

## 8. Fitur pada Tahap Trial / Pengembangan Lanjutan

- Pengisian ukuran berdasarkan kategori (sepatu, baju, merchandise) — selaras dengan kebutuhan pada bagian 5.5.
- Item pada Entitlement saat ini tidak bisa diedit — selaras dengan perbaikan pada bagian 5.4.
- Pesan error saat scan dilakukan setelah waktu terlewat masih belum informatif — selaras dengan perbaikan pada bagian 5.6.
- Date dan Session pada event distribusi hanya digunakan untuk blast notifikasi, sedangkan batas scan di lapangan belum dibatasi ketat (dapat berlaku hingga satu semester) — selaras dengan bagian 5.6.

---

## 9. Keputusan yang Masih Diperlukan (Open Decisions)

Sebagian topik belum memiliki satu kesimpulan tunggal pada materi sumber (catatan tim, hasil Gemini, dan hasil ChatGPT berbeda dalam merekomendasikan opsi). Poin-poin berikut perlu diputuskan oleh pemilik produk sebelum development dimulai:

| Topik | Opsi A | Opsi B | Rekomendasi |
|---|---|---|---|
| Label halaman login | Ubah label "Email/NIM" menjadi "NIM" saja pada login mahasiswa | Pisahkan sepenuhnya menjadi dua halaman: Login Mahasiswa dan Login Staff | **Opsi B** — lebih jelas secara UX dan mencegah kekeliruan input di kemudian hari |
| Batas jumlah ganti ukuran (event khusus) | Dibatasi tetap 1 kali sesuai kondisi saat ini (namun perlu diperbaiki agar tidak error) | Diizinkan lebih dari 1 kali dengan batas maksimal yang disepakati (mis. 2–3 kali) | Perlu keputusan pemilik proses; dampak ke stok & entitlement harus dihitung ulang bila lebih dari 1 kali |
| Sesi login bersamaan (concurrency) | Pertahankan single-session (satu akun hanya bisa login di satu tempat) | Izinkan multi-session dengan pembatasan wajar | Perlu klarifikasi apakah ini kebijakan keamanan yang disengaja atau keterbatasan teknis |
| Harga pada pengambilan lintas tahun | Gunakan harga tahun entitlement awal | Gunakan harga tahun realisasi pengambilan (sesuai batch FIFO) | Perlu keputusan dari pihak keuangan kampus |

---

## 10. Ringkasan Bug yang Ditemukan Saat Trial

| No | Bug | Modul Terkait |
|---|---|---|
| 1 | Item pada Entitlement tidak dapat diedit (tambah/ubah/hapus). | Distribution → Entitlement |
| 2 | Event ganti ukuran mahasiswa baru hanya bisa dilakukan 1 kali, error pada percobaan berikutnya. | Distribution / Event |
| 3 | Pesan error tidak informatif saat scan dilakukan setelah waktu/sesi berakhir. | Distribution / Scan |
| 4 | Field Email Kampus mandatory untuk mahasiswa baru yang belum memiliki email kampus. | Student Data / Akun |
| 5 | Harga pada Item Price belum bisa diedit. | Master Data → Item Price |
| 6 | Satu akun tidak bisa digunakan pada dua sesi login bersamaan (mahasiswa & staf). | Autentikasi |
| 7 | Beberapa mapping data (ID Student, SKU, Program, Item Department) belum sesuai. | Distribution |

---

## 11. Prioritas & Rencana Implementasi

> **Target utama:** seluruh fitur berprioritas Critical harus siap diuji sebelum **28 Juli 2026**, mengingat estimasi seragam mulai diterima pada tanggal tersebut.

| Prioritas | Kebutuhan | Modul |
|---|---|---|
| 🔴 Critical | Implementasi FIFO pada penerimaan & pengeluaran stok | Inventory |
| 🔴 Critical | Tabel riwayat harga beli/jual per tahun + fitur edit harga | Master Data – Item Price |
| 🔴 Critical | Perhitungan HPP berbasis batch FIFO | Inventory |
| 🔴 Critical | Validasi demand vs. stock dengan trigger peringatan | Inventory / Stock |
| 🔴 Critical | Email kampus tidak mandatory untuk mahasiswa baru; kredensial via email pribadi | Akun & Autentikasi |
| 🔴 Critical | Pengisian ukuran berbasis kategori (Baju/Sepatu/Merchandise) | Student / Master Data – Item Size |
| 🟠 Menengah | Kolom pencarian pada Entitlement dan Add Schedule | Distribution – Entitlement / Schedule |
| 🟠 Menengah | Rincian hak item pada halaman Entitlement | Distribution – Entitlement |
| 🟠 Menengah | Konfigurasi Minimum/Low/High Stock | Inventory / Master Data |
| 🟠 Menengah | Revisi label / pemisahan halaman Login Mahasiswa & Staff | Autentikasi |
| 🟣 Bug Fix | Item Entitlement tidak bisa diedit | Distribution – Entitlement |
| 🟣 Bug Fix | Event ganti ukuran mahasiswa baru (batas 1 kali, error) | Distribution / Event |
| 🟣 Bug Fix | Pesan error saat scan melewati batas waktu | Distribution / Scan |
| 🟣 Bug Fix | Sesi login bersamaan (single-session) & mapping data distribusi | Autentikasi / Distribution |

---

## 12. Kriteria Penerimaan (Acceptance Criteria) — Fitur Prioritas Critical

**Stock Receive & FIFO**
- Setiap input stock receive baru membentuk batch dengan tanggal dan harga beli tercatat.
- Saat distribusi/pengeluaran barang, sistem mengambil batch tertua terlebih dahulu secara otomatis, tanpa input manual pemilihan batch oleh admin.
- Laporan HPP menampilkan nilai yang konsisten dengan harga batch yang benar-benar terpakai.

**Item Price**
- Admin dapat mengubah harga beli/jual suatu item untuk tahun berjalan tanpa mengubah histori transaksi tahun sebelumnya.
- Jika tidak ada perubahan harga pada tahun berjalan, sistem otomatis menampilkan dan menggunakan harga tahun sebelumnya.

**Akun Mahasiswa Baru**
- Formulir pendataan mahasiswa baru dapat disimpan tanpa mengisi email kampus.
- Email kredensial akun terkirim ke email pribadi yang didaftarkan mahasiswa baru.
- Data mahasiswa dapat diperbarui dengan email kampus setelah tersedia, tanpa kehilangan riwayat data sebelumnya.

**Pengisian Ukuran Berbasis Kategori**
- Mahasiswa hanya mengisi ukuran satu kali per kategori (Baju, Sepatu, Merchandise), bukan per SKU.
- Ukuran kategori otomatis diterapkan ke seluruh SKU yang tergabung dalam kategori tersebut saat proses distribusi.

---

## 13. Lampiran: Catatan Operasional Tambahan

- Estimasi biaya hosting saat ini berkisar Rp100.000–Rp300.000 per bulan dan masih dianggap dalam batas aman.
- Apabila diperlukan upgrade kapasitas hosting, alur yang disepakati: upgrade hosting → kirim informasi kebutuhan upgrade → kirim bukti pembayaran → proses reimbursement.
- Modul Stock yang diakses mahasiswa bersifat pelaporan (report) saja dan tidak memerlukan fitur import/edit.
- Sistem penjadwalan distribusi mendukung pembuatan jadwal jangka panjang (satu semester hingga satu tahun ke depan); dampaknya terhadap performa sistem perlu dipantau saat volume data bertambah.

---

*— Akhir Dokumen —*
