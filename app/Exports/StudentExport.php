<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private int $row = 0;

    public function __construct(
        private ?string $search = null,
        private ?int $studyProgramId = null,
        private ?int $generationId = null,
    ) {}

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        $query = Student::with(['studyProgram.faculty', 'generation', 'studentLevel']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('nim', 'like', "%{$this->search}%");
            });
        }

        if ($this->studyProgramId) {
            $query->where('study_program_id', $this->studyProgramId);
        }

        if ($this->generationId) {
            $query->where('generation_id', $this->generationId);
        }

        return $query->latest()->orderBy('id');
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Name',
            'Study Program',
            'Faculty',
            'Level',
            'Type',
            'Campus Email',
            'Personal Email',
            'Account Status',
        ];
    }

    public function map($student): array
    {
        $this->row++;

        return [
            $this->row,
            $student->nim,
            $student->name,
            $student->studyProgram?->name ?? '-',
            $student->studyProgram?->faculty?->name ?? '-',
            $student->generation?->name ?? '-',
            $student->student_level_label,
            $student->email_kampus ?? '-',
            $student->email_pribadi ?? '-',
            $student->user_id ? 'Active' : 'Inactive',
        ];
    }
}
