<?php

namespace App\Services;

use App\Models\AppSetting;

class StudentSizeCategoryService
{
    /**
     * Kategori barang yang dianggap sebagai "baju" (ukuran huruf, mis. S/M/L).
     * Nilai dari setting DB, fallback ke config default.
     */
    public function bajuCategoryCodes(): array
    {
        return AppSetting::get(
            'student-size.baju_category_codes',
            config('student-size.baju_category_codes', [])
        );
    }

    /**
     * Kategori barang yang dianggap sebagai "sepatu" (ukuran angka, mis. 40-44).
     * Nilai dari setting DB, fallback ke config default.
     */
    public function sepatuCategoryCodes(): array
    {
        return AppSetting::get(
            'student-size.sepatu_category_codes',
            config('student-size.sepatu_category_codes', [])
        );
    }
}
