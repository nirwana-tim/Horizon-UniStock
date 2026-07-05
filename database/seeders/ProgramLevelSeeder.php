<?php

namespace Database\Seeders;

use App\Models\ProgramLevel;
use Illuminate\Database\Seeder;

class ProgramLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Angkatan 2024', 'code' => '2425'],
            ['name' => 'Angkatan 2025', 'code' => '2526'],
            ['name' => 'Angkatan 2026', 'code' => '2627'],
        ];

        foreach ($levels as $level) {
            ProgramLevel::firstOrCreate(
                ['code' => $level['code']],
                ['name' => $level['name']]
            );
        }
    }
}
