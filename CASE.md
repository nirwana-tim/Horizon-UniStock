# Horizon-UniStock — Daftar Kasus Bisnis & Aturan Validasi

Dokumen ini merangkum seluruh skenario kasus bisnis (*business cases*), aturan validasi, dan keputusan arsitektur yang telah didiskusikan dan diimplementasikan untuk referensi pengembangan sistem di masa mendatang.

---

### **Kasus 1: Proteksi Kode SKU & Entitlement (Immutability)**
* **Skenario**: Pengguna salah menginput kode kategori/tipe/departemen/entitlement dan ingin mengeditnya di kemudian hari.
* **Keputusan Arsitektur**: **KODE DIKUNCI (Read-Only / Disabled)** setelah dibuat.
* **Alasan**: 
  * Perubahan kode master di tengah jalan akan merusak string SKU barang yang sudah tersebar di database transaksi lama (`stock_movements`, `distribution_items`).
  * Jika kode diubah di sistem, QR Code fisik yang sudah telanjur dicetak dan ditempel di seragam tidak akan bisa di-scan lagi (*mismatch*).
* **Solusi**: Input kode disembunyikan/di-disable pada halaman Edit. Jika terjadi kesalahan ketik di awal, admin harus menghapus data tersebut (selama belum ada transaksi) dan membuat baru.

---

### **Kasus 2: Aturan Input Kode Baru (Huruf vs Angka)**
* **Skenario**: Pembedaan metode pengisian kode saat pembuatan baru (*create*) berdasarkan tipe kode.
* **Aturan Implementasi**:
  * **Kode Karakter/Huruf (Kategori & Tipe)**: Admin menginput kode secara manual (misal: `UNF`, `SHO`, `CLG`) pada form tambah. Validasi backend menjamin kode wajib diisi (`required`) dan **harus unik** di database.
  * **Kode Angka/Nomor (Departemen & Ukuran)**: Form input kode dihapus. Backend secara otomatis men-generate nomor urut dua digit yang belum terpakai (seperti `01`, `02`, `03`... `99`) agar penomoran rapi dan konsisten.

---

### **Kasus 3: Generator Kode Entitlement Otomatis (UI Dropdown)**
* **Skenario**: Admin kesulitan dan rawan melakukan salah ketik (*typo*) saat menuliskan string Kode Entitlement secara manual (contoh format: `{LevelCode}{FacultyCode}{ProdiCode}` seperti `2425FHSS1-KEP`).
* **Solusi Implementasi**:
  * Form input teks Kode Entitlement diubah menjadi **`readonly` (di-lock)** di halaman Tambah Entitlement.
  * Ditambahkan dua dropdown menu: **Angkatan (Program Level)** dan **Program Studi (Study Program)**.
  * JavaScript secara dinamis mendeteksi pilihan admin, menarik kode relasi fakultas terkait secara otomatis, lalu menggabungkannya menjadi kode entitlement yang valid secara *real-time*. Menjamin kecocokan kode 100% dengan data mahasiswa.

---

### **Kasus 4: Pencatatan Harga Barang Historis & Kesiapan FIFO**
* **Skenario**: Harga seragam dari vendor mengalami kenaikan di tahun berjalan akibat inflasi atau pergantian vendor.
* **Solusi Implementasi**: 
  * Harga jual dan HPP disimpan di dalam tabel terpisah **`item_prices`** yang terhubung secara historis ke barang.
  * Menghindari perubahan harga retroaktif (mengubah harga sekarang tidak akan merusak laporan margin laba/GPM periode tahun lalu).
  * Struktur ini sudah mendukung penuh jika di masa depan diaktifkan fitur **FIFO (First-In, First-Out)**, di mana pemotongan stok tinggal didasarkan pada data *batch* `stock_receive_items` tertua yang tersisa.

---

### **Kasus 5: Kelayakan Pembayaran Mahasiswa (Eligibility One-Click Toggle)**
* **Skenario**: Menjamin mahasiswa layak mengambil barang bertahap sesuai status keuangan (lunas/cuti), dengan pengujian harian yang praktis tanpa bergantung penuh pada unggahan file Excel.
* **Solusi Implementasi**:
  * Sistem mendukung **Excel Import** untuk input data keuangan secara massal di awal semester.
  * Disediakan menu manual **"Kelayakan Mahasiswa"** berupa daftar seluruh mahasiswa kampus yang dilengkapi tombol **Toggle Satu-Klik**:
    * Mahasiswa belum lunas &rarr; Klik **"Set Lunas (Eligible)"** untuk mendaftarkan kelayakan instan.
    * Mahasiswa lunas &rarr; Klik **"Set Belum Lunas"** untuk menghapus status kelayakan instan (menolak hak ambil barang di loket scan).

---

### **Kasus 6: Pengisian & Pengubahan Ukuran oleh Mahasiswa**
* **Skenario**: Pembatasan kuota perubahan ukuran agar mahasiswa tidak terus-menerus mengganti ukuran baju setelah dipesan.
* **Solusi Implementasi**:
  * Mahasiswa hanya boleh mengisi ukuran **maksimal 2 kali** (termasuk inputan pertama kali) pada periode aktif.
  * Kuota perubahan ini dihitung per **Periode Distribusi** (di-reset di tahun kedua agar mahasiswa bisa menginput ukuran baru untuk kit tahun kedua mereka).
  * Form pengisian otomatis terkunci secara global begitu melewati batas tanggal **`size_change_deadline`** yang ditentukan admin pada periode berjalan.

---

### **Kasus 7: Fitting & Pergantian Ukuran di Lapangan (Fitting Overrides)**
* **Skenario**: Di meja pembagian gudang, mahasiswa mencoba baju ukuran M (sesuai pesanannya), namun ternyata sempit dan minta tukar ke ukuran L di tempat.
* **Solusi Implementasi**:
  * Staff dibekali dropdown **"Ukuran Aktual"** pada layar scan.
  * Staff mengubah ukuran dari M ke L sebelum klik simpan.
  * Sistem akan memotong stok fisik ukuran L (bukan ukuran M) di database, dan mencatat riwayat transaksi bahwa barang yang diserahkan secara aktual adalah ukuran L.

---

### **Kasus 8: Pembagian Entitlement & Kelayakan Bertahap per Semester**
* **Skenario**: Barang hak mahasiswa (seperti Jas Almamater vs Kit Klinis) dibagikan di semester/tahun yang berbeda. Ditambah lagi, kelayakan pembayaran (*Eligibility*) dicek setiap pergantian semester. Selain itu, mahasiswa boleh menginput ukuran badan kapan saja meski belum melakukan registrasi ulang.
* **Solusi Implementasi**:
  * **Pemisahan Entitlement**: Hak barang dipecah per-semester/tingkat (misal: Entitlement Ganjil untuk Almamater, Entitlement Genap untuk Kit Klinis). Mahasiswa dikaitkan ke Entitlement aktif yang sesuai.
  * **Reset Status Kelayakan**: Status kelayakan mahasiswa (`EligibilityRecord`) di-reset kembali menjadi "Belum Lunas" setiap kali memasuki semester baru.
  * **Pemisahan Input Ukuran & Distribusi**: Pintu input ukuran di aplikasi mahasiswa selalu terbuka secara bebas (tidak diblokir status lunas), namun saat pengambilan barang di gudang (`DistributionService`), status kelayakan wajib bernilai **Eligible (Lunas)**.
