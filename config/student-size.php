<?php

return [
    // Nilai default berikut hanya dipakai bila belum disimpan lewat
    // halaman "System → Konfigurasi Ukuran" (tabel app_settings).
    // Setelah admin/super admin menyimpan setting, nilai DB yang dipakai.

    // Kategori yang menyuplai ukuran "BAJU" (huruf: XS..XXXL)
    'baju_category_codes' => ['UNF', 'KIT', 'KTM'],

    // Kategori yang menyuplai ukuran "SEPATU" (angka: 34-46)
    'sepatu_category_codes' => ['SHO'],
];
