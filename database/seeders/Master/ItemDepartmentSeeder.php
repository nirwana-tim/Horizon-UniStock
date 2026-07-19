<?php

namespace Database\Seeders\Master;

use App\Models\ItemDepartment;
use Illuminate\Database\Seeder;

class ItemDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        ItemDepartment::firstOrCreate(
            ['code' => '02'],
            ['label' => 'STIKES / Fakultas Ilmu Kesehatan']
        );
    }
}
