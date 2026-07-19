<?php

namespace Database\Seeders\Master;

use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    public function run(): void
    {
        ItemType::firstOrCreate(
            ['code' => 'SCB'],
            ['label' => 'Scrub']
        );
    }
}
