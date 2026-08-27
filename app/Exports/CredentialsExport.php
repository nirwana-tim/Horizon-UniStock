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

    public function __construct(
        private array $students,
    ) {}

    public function collection(): Enumerable
    {
        return collect($this->students);
    }

    public function headings(): array
    {
        return ['NIM', 'Name', 'Email', 'Password'];
    }

    public function map($student): array
    {
        $email = $student->resolveNotificationEmail() ?? $student->email_kampus ?? $student->email_pribadi ?? '-';
        $password = "Uniform@{$student->nim}";

        return [
            $student->nim,
            $student->name,
            $email,
            $password,
        ];
    }
}
