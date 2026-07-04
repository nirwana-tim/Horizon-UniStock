<?php

namespace App\Imports;

use App\Models\EligibilityRecord;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EligibilityImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): EligibilityRecord
    {
        $student = Student::where('nim', $row['nim'])->first();

        return EligibilityRecord::updateOrCreate(
            [
                'student_id' => $student?->id,
            ],
            [
                'is_eligible' => filter_var($row['is_eligible'], FILTER_VALIDATE_BOOLEAN),
                'payment_status' => $row['payment_status'] ?? null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nim' => ['required', 'string', 'exists:students,nim'],
            'is_eligible' => ['required', 'boolean'],
            'payment_status' => ['nullable', 'string', 'max:50'],
        ];
    }
}
