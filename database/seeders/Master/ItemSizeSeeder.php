<?php

namespace Database\Seeders\Master;

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
        ];

        foreach ($sizes as $size) {
            ItemSize::firstOrCreate(
                ['code' => $size['code']],
                ['label' => $size['label']]
            );
        }
    }
}
