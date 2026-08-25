<?php

namespace App\Exports;

use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CredentialsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    private int $row = 0;

    public function __construct(
        private array $students,
        private array $passwords = [],
    ) {}

    public function collection(): Enumerable
    {
        return collect($this->students);
    }

    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Name',
            'Study Program',
            'Campus Email',
            'Log In',
            'Password',
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
            $student->email_kampus ?? '-',
            $student->user?->last_login_at ? 'Sudah Login' : 'Belum Login',
            $this->passwords[$student->nim] ?? '-',
        ];
    }
}
