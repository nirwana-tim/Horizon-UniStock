<?php

namespace Database\Seeders\Master;

use App\Models\ProgramLevel;
use Illuminate\Database\Seeder;

class ProgramLevelSeeder extends Seeder
{
    public function run(): void
    {
        ProgramLevel::firstOrCreate(
            ['code' => '2425'],
            ['name' => 'SY 24/25']
        );
    }
}
