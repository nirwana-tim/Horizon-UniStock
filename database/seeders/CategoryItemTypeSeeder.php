<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use App\Models\ItemType;
use Illuminate\Database\Seeder;

class CategoryItemTypeSeeder extends Seeder
{
    public function run(): void
    {
        $mappings = [
            // Seragam (UNF) -> ALM, CLG, SCB, LAB, COM
            'UNF' => ['ALM', 'CLG', 'SCB', 'LAB', 'COM'],
            // Sepatu (SHO) -> CLG, CLC
            'SHO' => ['CLG', 'CLC'],
            // Kartu Tanda Mahasiswa (KTM) -> KTM
            'KTM' => ['KTM'],
            // Kit Praktik (KIT) -> NUR, MID
            'KIT' => ['NUR', 'MID'],
            // Merchandise (MRC) -> YDH, TAG, TBR
            'MRC' => ['YDH', 'TAG', 'TBR'],
        ];

        foreach ($mappings as $categoryCode => $typeCodes) {
            $category = ItemCategory::where('code', $categoryCode)->first();
            if (!$category) {
                continue;
            }

            $types = ItemType::whereIn('code', $typeCodes)->get();
            $category->types()->syncWithoutDetaching($types->pluck('id')->toArray());
        }
    }
}
