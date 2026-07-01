<?php

namespace Database\Seeders;

use App\Models\ProgramLevel;
use Illuminate\Database\Seeder;

class ProgramLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Angkatan 2024', 'code' => 'ANG-2024'],
            ['name' => 'Angkatan 2025', 'code' => 'ANG-2025'],
            ['name' => 'Angkatan 2026', 'code' => 'ANG-2026'],
            ['name' => 'Semester 1', 'code' => 'SEM-1'],
            ['name' => 'Semester 2', 'code' => 'SEM-2'],
            ['name' => 'Semester 3', 'code' => 'SEM-3'],
            ['name' => 'Semester 4', 'code' => 'SEM-4'],
            ['name' => 'Semester 5', 'code' => 'SEM-5'],
            ['name' => 'Semester 6', 'code' => 'SEM-6'],
        ];

        foreach ($levels as $level) {
            ProgramLevel::firstOrCreate(
                ['code' => $level['code']],
                ['name' => $level['name']]
            );
        }
    }
}
