<?php

namespace Database\Seeders;

use App\Models\ItemSize;
use Illuminate\Database\Seeder;

class ItemSizeSeeder extends Seeder
{
    public function run(): void
    {
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
            ['code' => 'TM', 'label' => 'Termos'],
            ['code' => 'TL', 'label' => 'Tali'],
            ['code' => 'VR', 'label' => 'Viral'],
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
        ];

        foreach ($sizes as $size) {
            ItemSize::firstOrCreate(
                ['code' => $size['code']],
                ['label' => $size['label']]
            );
        }
    }
}
