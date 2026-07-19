<?php

namespace Database\Seeders\Master;

use App\Models\Faculty;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    public function run(): void
    {
        $faculty = Faculty::where('code', 'FHS')->first();

        if (!$faculty) {
            $this->command->warn('Faculty FHS not found. Run Master\\FacultySeeder first.');

            return;
        }

        StudyProgram::firstOrCreate(
            ['code' => 'KEP'],
            [
                'name' => 'S1 Keperawatan',
                'faculty_id' => $faculty->id,
            ]
        );
    }
}
