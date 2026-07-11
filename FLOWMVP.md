# UniStock — Panduan Skenario Pengujian MVP (End-to-End)

Dokumen ini berisi tutorial langkah-demi-langkah (*step-by-step*) untuk menguji seluruh alur kerja (*flow*) utama sistem UniStock dari awal pembuatan data hingga barang diterima oleh mahasiswa.

---

## **Kredensial Akun Pengujian**
*(Password untuk semua akun di bawah adalah: **`password`**)*

* **Super Admin**: `superadmin@horizon-unistock.test`
* **Finance/Admin**: `finance@horizon-unistock.test`
* **Staff Gudang**: `staff@horizon-unistock.test`
* **Mahasiswa (Default)**: `student@horizon-unistock.test`

---

## **Langkah 1: Registrasi Mahasiswa Baru**
* **Tujuan**: Mendaftarkan mahasiswa baru secara manual ke dalam sistem.
* **Peran**: **Finance/Admin**

1. Login ke aplikasi web menggunakan akun **Finance/Admin** (`finance@horizon-unistock.test`).
2. Buka menu **Master Data** -> **Mahasiswa**.
3. Klik tombol **Tambah Mahasiswa**.
4. Isi data mahasiswa:
   * **Nama**: Andi
   * **NIM**: `20260001`
   * **Program Studi**: S1 Keperawatan
   * **Angkatan (Level)**: Tingkat 1
   * **Tipe**: Freshman
   * **Email Kampus**: `andi@krw.horizon.ac.id` (Gunakan domain kampus yang sesuai)
5. Klik **Simpan**. Sistem akan otomatis men-generate akun pengguna baru untuk Andi (Username = NIM, Password = `password` atau random).

---

## **Langkah 2: Menetapkan Status Kelayakan Keuangan**
* **Tujuan**: Menjamin mahasiswa tersebut layak mengambil barang (sudah bayar lunas).
* **Peran**: **Finance/Admin**

1. Buka menu **Kelayakan Mahasiswa** di sidebar.
2. Cari nama mahasiswa **Andi** (NIM `20260001`) pada kolom pencarian.
3. Klik tombol **Set Lunas (Eligible)** di sebelah kanan nama Andi.
4. Status Andi seketika akan berubah menjadi **Layak (Eligible)** dengan status pembayaran **Paid**.
*(Catatan: Untuk membatalkan, Anda cukup mengklik tombol "Set Belum Lunas" kembali).*

---

## **Langkah 3: Menyiapkan Hak Barang (Entitlement) & Jadwal**
* **Tujuan**: Mengatur barang apa saja yang berhak diterima mahasiswa serta jadwal pengambilannya.
* **Peran**: **Finance/Admin**

1. **Buat Hak Barang (Entitlement)**:
   * Buka menu **Entitlement**.
   * Klik **Tambah Entitlement**.
   * Pilih **Angkatan (Program Level)** dan **Program Studi** pada dropdown. Sistem akan secara otomatis men-generate **Kode Entitlement** yang tepat.
   * Isi **Deskripsi** (misal: *Almamater S1 Keperawatan Angkatan 2024/2025*).
   * Pada bagian **Pilih Item & Jumlah Hak**, centang barang yang sesuai (misal: *Almamater*) dan sesuaikan jumlahnya (misal: `1`).
   * Klik **Simpan**.
   *(Sistem akan secara otomatis mendeteksi bahwa mahasiswa dengan angkatan & jurusan tersebut berhak mendapatkan item ini).*
2. **Buat Jadwal Distribusi**:
   * Buka menu **Jadwal Distribusi**.
   * Klik **Tambah Jadwal**.
   * Isi data jadwal:
     * **Nama Jadwal**: (misal: *Pembagian Almamater Tahun Kesatu*)
     * **Angkatan / Fakultas / Program Studi**: (Pilih kriteria mahasiswa yang boleh mengambil, atau biarkan kosong/Semua jika untuk seluruh mahasiswa)
     * **Tanggal / Lokasi / Sesi / Jam**: (Tentukan kapan dan di mana pengambilan dilakukan)
     * **Item yang Dibagikan**: Centang barang yang akan dibagikan pada jadwal ini (misal: centang *Almamater* pada grid di bawah).
   * Klik **Simpan**.

---

## **Langkah 4: Mahasiswa Mengisi Ukuran & Mendapat QR**
* **Tujuan**: Mahasiswa mengisi ukuran seragam secara mandiri dan mengunduh kode QR identitas pengambilan.
* **Peran**: **Mahasiswa (Andi)**

1. Logout dari akun Admin, lalu login sebagai **Andi** (NIM: `20260001` / Password: `password`).
2. **Ubah Password**:
   * Karena ini login pertama, sistem akan mendeteksi password default dan memaksa Anda mengganti password terlebih dahulu demi keamanan.
3. **Pilih Ukuran**:
   * Setelah masuk ke dashboard, pilih menu **Input Ukuran**.
   * Pilih ukuran Jas Almamater Anda pada opsi yang tersedia (misal: ukuran **M**).
   * Klik **Simpan Ukuran**.
4. **Unduh QR Code**:
   * Masuk ke menu **QR Code**.
   * Layar akan memunculkan QR Code ID permanen milik Andi. Catat atau screenshot QR Code ini untuk di-scan di gudang.

---

## **Langkah 5: Serah Terima Barang di Gudang & Fitting**
* **Tujuan**: Staff melakukan verifikasi kelayakan mahasiswa, melayani fitting, menyerahkan barang, dan memotong stok.
* **Peran**: **Staff Gudang**

1. Logout dari akun mahasiswa, lalu login sebagai **Staff Gudang** (`staff@horizon-unistock.test`).
2. Masuk ke halaman **Scan QR** / **Distribusi Barang**.
3. **Scan Identitas**:
   * Arahkan QR Code Andi ke kamera scanner sistem (atau ketik NIM `20260001` secara manual jika kamera tidak tersedia).
4. **Verifikasi & Fitting**:
   * Layar Staff menampilkan status Andi: **Layak (Eligible)** dan menampilkan jas ukuran **M**.
   * Andi mencoba jas ukuran M secara fisik, namun kesempitan. Andi meminta ukuran **L**.
   * Staff mengganti dropdown **Ukuran Aktual** di layar dari **M** menjadi **L**.
5. **Submit Transaksi**:
   * Staff mengklik tombol **Submit / Bagikan Barang**.
   * Sistem otomatis memotong stok Jas Almamater ukuran **L** sebanyak 1 unit di gudang, mencatat log transaksi, dan menandai barang Andi telah sukses dibagikan.
