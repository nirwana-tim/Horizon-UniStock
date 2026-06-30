<?php

namespace App\Imports;

use App\Models\ProgramLevel;
use App\Models\Student;
use App\Models\StudyProgram;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): Student
    {
        $studyProgram = StudyProgram::where('code', $row['program_studi'])->first();
        $programLevel = ProgramLevel::where('code', $row['level'])->first();

        return new Student([
            'nim' => $row['nim'],
            'name' => $row['nama'],
            'email_kampus' => $row['email_kampus'],
            'email_pribadi' => $row['email_pribadi'] ?? null,
            'study_program_id' => $studyProgram?->id,
            'program_level_id' => $programLevel?->id,
            'qr_token' => Str::uuid(),
        ]);
    }

    public function rules(): array
    {
        return [
            'nim' => ['required', 'string', 'max:20', 'unique:students,nim'],
            'nama' => ['required', 'string', 'max:255'],
            'email_kampus' => ['required', 'email', 'max:255', 'unique:students,email_kampus'],
            'email_pribadi' => ['nullable', 'email', 'max:255'],
            'program_studi' => ['required', 'string', 'exists:study_programs,code'],
            'level' => ['required', 'string', 'exists:program_levels,code'],
        ];
    }
}
