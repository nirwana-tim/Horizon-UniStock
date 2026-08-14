<?php

namespace App\Imports;

use App\Models\EligibilityRecord;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Validation\ValidationException as IlluminateValidationException;

class EligibilityImport implements ToCollection, WithHeadingRow
{
    private int $importedCount = 0;
    private int $totalRows = 0;

    public function headingRow(): int
    {
        return 4;
    }

    public function collection(Collection $rows): void
    {
        $records = $this->recordsFromRows($rows);
        $this->totalRows = count($records);

        $failures = $this->validateRecords($records);
        if ($failures !== []) {
            throw new ValidationException(
                IlluminateValidationException::withMessages([]),
                $failures
            );
        }

        DB::transaction(function () use ($records) {
            foreach ($records as $record) {
                $student = Student::where('nim', $record['nim'])->first();
                if (! $student) {
                    continue;
                }

                $statusBayar = strtolower(trim((string) $record['status_bayar']));
                $isEligible = $statusBayar === '' || in_array($statusBayar, ['lunas', 'bayar', 'ya', '1', 'true', 'yes', 'sudah'], true);

                EligibilityRecord::updateOrCreate(
                    ['student_id' => $student->id],
                    [
                        'is_eligible' => $isEligible,
                        'payment_status' => $record['status_bayar'],
                    ]
                );

                $this->importedCount++;
            }
        });
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function countRows(Collection $rows): int
    {
        return count($this->recordsFromRows($rows));
    }

    private function recordsFromRows(Collection $rows): array
    {
        $records = [];

        foreach ($rows as $index => $row) {
            $values = $row instanceof Collection ? $row->toArray() : (array) $row;

            $nim = $this->clean($values['nim'] ?? null);
            $statusBayar = $this->clean($values['status_bayar'] ?? null);

            if ($nim === null && $statusBayar === null) {
                continue;
            }

            $records[] = [
                'row' => $index + 1,
                'nim' => $nim,
                'status_bayar' => $statusBayar,
            ];
        }

        return $records;
    }

    private function validateRecords(array &$records): array
    {
        $failures = [];
        $nims = array_filter(array_map(fn ($r) => $r['nim'], $records));
        $validNims = $nims !== [] ? Student::whereIn('nim', $nims)->pluck('nim')->flip() : collect();

        foreach ($records as &$record) {
            $rules = [
                'nim' => ['required', 'string', 'max:50'],
                'status_bayar' => ['required', 'string', 'max:50'],
            ];

            $validator = Validator::make($record, $rules);

            if ($record['nim'] !== null && ! $validNims->has($record['nim'])) {
                $validator->errors()->add('nim', 'NIM tidak ditemukan di database.');
            }

            foreach ($validator->errors()->messages() as $attribute => $messages) {
                $failures[] = new Failure($record['row'], $attribute, $messages, $record);
            }
        }

        return $failures;
    }

    private function clean(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (is_numeric($value) && (str_contains(strtolower((string) $value), 'e+') || is_float($value))) {
            $value = number_format((float) $value, 0, '', '');
        }
        $value = ltrim(trim((string) $value), "'");
        return $value === '' ? null : $value;
    }
}
