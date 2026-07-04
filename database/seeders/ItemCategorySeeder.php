<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'KIT', 'label' => 'Kit'],
            ['code' => 'KTM', 'label' => 'KTM'],
            ['code' => 'UNF', 'label' => 'Uniform'],
            ['code' => 'SHO', 'label' => 'Shoes'],
            ['code' => 'MRC', 'label' => 'Merchandise'],
        ];

        foreach ($categories as $category) {
            ItemCategory::firstOrCreate(
                ['code' => $category['code']],
                ['label' => $category['label']]
            );
        }
    }
}
