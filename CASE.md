# UniStock — Daftar Kasus Bisnis & Pilihan Keputusan Finance

Dokumen ini merangkum seluruh skenario kasus bisnis (*business cases*), aturan validasi, keputusan arsitektur yang sudah diimplementasikan, serta **pilihan keputusan (opsi kebijakan)** untuk kasus-kasus yang masih memerlukan persetujuan akhir dari tim Finance saat presentasi prototype.

---

## Bagian A: Kasus Bisnis yang Sudah Diimplementasikan

### **Kasus 1: Proteksi Kode Master & SKU (Immutability)**
* **Skenario**: Pengguna salah menginput kode kategori/tipe/departemen/entitlement dan ingin mengeditnya di kemudian hari.
* **Keputusan Sistem**: **KODE DIKUNCI (Read-Only)** setelah dibuat.
* **Alasan**: Perubahan kode master di tengah jalan akan merusak string SKU barang yang sudah terikat di riwayat transaksi lama (`stock_movements`, `distribution_items`). Jika kode diubah, QR Code fisik yang sudah ditempel di baju tidak akan bisa di-scan lagi.
* **Solusi**: Kolom Kode di-disable pada form Edit. Jika salah ketik, data harus dihapus (selama belum ada transaksi) lalu dibuat ulang.

### **Kasus 2: Aturan Input Master Kode Baru (Auto-generate)**
* **Skenario**: Menjaga kerapian kode agar tidak diisi asal-asalan oleh admin.
* **Keputusan Sistem**: 
  * **Kode Huruf (Kategori & Tipe)**: Diisi manual oleh admin (misal: `UNF`, `SHO`, `ALM`).
  * **Kode Angka (Departemen & Ukuran)**: Form input kode dibuang. Sistem otomatis men-generate nomor urut dua digit secara berurutan (seperti `01`, `02`, `03`... `99`) agar rapi.

### **Kasus 3: Generator Kode Entitlement Otomatis**
* **Skenario**: Menghindari salah ketik saat menulis Kode Entitlement secara manual (contoh format: `{Level}{Faculty}{Prodi}` seperti `2627FHSS1-KEP`).
* **Keputusan Sistem**: Form input teks diubah menjadi `readonly`. Admin cukup memilih dropdown **Angkatan** dan **Program Studi**, lalu JavaScript otomatis menggabungkannya secara *real-time*.

### **Kasus 4: Pencatatan Harga Barang Historis (Kesiapan FIFO)**
* **Skenario**: Harga seragam dari vendor mengalami kenaikan di tengah tahun berjalan.
* **Keputusan Sistem**: Harga jual dan HPP disimpan di tabel terpisah `item_prices` secara historis. Mengubah harga sekarang tidak akan mengubah margin laba (GPM) pada laporan transaksi tahun lalu.

### **Kasus 5: Pengubahan Ukuran oleh Mahasiswa (Edit Limit)**
* **Skenario**: Mahasiswa terus-menerus mengubah ukuran baju sehingga membingungkan rekap pemesanan ke vendor.
* **Keputusan Sistem**: Mahasiswa hanya boleh mengisi ukuran **maksimal 2 kali** (1x input pertama, 1x kesempatan edit) per Periode Distribusi. Setelah itu, dropdown terkunci secara otomatis.

### **Kasus 6: Penyaringan Saldo Hak Lintas Transaksi (Remaining Balance Tracking)**
* **Skenario**: Bagaimana mencegah mahasiswa melakukan klaim/ambil ganda (*double-claim*) untuk barang yang sama di tahun berikutnya atau pada jadwal yang berbeda?
* **Keputusan Sistem**: **SISTEM SALDO HAK OTOMATIS**. 
* **Solusi**: Setiap kali mahasiswa di-scan di loket, sistem secara *real-time* menghitung sisa saldo barang (`Sisa Saldo = Total Hak Entitlement - Jumlah yang Sudah Pernah Diambil di Masa Lalu`). Jika saldo barang tersebut sudah habis (bernilai 0), maka baris barang di layar scan staf otomatis terkunci (*disabled/gray out*) dengan label hijau **"Sudah Diambil"**.

### **Kasus 7: Filter Dinamis Form Pembuatan Jadwal (Dynamic Form Filtering)**
* **Skenario**: Bagaimana mencegah admin salah mencentang barang yang tidak sesuai untuk prodi/angkatan tertentu saat membuat jadwal distribusi?
* **Keputusan Sistem**: **FORM DINAMIS BERBASIS ALPINE.JS**.
* **Solusi**: Saat membuat/mengedit jadwal distribusi, begitu Fakultas, Prodi, dan Angkatan dipilih, grid checklist barang di bagian bawah otomatis menyusut hanya menampilkan item yang terdaftar di Entitlement prodi tersebut. Jika Admin mengubah pilihan prodi secara mendadak, sistem otomatis meng-uncheck (membatalkan centang) barang-barang yang tersembunyi di latar belakang.

---

## Bagian B: Kasus Ambigu (Butuh Pilihan Keputusan dari Finance)

Berikut adalah beberapa kasus operasional yang memiliki beberapa alternatif solusi. Mohon pihak Finance memberikan keputusan kebijakan yang ingin diterapkan pada sistem:

### **Kasus 8: Batas Akhir Input Ukuran (Lock Deadline)**
* **Skenario**: Tanggal batas akhir pengisian ukuran mahasiswa sudah lewat. Bagaimana sistem harus membatasi mahasiswa?
* **Opsi Keputusan untuk Finance**:
  * **Opsi 1 (Kunci Total - Rekomendasi)**: Form pengisian di akun mahasiswa langsung dikunci total. Mahasiswa tidak bisa mengisi atau mengganti ukuran sama sekali. Mereka harus melapor manual ke Admin jika ingin mengubahnya.
  * **Opsi 2 (Tombol Minta Izin)**: Form tetap dikunci, tetapi mahasiswa diberikan tombol "Minta Izin Ubah Ukuran". Admin tinggal klik setujui/tolak dari layar Admin.
  * **Opsi 3 (Buka dengan Catatan)**: Form dibiarkan tetap terbuka, tetapi setiap pengubahan yang terlambat otomatis diberi tanda "Terlambat" di rekap pesanan ke vendor.

### **Kasus 9: Penanganan Desain Baru vs Stok Lama**
* **Skenario**: Ada perubahan desain seragam (misal: celana olahraga dari biru diganti merah). Bagaimana agar sisa stok celana biru di gudang tidak salah diberikan ke Mahasiswa Baru?
* **Opsi Keputusan untuk Finance**:
  * **Opsi 1 (Daftarkan Barang Baru - Rekomendasi)**: Desain celana merah didaftarkan sebagai nama barang baru di sistem. Sisa stok celana biru dipisahkan dan hanya digunakan untuk mahasiswa lama yang ingin membeli gantinya (eceran).
  * **Opsi 2 (Karantina / Kosongkan Stok)**: Tetap memakai nama barang yang sama, tetapi sisa stok celana biru di sistem gudang langsung dikurangi menjadi 0 (dianggap tidak aktif) agar tidak otomatis terpotong saat pembagian.
  * **Opsi 3 (Sistem Batch/Lot)**: Mengaktifkan pemisahan batch stok di gudang (Batch 2025 vs Batch 2026), lalu diatur agar pembagian mahasiswa baru hanya memotong stok dari Batch 2026.

### **Kasus 10: Penanganan Stok Habis Saat Pembagian**
* **Skenario**: Mahasiswa datang membawa QR Code, tetapi ukuran bajunya (misal: L) ternyata habis di gudang. Bagaimana staf gudang mencatatnya?
* **Opsi Keputusan untuk Finance**:
  * **Opsi 1 (Tunda Pembagian - Sudah Diterapkan)**: Barang tersebut tidak bisa dicentang di aplikasi. Staf hanya membagikan barang yang ada saja. Transaksi dicatat "Diterima Sebagian", dan sisa barang diambil nanti saat stok baru datang.
  * **Opsi 2 (Kas Bon Seragam / Stok Minus)**: Tetap dicentang sebagai "Sudah Diambil", tetapi stok di sistem dibiarkan menjadi minus (`-1`) sebagai tanda gudang berhutang seragam ke mahasiswa tersebut.

### **Kasus 11: Tampilan Daftar Barang di Layar Scan Staf**
* **Skenario**: Jadwal hari ini membagikan semua jenis barang, tetapi mahasiswa yang di-scan hanya berhak menerima beberapa barang saja sesuai prodi/angkatan mereka.
* **Opsi Keputusan untuk Finance**:
  * **Opsi 1 (Hanya Tampilkan Hak Mahasiswa - Rekomendasi)**: Layar scan staf hanya menampilkan barang-barang yang memang menjadi hak mahasiswa tersebut. Layar terlihat bersih dan ringkas.
  * **Opsi 2 (Tampilkan Semua tapi Kunci Barang Lain)**: Layar menampilkan seluruh jenis barang yang dibagikan hari itu, tetapi barang yang bukan hak mahasiswa tersebut otomatis terkunci (gray out) dan tidak bisa dicentang staf.

### **Kasus 12: Pengisian Ukuran Mahasiswa Lama (Continuing)**
* **Skenario**: Mahasiswa angkatan atas berhak mendapat barang baru (misal: Baju Praktek Baru). Bagaimana sistem menentukan ukurannya agar pembagian tidak macet karena mahasiswa lupa isi ukuran?
* **Opsi Keputusan untuk Finance**:
  * **Opsi 1 (Gunakan Ukuran Tahun Lalu - Sudah Aktif)**: Sistem otomatis menggunakan ukuran yang diisi mahasiswa pada tahun pertama. Namun jika ada tipe barang baru, mahasiswa tetap harus login untuk mengisi ukuran barang baru tersebut.
  * **Opsi 2 (Auto-Copy Kategori Serupa - Rekomendasi)**: Sistem otomatis menyamakan ukuran barang baru dengan ukuran barang lama yang sejenis (misal: jika ukuran jas almamater tahun lalu `M`, maka baju praktek baru otomatis diset `M`). Mahasiswa tidak perlu mengisi ulang kecuali ukurannya berubah.
  * **Opsi 3 (Wajib Isi Ulang Setiap Tahun)**: Setiap awal tahun ajaran baru, semua mahasiswa wajib mengisi ulang ukuran badan mereka untuk mengantisipasi perubahan berat/tinggi badan.

### **Kasus 13: Pembelian Eceran Indent (Pre-Order via QR)**
* **Skenario**: Mahasiswa lama membeli seragam eceran, tetapi stok kosong dan harus menunggu pesanan batch baru. Bagaimana merekam pengambilannya nanti di loket pembagian menggunakan QR Code?
* **Opsi Keputusan untuk Finance**:
  * **Opsi 1 (Masukkan ke Hak Pembagian - Rekomendasi)**: Setelah mahasiswa lama membayar eceran, Admin memasukkan namanya secara manual ke dalam daftar hak penerima barang periode tersebut. Saat antre, staf tinggal scan QR seperti biasa.
  * **Opsi 2 (Pemisahan Status Belanjaan)**: Saat di-scan, layar staf memunculkan info khusus bahwa mahasiswa lama tersebut mengambil **"Barang Belanjaan Eceran (Lunas)"**, bukan jatah seragam gratis akademik. (Rapi secara pencatatan audit keuangan, memerlukan modifikasi fitur).

---

## Bagian C: Daftar Pertanyaan Wawancara Alur Aktual Finance

Berikut adalah daftar pertanyaan terarah yang dapat digunakan saat presentasi prototype untuk memvalidasi alur operasional keuangan dan logistik:

### **1. Alur Pengadaan & Pemesanan ke Vendor (Restock / Procurement)**
* *"Bagaimana cara Finance menentukan kuota pesanan seragam ke vendor saat ini? Apakah memesan persis sejumlah data ukuran mahasiswa yang masuk, atau selalu ditambah buffer (misal +5% atau +10%) untuk cadangan salah ukuran?"*
* *"Kapan pemesanan (Purchase Order) dikirimkan ke vendor? Apakah menunggu sampai deadline input ukuran ditutup, atau dicicil per gelombang pendaftaran mahasiswa?"*
* *"Ketika barang datang dari vendor, apakah tim gudang mencatat penerimaan barang (Stock Receive) berdasarkan Surat Jalan/Delivery Order? Dan apakah Finance mencocokkan jumlah barang datang tersebut dengan dokumen Purchase Order (PO) awal?"*

### **2. Alur Penjualan Sisa Stok (Excess Stock & Retail POS)**
* *"Jika di akhir masa distribusi terdapat kelebihan seragam (stok sisa), apakah sisa tersebut boleh dijual secara eceran (POS) kepada mahasiswa lama yang seragamnya hilang/rusak?"*
* *"Jika boleh dijual eceran, bagaimana harga jual eceran ditentukan? Apakah sama dengan HPP modal, atau ada margin keuntungan tersendiri untuk kas universitas?"*
* *"Bagaimana alur pembayaran untuk POS Eceran ini nantinya? Apakah mahasiswa bayar tunai di gudang, via transfer bank universitas, atau di-potong dari tagihan semesteran mereka?"*
