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
        ItemCategory::firstOrCreate(
            ['code' => 'KIT'],
            ['label' => 'Kit']
        );
        ItemCategory::firstOrCreate(
            ['code' => 'KTM'],
            ['label' => 'Kartu Tanda Mahasiswa']
        );
        ItemCategory::firstOrCreate(
            ['code' => 'SHO'],
            ['label' => 'Shoes']
        );
        ItemCategory::firstOrCreate(
            ['code' => 'MRC'],
            ['label' => 'Merchandise']
        );
    }
}
