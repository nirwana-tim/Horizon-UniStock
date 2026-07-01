<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            ['name' => 'Faculty of Health Science', 'code' => 'FHS'],
            ['name' => 'Faculty of Computer Science', 'code' => 'FCS'],
            ['name' => 'Faculty of Economics & Business', 'code' => 'FEB'],
            ['name' => 'Faculty of Tourism & Hospitality', 'code' => 'FTH'],
        ];

        foreach ($faculties as $faculty) {
            Faculty::firstOrCreate(
                ['code' => $faculty['code']],
                ['name' => $faculty['name']]
            );
        }
    }
}
