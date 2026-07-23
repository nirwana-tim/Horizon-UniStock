<?php

namespace Database\Seeders\Master;

use App\Models\ItemCategory;
use App\Models\ItemSize;
use Illuminate\Database\Seeder;

class ItemSizeSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = [
            'UNF' => ItemCategory::where('code', 'UNF')->value('id'),
            'KIT' => ItemCategory::where('code', 'KIT')->value('id'),
            'KTM' => ItemCategory::where('code', 'KTM')->value('id'),
            'SHO' => ItemCategory::where('code', 'SHO')->value('id'),
            'MRC' => ItemCategory::where('code', 'MRC')->value('id'),
        ];

        $sizeCategories = [
            '01' => ['UNF'],
            '02' => ['UNF'],
            '03' => ['UNF'],
            '04' => ['UNF'],
            '05' => ['UNF'],
            '06' => ['UNF'],
            '07' => ['UNF'],
            '08' => ['UNF'],
            '09' => ['UNF'],
            '10' => ['UNF'],
            '11' => ['UNF'],
            '12' => ['UNF'],
            '13' => ['UNF'],
            '14' => ['UNF'],
            '15' => ['UNF'],
            '16' => ['UNF'],
            '17' => ['UNF'],
            '18' => ['UNF'],
            '19' => ['UNF'],
            '20' => ['UNF'],
            '34' => ['SHO'],
            '35' => ['SHO'],
            '36' => ['SHO'],
            '37' => ['SHO'],
            '38' => ['SHO'],
            '39' => ['SHO'],
            '40' => ['SHO'],
            '41' => ['SHO'],
            '42' => ['SHO'],
            '43' => ['SHO'],
            '44' => ['SHO'],
            '45' => ['SHO'],
            '46' => ['SHO'],
            'TM' => ['MRC'],
            'TL' => ['MRC'],
            'VR' => ['MRC'],
        ];

        $sizes = [
            ['code' => '01', 'label' => 'All Size'],
            ['code' => '02', 'label' => 'XS'],
            ['code' => '03', 'label' => 'S'],
            ['code' => '04', 'label' => 'M'],
            ['code' => '05', 'label' => 'L'],
            ['code' => '06', 'label' => 'XL'],
            ['code' => '07', 'label' => 'XXL'],
            ['code' => '08', 'label' => 'XXXL'],
            ['code' => '09', 'label' => 'XXXXL'],
            ['code' => '10', 'label' => 'XXXXXL'],
            ['code' => '11', 'label' => 'XXXXXXL'],
            ['code' => '12', 'label' => 'S Maroon'],
            ['code' => '13', 'label' => 'M Maroon'],
            ['code' => '14', 'label' => 'L Maroon'],
            ['code' => '15', 'label' => 'XL Maroon'],
            ['code' => '16', 'label' => 'XXL Maroon'],
            ['code' => '17', 'label' => 'XXXL Maroon'],
            ['code' => '18', 'label' => 'XXXXL Maroon'],
            ['code' => '19', 'label' => 'XXXXXL Maroon'],
            ['code' => '20', 'label' => 'XXXXXXL Maroon'],
            ['code' => '34', 'label' => '34'],
            ['code' => '35', 'label' => '35'],
            ['code' => '36', 'label' => '36'],
            ['code' => '37', 'label' => '37'],
            ['code' => '38', 'label' => '38'],
            ['code' => '39', 'label' => '39'],
            ['code' => '40', 'label' => '40'],
            ['code' => '41', 'label' => '41'],
            ['code' => '42', 'label' => '42'],
            ['code' => '43', 'label' => '43'],
            ['code' => '44', 'label' => '44'],
            ['code' => '45', 'label' => '45'],
            ['code' => '46', 'label' => '46'],
            ['code' => 'TM', 'label' => 'Termos'],
            ['code' => 'TL', 'label' => 'Tali'],
            ['code' => 'VR', 'label' => 'Viral'],
            
        ];

        foreach ($sizes as $size) {
            $itemSize = ItemSize::firstOrCreate(
                ['code' => $size['code']],
                ['label' => $size['label']]
            );

            $catCodes = $sizeCategories[$size['code']];
            $catIds = array_map(fn ($code) => $categoryIds[$code], $catCodes);
            $itemSize->categories()->sync($catIds);
        }
    }
}
