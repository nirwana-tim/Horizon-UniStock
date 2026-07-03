<?php

namespace Database\Seeders;

use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'ALM', 'name' => 'Almamater'],
            ['code' => 'CLG', 'name' => 'College'],
            ['code' => 'CLC', 'name' => 'Clinical'],
            ['code' => 'SCB', 'name' => 'Scrub'],
            ['code' => 'LAB', 'name' => 'Laboratory'],
            ['code' => 'COM', 'name' => 'Community'],
            ['code' => 'SHO', 'name' => 'Shoes'],
            ['code' => 'KTM', 'name' => 'KTM'],
            ['code' => 'TAG', 'name' => 'Name Tag'],
            ['code' => 'YDH', 'name' => 'Lanyard & Holder'],
            ['code' => 'MID', 'name' => 'Midwifery Kit'],
            ['code' => 'NUR', 'name' => 'Nursing Kit'],
            ['code' => 'TBR', 'name' => 'Tumbler'],
        ];

        foreach ($types as $type) {
            ItemType::firstOrCreate(
                ['code' => $type['code']],
                ['name' => $type['name']]
            );
        }
    }
}
