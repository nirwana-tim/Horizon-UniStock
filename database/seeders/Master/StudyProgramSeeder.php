<?php

namespace Database\Seeders\Master;

use App\Models\Faculty;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'FHS' => [
                ['code' => 'S1 KEP', 'name' => 'S1 Keperawatan'],
                ['code' => 'S1 KEP NR', 'name' => 'S1 Keperawatan Ners'],
                ['code' => 'NERS', 'name' => 'Ners'],
                ['code' => 'D3 KEP', 'name' => 'D3 Keperawatan'],
                ['code' => 'D3 KEB', 'name' => 'D3 Kebidanan'],
            ],
            'FICT' => [
                ['code' => 'S1 SI', 'name' => 'S1 Sistem Informasi'],
                ['code' => 'S1 IF', 'name' => 'S1 Informatika'],
                ['code' => 'S1 TI', 'name' => 'S1 Teknik Informatika'],
            ],
            'FMB' => [
                ['code' => 'S1 MNJ', 'name' => 'S1 Management'],
                ['code' => 'S1 AKT', 'name' => 'S1 Akuntansi'],
                ['code' => 'S1 PARI', 'name' => 'S1 Pariwisata'],
            ],
        ];

        foreach ($data as $facultyCode => $studyPrograms) {
            $faculty = Faculty::where('code', $facultyCode)->first();

            if (!$faculty) {
                $this->command->warn("Faculty {$facultyCode} not found. Run Master\\FacultySeeder first.");
                continue;
            }

            foreach ($studyPrograms as $program) {
                StudyProgram::firstOrCreate(
                    ['code' => $program['code']],
                    [
                        'name' => $program['name'],
                        'faculty_id' => $faculty->id,
                    ]
                );
            }
        }
    }
}