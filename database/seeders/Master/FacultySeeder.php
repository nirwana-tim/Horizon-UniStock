<?php

namespace Database\Seeders\Master;

use App\Models\Faculty;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        Faculty::firstOrCreate(
            ['code' => 'FICT'],
            ['name' => 'Faculty of Information Computer and Technology']
        );
        Faculty::firstOrCreate(
            ['code' => 'FHS'],
            ['name' => 'Faculty of Health Sciences']
        );
        Faculty::firstOrCreate(
            ['code' => 'FMB'],
            ['name' => 'Faculty of Management and Business']
        );
    }
}
