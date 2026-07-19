<?php

namespace Database\Seeders\Master;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        ItemCategory::firstOrCreate(
            ['code' => 'UNF'],
            ['label' => 'Uniform']
        );
    }
}
