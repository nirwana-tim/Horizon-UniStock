<?php

namespace Database\Seeders;

use App\Models\ItemDepartment;
use Illuminate\Database\Seeder;

class ItemDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['code' => '01', 'label' => 'Horizon'],
            ['code' => '02', 'label' => 'STIKES'],
            ['code' => '03', 'label' => 'STMIK'],
            ['code' => '04', 'label' => 'STIE'],
            ['code' => '05', 'label' => 'S1 KEP'],
            ['code' => '06', 'label' => 'D3 KEP'],
            ['code' => '07', 'label' => 'D3 KEB'],
            ['code' => '08', 'label' => 'PROF NERS'],
            ['code' => '09', 'label' => 'NERS'],
            ['code' => '14', 'label' => 'S1 Pariwisata'],
        ];

        foreach ($departments as $dept) {
            ItemDepartment::firstOrCreate(
                ['code' => $dept['code']],
                ['label' => $dept['label']]
            );
        }
    }
}
