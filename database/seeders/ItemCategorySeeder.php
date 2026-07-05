<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'UNF', 'label' => 'Seragam'],
            ['code' => 'SHO', 'label' => 'Sepatu'],
            ['code' => 'KTM', 'label' => 'Kartu Tanda Mahasiswa'],
            ['code' => 'KIT', 'label' => 'Kit Praktik'],
            ['code' => 'MRC', 'label' => 'Merchandise'],
        ];

        foreach ($categories as $category) {
            ItemCategory::updateOrCreate(
                ['code' => $category['code']],
                ['label' => $category['label']]
            );
        }
    }
}
