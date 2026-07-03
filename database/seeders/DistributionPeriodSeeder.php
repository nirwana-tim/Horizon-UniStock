<?php

namespace Database\Seeders;

use App\Models\DistributionPeriod;
use Illuminate\Database\Seeder;

class DistributionPeriodSeeder extends Seeder
{
    public function run(): void
    {
        DistributionPeriod::firstOrCreate(
            ['name' => 'Tahun Akademik 2025/2026'],
            [
                'start_date' => '2025-09-01',
                'end_date' => '2026-08-31',
                'size_change_deadline' => '2026-08-01 23:59:59',
                'is_active' => true,
            ]
        );

        DistributionPeriod::firstOrCreate(
            ['name' => 'Tahun Akademik 2024/2025'],
            [
                'start_date' => '2024-09-01',
                'end_date' => '2025-08-31',
                'size_change_deadline' => '2025-08-01 23:59:59',
                'is_active' => false,
            ]
        );
    }
}
