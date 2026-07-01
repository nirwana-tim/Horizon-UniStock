# Testing Scenarios

| No | Skenario | Expected Result |
|----|----------|-----------------|
| 1 | Import duplicate NIM | Error, tidak boleh duplikat |
| 2 | QR tidak valid | Tampilkan error, izinkan cari NIM |
| 3 | Mahasiswa tidak eligible | Pengambilan ditolak |
| 4 | Double submit (staf yang sama) | Sistem tolak, transaksi sudah ada |
| 5 | Actual size berbeda dari expected | Simpan actual size, log perubahan |
| 6 | Stok kurang dari yang diminta | Tawarkan partial pickup |
| 7 | Partial pickup dipilih | Simpan qty sebagian, update stok sebagian |
| 8 | Export report distribusi | File Excel berisi semua data distribusi |
| 9 | Lupa password | OTP terkirim, validasi, reset berhasil |
| 10 | Import Excel format salah | Error handling, tampilkan pesan jelas |
| 11 | Update ukuran kedua kalinya | Tolak, maksimal 1 kali |
| 12 | Jadwal distribusi - email duplikat | Hanya dikirim 1x per mahasiswa per jadwal |
| 13 | Stock opname - variance positif | Surplus tercatat, adjustment journal dibuat |
| 14 | Stock opname - variance negatif | Shortage tercatat, adjustment journal dibuat |
| 15 | GPM calculation | (Harga Jual - HPP) × Qty Terjual = Laba/Rugi |
| 16 | Import stock opname | Variance dihitung otomatis per item |
