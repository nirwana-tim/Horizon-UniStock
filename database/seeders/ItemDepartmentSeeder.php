<?php

namespace Database\Seeders;

use App\Models\ItemDepartment;
use Illuminate\Database\Seeder;

class ItemDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['code' => '01', 'name' => 'Horizon'],
            ['code' => '02', 'name' => 'STIKES'],
            ['code' => '03', 'name' => 'STMIK'],
            ['code' => '04', 'name' => 'STIE'],
            ['code' => '05', 'name' => 'S1 KEP'],
            ['code' => '06', 'name' => 'D3 KEP'],
            ['code' => '07', 'name' => 'D3 KEB'],
            ['code' => '08', 'name' => 'PROF NERS'],
            ['code' => '09', 'name' => 'NERS'],
            ['code' => '14', 'name' => 'S1 Pariwisata'],
        ];

        foreach ($departments as $dept) {
            ItemDepartment::firstOrCreate(
                ['code' => $dept['code']],
                ['name' => $dept['name']]
            );
        }
    }
}
