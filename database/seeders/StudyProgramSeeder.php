<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    public function run(): void
    {
        $fhs = Faculty::where('code', 'FHS')->first();
        $fcs = Faculty::where('code', 'FCS')->first();
        $feb = Faculty::where('code', 'FEB')->first();
        $fth = Faculty::where('code', 'FTH')->first();

        $programs = [
            ['name' => 'D3 Keperawatan', 'code' => 'D3-KEP', 'faculty_id' => $fhs?->id],
            ['name' => 'D3 Kebidanan', 'code' => 'D3-KEB', 'faculty_id' => $fhs?->id],
            ['name' => 'D3 Farmasi', 'code' => 'D3-FAR', 'faculty_id' => $fhs?->id],
            ['name' => 'S1 Keperawatan', 'code' => 'S1-KEP', 'faculty_id' => $fhs?->id],
            ['name' => 'S1 Kebidanan', 'code' => 'S1-KEB', 'faculty_id' => $fhs?->id],
            ['name' => 'S1 Farmasi', 'code' => 'S1-FAR', 'faculty_id' => $fhs?->id],
            ['name' => 'S1 Gizi', 'code' => 'S1-GIZ', 'faculty_id' => $fhs?->id],
            ['name' => 'S1 Kesehatan Masyarakat', 'code' => 'S1-KESMAS', 'faculty_id' => $fhs?->id],
            ['name' => 'Profesi Ners', 'code' => 'PROF-NERS', 'faculty_id' => $fhs?->id],
            ['name' => 'Profesi Bidan', 'code' => 'PROF-BIDAN', 'faculty_id' => $fhs?->id],
            ['name' => 'S1 Informatika', 'code' => 'S1-INF', 'faculty_id' => $fcs?->id],
            ['name' => 'S1 Teknologi Informasi', 'code' => 'S1-TI', 'faculty_id' => $fcs?->id],
            ['name' => 'S1 Sistem Informasi', 'code' => 'S1-SI', 'faculty_id' => $fcs?->id],
            ['name' => 'S1 Manajemen', 'code' => 'S1-MNJ', 'faculty_id' => $feb?->id],
            ['name' => 'S1 Akuntansi', 'code' => 'S1-AKT', 'faculty_id' => $feb?->id],
            ['name' => 'S1 Pariwisata', 'code' => 'S1-PAR', 'faculty_id' => $fth?->id],
        ];

        foreach ($programs as $program) {
            StudyProgram::firstOrCreate(
                ['code' => $program['code']],
                ['name' => $program['name'], 'faculty_id' => $program['faculty_id']]
            );
        }
    }
}
