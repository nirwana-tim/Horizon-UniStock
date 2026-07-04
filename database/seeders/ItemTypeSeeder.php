<?php

namespace Database\Seeders;

use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'ALM', 'label' => 'Almamater'],
            ['code' => 'CLG', 'label' => 'College'],
            ['code' => 'CLC', 'label' => 'Clinical'],
            ['code' => 'SCB', 'label' => 'Scrub'],
            ['code' => 'LAB', 'label' => 'Laboratory'],
            ['code' => 'COM', 'label' => 'Community'],
            ['code' => 'YDH', 'label' => 'Lanyard & Holder'],
            ['code' => 'KTM', 'label' => 'KTM'],
            ['code' => 'TAG', 'label' => 'Name Tag'],
            ['code' => 'NUR', 'label' => 'Nursing Kit'],
            ['code' => 'MID', 'label' => 'Midwifery Kit'],
            ['code' => 'TBR', 'label' => 'Tumbler'],
        ];

        foreach ($types as $type) {
            ItemType::firstOrCreate(
                ['code' => $type['code']],
                ['label' => $type['label']]
            );
        }
    }
}
