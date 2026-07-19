<?php

namespace App\Imports;

use App\Models\ProgramLevel;
use App\Models\Student;
use App\Models\StudyProgram;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;

class StudentImport implements ToCollection, WithMultipleSheets
{
    private int $totalRows = 0;

    private int $importedCount = 0;

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

        foreach ($records as $record) {
            Student::create([
                'nim' => $record['nim'],
                'name' => $record['name'],
                'email_kampus' => $record['email_kampus'],
                'email_pribadi' => $record['email_pribadi'],
                'study_program_id' => $record['study_program']->id,
                'program_level_id' => $record['program_level']->id,
                'student_type' => $record['student_type'],
                'entitlement_code' => $record['program_level']->code
                    . $record['study_program']->faculty->code
                    . $record['study_program']->code,
            ]);

            $this->importedCount++;
        }
    }

    public function sheets(): array
    {
        return [
            'Data' => $this,
        ];
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
            $values = array_values($row instanceof Collection ? $row->toArray() : (array) $row);

            if ($this->shouldSkipRow($values)) {
                continue;
            }

            $records[] = [
                'row' => $index + 1,
                'nim' => $this->clean($values[0] ?? null),
                'name' => $this->clean($values[1] ?? null),
                'program' => $this->clean($values[2] ?? null),
                'gender' => $this->clean($values[3] ?? null),
                'shirt_size' => $this->clean($values[4] ?? null),
                'shoe_size' => $this->clean($values[5] ?? null),
                'email_kampus' => $this->clean($values[6] ?? null),
                'email_pribadi' => $this->clean($values[7] ?? null),
                'student_type_raw' => $this->clean($values[8] ?? null),
            ];
        }

        return $records;
    }

    private function validateRecords(array &$records): array
    {
        $failures = [];
        $seenNims = [];
        $seenCampusEmails = [];

        foreach ($records as &$record) {
            $validator = Validator::make($record, [
                'nim' => ['required', 'string', 'max:20', 'unique:students,nim'],
                'name' => ['required', 'string', 'max:255'],
                'program' => ['required', 'string'],
                'email_kampus' => ['required', 'email', 'max:255', 'unique:students,email_kampus'],
                'email_pribadi' => ['nullable', 'email', 'max:255'],
                'student_type_raw' => ['required', 'string', 'in:Year 1 Sem 1,Year 1 Sem 2,Year 2 Sem 3,Year 2 Sem 4,Continuing,continuing,year_1_sem_1,year_1_sem_2,year_2_sem_3,year_2_sem_4'],
            ], [
                'student_type_raw.in' => 'The tipe field must be Year 1 Sem 1, Year 1 Sem 2, Year 2 Sem 3, Year 2 Sem 4, or Continuing.',
            ]);

            foreach ($validator->errors()->messages() as $attribute => $messages) {
                $failures[] = new Failure($record['row'], $attribute, $messages, $record);
            }

            if ($record['nim'] && in_array($record['nim'], $seenNims, true)) {
                $failures[] = new Failure($record['row'], 'nim', ['The nim field has a duplicate value in this file.'], $record);
            }

            if ($record['email_kampus'] && in_array(Str::lower($record['email_kampus']), $seenCampusEmails, true)) {
                $failures[] = new Failure($record['row'], 'email_kampus', ['The email kampus field has a duplicate value in this file.'], $record);
            }

            $seenNims[] = $record['nim'];
            $seenCampusEmails[] = Str::lower((string) $record['email_kampus']);

            $record['study_program'] = $this->resolveStudyProgram($record['program']);
            if (! $record['study_program']) {
                $failures[] = new Failure($record['row'], 'program', ["Program studi '{$record['program']}' tidak ditemukan."], $record);
            }

            $record['program_level'] = $this->resolveProgramLevel($record['nim']);
            if (! $record['program_level']) {
                $failures[] = new Failure($record['row'], 'level', ['Level/angkatan tidak ditemukan dari NIM.'], $record);
            }

            $record['student_type'] = match (Str::lower((string) $record['student_type_raw'])) {
                'year 1 sem 1', 'year_1_sem_1' => 'year_1_sem_1',
                'year 1 sem 2', 'year_1_sem_2' => 'year_1_sem_2',
                'year 2 sem 3', 'year_2_sem_3' => 'year_2_sem_3',
                'year 2 sem 4', 'year_2_sem_4' => 'year_2_sem_4',
                default => 'continuing',
            };
        }

        return $failures;
    }

    private function shouldSkipRow(array $values): bool
    {
        $firstCell = $this->clean($values[0] ?? null);

        if ($firstCell === null) {
            return collect($values)->filter(fn ($value): bool => $this->clean($value) !== null)->isEmpty();
        }

        return Str::startsWith(Str::upper($firstCell), [
            'TEMPLATE IMPORT',
            'ISI DATA',
            'URUTAN KOLOM',
            'NIM',
            'CONTOH FORMAT',
            'CONTOH',
        ]);
    }

    private function resolveStudyProgram(?string $value): ?StudyProgram
    {
        if (! $value) {
            return null;
        }

        $normalized = $this->normalizeProgramName($value);

        return StudyProgram::query()
            ->with('faculty')
            ->get()
            ->first(function (StudyProgram $program) use ($value, $normalized): bool {
                return $program->code === $value
                    || $this->normalizeProgramName($program->name) === $normalized;
            });
    }

    private function resolveProgramLevel(?string $nim): ?ProgramLevel
    {
        if (! $nim) {
            return null;
        }

        // 1. Try to match a 4-digit year prefix (e.g. 2024, 2025, 2026) at the start of NIM
        if (preg_match('/^(20\d{2})/', $nim, $matches)) {
            $year = (int) substr($matches[1], 2, 2); // e.g. "26"
            $code = sprintf('%02d%02d', $year, $year + 1);
            $level = ProgramLevel::where('code', $code)->first();
            if ($level) {
                return $level;
            }
        }

        // 2. Fallback to standard format: 2-digit year before the last 4 digits (e.g. 25xxxx)
        if (preg_match('/(\d{2})\d{4}$/', $nim, $matches)) {
            $year = (int) $matches[1];
            $code = sprintf('%02d%02d', $year, $year + 1);
            return ProgramLevel::where('code', $code)->first();
        }

        return null;
    }

    private function normalizeProgramName(string $value): string
    {
        $value = Str::of($value)
            ->upper()
            ->replaceMatches('/\s+\d+$/', '')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();

        return $value;
    }

    private function clean(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Handle scientific notation / float strings from Excel large numbers
        if (is_numeric($value) && (str_contains(strtolower((string)$value), 'e+') || is_float($value))) {
            $value = number_format((float)$value, 0, '', '');
        }

        $value = ltrim(trim((string) $value), "'");

        return $value === '' ? null : $value;
    }
}
