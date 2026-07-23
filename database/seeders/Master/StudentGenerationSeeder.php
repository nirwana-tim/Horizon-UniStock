<?php

namespace Database\Seeders\Master;

use App\Models\StudentGeneration;
use Illuminate\Database\Seeder;

class StudentGenerationSeeder extends Seeder
{
    public function run(): void
    {
        StudentGeneration::firstOrCreate(
            ['code' => '2425'],
            ['name' => '2024/2025']
        );
        StudentGeneration::firstOrCreate(
            ['code' => '2526'],
            ['name' => '2025/2026']
        );
        StudentGeneration::firstOrCreate(
            ['code' => '2627'],
            ['name' => '2026/2027']
        );
    }
}
