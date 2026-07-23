<?php

namespace Database\Seeders\Master;

use App\Models\ItemDepartment;
use Illuminate\Database\Seeder;

class ItemDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        ItemDepartment::firstOrCreate(
            ['code' => '01'],
            ['label' => 'Horizon']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '02'],
            ['label' => 'STIKES']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '03'],
            ['label' => 'STMIK']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '04'],
            ['label' => 'STIE']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '05'],
            ['label' => 'S1 KEP']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '06'],
            ['label' => 'D3 KEP']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '07'],
            ['label' => 'D3 KEB']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '08'],
            ['label' => 'S1 KEP NR']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '09'],
            ['label' => 'NERS']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '10'],
            ['label' => 'S1 SI']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '11'],
            ['label' => 'S1 IF']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '12'],           
            ['label' => 'Management']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '13'],
            ['label' => 'Akuntansi']
        );
        ItemDepartment::firstOrCreate(
            ['code' => '14'],
            ['label' => 'Pariwisata']
        );
    }
}
