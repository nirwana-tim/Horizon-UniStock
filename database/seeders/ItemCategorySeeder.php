<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Almamater', 'description' => 'Jaket almamater universitas'],
            ['name' => 'Seragam Kuliah', 'description' => 'Seragam kuliah maroon (College Uniform)'],
            ['name' => 'Seragam Praktek', 'description' => 'Seragam praktek klinik (Clinical Uniform)'],
            ['name' => 'Scrub Suit', 'description' => 'Seragam scrub + head cap'],
            ['name' => 'Jas Lab', 'description' => 'Jas laboratorium (Laboratory Gown)'],
            ['name' => 'Seragam Komunitas', 'description' => 'Seragam komunitas (Community Uniform)'],
            ['name' => 'Sepatu Kuliah', 'description' => 'Sepatu kuliah hitam (College Shoes)'],
            ['name' => 'Sepatu Praktek', 'description' => 'Sepatu praktek putih (Clinical Shoes)'],
            ['name' => 'Scrub Shoes', 'description' => 'Sepatu khusus scrub'],
            ['name' => 'Lanyard & Holder', 'description' => 'Lanyard dan holder KTM'],
            ['name' => 'KTM', 'description' => 'Kartu Tanda Mahasiswa'],
            ['name' => 'Name Tag', 'description' => 'Nameplate / tanda pengenal'],
            ['name' => 'Nursing Kit', 'description' => 'Perlengkapan praktik keperawatan'],
            ['name' => 'Midwifery Kit', 'description' => 'Perlengkapan praktik kebidanan'],
        ];

        foreach ($categories as $category) {
            ItemCategory::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
