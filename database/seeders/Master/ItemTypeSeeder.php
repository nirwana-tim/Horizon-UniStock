<?php

namespace Database\Seeders\Master;

use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    public function run(): void
    {
        ItemType::firstOrCreate(
            ['code' => 'ALM'],
            ['label' => 'Almamater']
        );
        ItemType::firstOrCreate(
            ['code' => 'CLG'],
            ['label' => 'College']
        );
        ItemType::firstOrCreate(
            ['code' => 'CLC'],
            ['label' => 'Clinical']
        );
        ItemType::firstOrCreate(
            ['code' => 'LAB'],
            ['label' => 'Laboratory']
        );
        ItemType::firstOrCreate(
            ['code' => 'SCB'],
            ['label' => 'Scrub']
        );
        ItemType::firstOrCreate(
            ['code' => 'COM'],
            ['label' => 'Community']
        );
        ItemType::firstOrCreate(
            ['code' => 'NUR'],
            ['label' => 'Nursing']
        );
        ItemType::firstOrCreate(
            ['code' => 'MID'],
            ['label' => 'Midwifery']
        );
        ItemType::firstOrCreate(
            ['code' => 'YDH'],
            ['label' => 'Lanyard & Holder']
        );
        ItemType::firstOrCreate(
            ['code' => 'KTM'],
            ['label' => 'Kartu Tanda Mahasiswa']
        );
        ItemType::firstOrCreate(
            ['code' => 'TAG'],
            ['label' => 'Tag']
        );
        ItemType::firstOrCreate(
            ['code' => 'TBR'],
            ['label' => 'Tumbler']
        );
    }
}
