<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use App\Models\ItemSize;
use Illuminate\Database\Seeder;

class CategoryItemSizeSeeder extends Seeder
{
    public function run(): void
    {
        $mapping = [
            'KIT' => ['01'],
            'KTM' => ['01'],
            'UNF' => ['02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20'],
            'SHO' => ['34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'],
            'MRC' => ['TM', 'TL', 'VR'],
        ];

        foreach ($mapping as $catCode => $sizeCodes) {
            $category = ItemCategory::where('code', $catCode)->first();
            if (!$category) {
                continue;
            }

            $sizeIds = ItemSize::whereIn('code', $sizeCodes)->pluck('id')->toArray();
            $category->sizes()->syncWithoutDetaching($sizeIds);
        }
    }
}
