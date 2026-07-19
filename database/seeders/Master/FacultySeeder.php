<?php

namespace Database\Seeders\Master;

use App\Models\Faculty;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        Faculty::firstOrCreate(
            ['code' => 'FHS'],
            ['name' => 'Fakultas Ilmu Kesehatan']
        );
    }
}
