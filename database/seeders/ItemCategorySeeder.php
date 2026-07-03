<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Almamater', 'code' => 'ALM'],
            ['name' => 'Seragam Kuliah', 'code' => 'CLG'],
            ['name' => 'Seragam Praktek', 'code' => 'CLC'],
            ['name' => 'Scrub Suit', 'code' => 'SCB'],
            ['name' => 'Jas Lab', 'code' => 'JLB'],
            ['name' => 'Seragam Komunitas', 'code' => 'COM'],
            ['name' => 'Sepatu Kuliah', 'code' => 'SCL'],
            ['name' => 'Sepatu Praktek', 'code' => 'SPR'],
            ['name' => 'Scrub Shoes', 'code' => 'SSH'],
            ['name' => 'Lanyard & Holder', 'code' => 'LYD'],
            ['name' => 'KTM', 'code' => 'KTM'],
            ['name' => 'Name Tag', 'code' => 'NAM'],
            ['name' => 'Nursing Kit', 'code' => 'NRS'],
            ['name' => 'Midwifery Kit', 'code' => 'MID'],
            ['name' => 'Merchandise', 'code' => 'MRC'],
        ];

        foreach ($categories as $category) {
            ItemCategory::firstOrCreate(
                ['code' => $category['code']],
                ['name' => $category['name']]
            );
        }
    }
}
