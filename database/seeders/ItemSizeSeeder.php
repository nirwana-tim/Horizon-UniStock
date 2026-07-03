<?php

namespace Database\Seeders;

use App\Models\ItemSize;
use Illuminate\Database\Seeder;

class ItemSizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['code' => '01', 'name' => 'All Size', 'sort_order' => 1],
            ['code' => '03', 'name' => 'S', 'sort_order' => 10],
            ['code' => '04', 'name' => 'M', 'sort_order' => 20],
            ['code' => '05', 'name' => 'L', 'sort_order' => 30],
            ['code' => '06', 'name' => 'XL', 'sort_order' => 40],
            ['code' => '07', 'name' => '2XL', 'sort_order' => 50],
            ['code' => '08', 'name' => '3XL', 'sort_order' => 60],
            ['code' => '09', 'name' => '4XL', 'sort_order' => 70],
            ['code' => '10', 'name' => '5XL', 'sort_order' => 80],
            ['code' => '11', 'name' => '6XL', 'sort_order' => 90],
            ['code' => '12', 'name' => '7XL', 'sort_order' => 100],
            ['code' => '13', 'name' => '8XL', 'sort_order' => 110],
            ['code' => '14', 'name' => '9XL', 'sort_order' => 120],
            ['code' => '15', 'name' => '10XL', 'sort_order' => 130],
            ['code' => '16', 'name' => '11XL', 'sort_order' => 140],
            ['code' => '17', 'name' => '12XL', 'sort_order' => 150],
            ['code' => '18', 'name' => '13XL', 'sort_order' => 160],
            ['code' => '19', 'name' => '14XL', 'sort_order' => 170],
            ['code' => '20', 'name' => '15XL', 'sort_order' => 180],
            ['code' => '34', 'name' => '34', 'sort_order' => 200],
            ['code' => '35', 'name' => '35', 'sort_order' => 210],
            ['code' => '36', 'name' => '36', 'sort_order' => 220],
            ['code' => '37', 'name' => '37', 'sort_order' => 230],
            ['code' => '38', 'name' => '38', 'sort_order' => 240],
            ['code' => '39', 'name' => '39', 'sort_order' => 250],
            ['code' => '40', 'name' => '40', 'sort_order' => 260],
            ['code' => '41', 'name' => '41', 'sort_order' => 270],
            ['code' => '42', 'name' => '42', 'sort_order' => 280],
            ['code' => '43', 'name' => '43', 'sort_order' => 290],
            ['code' => '44', 'name' => '44', 'sort_order' => 300],
            ['code' => '45', 'name' => '45', 'sort_order' => 310],
            ['code' => '46', 'name' => '46', 'sort_order' => 320],
            ['code' => 'TM', 'name' => 'TM', 'sort_order' => 400],
            ['code' => 'TL', 'name' => 'TL', 'sort_order' => 410],
            ['code' => 'VR', 'name' => 'VR', 'sort_order' => 420],
        ];

        foreach ($sizes as $size) {
            ItemSize::firstOrCreate(
                ['code' => $size['code']],
                ['name' => $size['name'], 'sort_order' => $size['sort_order']]
            );
        }
    }
}
