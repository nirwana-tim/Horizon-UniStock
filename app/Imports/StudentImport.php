<?php

namespace App\Imports;

use App\Models\Faculty;
use App\Models\Item;
use App\Models\Student;
use App\Models\StudentGeneration;
use App\Services\Master\GenerationResolverService;
use App\Models\StudentSizeItem;
use App\Models\StudentSizeProfile;
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
            $student = Student::updateOrCreate(
                ['nim' => $record['nim']],
                [
                    'name' => $record['name'],
                    'email_kampus' => $record['email_kampus'],
                    'email_pribadi' => $record['email_pribadi'],
                    'study_program_id' => $record['study_program']->id,
                    'generation_id' => $record['program_level']->id,
                    'student_level' => $record['student_level'],
                    'entitlement_code' => ($record['student_level'] ?? 'Y1S1')
                        . ($record['study_program']->faculty->code ?? 'FHS')
                        . $record['study_program']->code,
                ]
            );

            // Save shirt size & shoe size if provided
            if (!empty($record['shirt_size']) || !empty($record['shoe_size'])) {
                $profile = StudentSizeProfile::firstOrCreate(
                    ['student_id' => $student->id],
                    ['is_filled' => true, 'filled_at' => now()]
                );

                $items = Item::all();
                if (!empty($record['shirt_size'])) {
                    $shirtItem = $items->first(fn ($i) => str_contains(strtolower($i->name), 'uniform') || str_contains(strtolower($i->name), 'seragam') || str_contains(strtolower($i->code), 'unf')) ?? $items->first();
                    if ($shirtItem) {
                        StudentSizeItem::updateOrCreate(
                            ['size_profile_id' => $profile->id, 'item_id' => $shirtItem->id],
                            ['size' => $record['shirt_size'], 'change_count' => 0]
                        );
                    }
                }

                if (!empty($record['shoe_size'])) {
                    $shoeItem = $items->first(fn ($i) => str_contains(strtolower($i->name), 'sepatu') || str_contains(strtolower($i->name), 'shoe') || str_contains(strtolower($i->code), 'sho'));
                    if ($shoeItem) {
                        StudentSizeItem::updateOrCreate(
                            ['size_profile_id' => $profile->id, 'item_id' => $shoeItem->id],
                            ['size' => $record['shoe_size'], 'change_count' => 0]
                        );
                    }
                }
            }

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

            $nim = $this->clean($values[0] ?? null);
            if (!$nim) {
                continue;
            }

            $emailKampus = $this->clean($values[6] ?? null);
            if (!$emailKampus) {
                $emailKampus = strtolower($nim) . '@krw.horizon.ac.id';
            }

            $records[] = [
                'row' => $index + 1,
                'nim' => $nim,
                'name' => $this->clean($values[1] ?? null),
                'program' => $this->clean($values[2] ?? null),
                'gender' => $this->normalizeGender($this->clean($values[3] ?? null)),
                'shirt_size' => $this->clean($values[4] ?? null),
                'shoe_size' => $this->clean($values[5] ?? null),
                'email_kampus' => $emailKampus,
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

        foreach ($records as &$record) {
            $validator = Validator::make($record, [
                'nim' => ['required', 'string', 'max:20'],
                'name' => ['required', 'string', 'max:255'],
                'program' => ['required', 'string'],
                'email_kampus' => ['required', 'email', 'max:255'],
                'email_pribadi' => ['nullable', 'email', 'max:255'],
            ]);

            foreach ($validator->errors()->messages() as $attribute => $messages) {
                $failures[] = new Failure($record['row'], $attribute, $messages, $record);
            }

            if (in_array($record['nim'], $seenNims, true)) {
                $failures[] = new Failure($record['row'], 'nim', ['NIM duplikat pada file ini.'], $record);
            }
            $seenNims[] = $record['nim'];

            $record['study_program'] = $this->resolveStudyProgram($record['program']);
            $record['program_level'] = $this->resolveProgramLevel($record['nim']);

            $rawType = $record['student_type_raw'] ?? '';

            $record['student_level'] = match (true) {
                str_contains($rawType, 'graduated') || str_contains($rawType, 'lulus') || str_contains($rawType, 'alumni') => 'graduated',
                str_contains($rawType, 'year 1 sem 1') || str_contains($rawType, 'y1s1') || str_contains($rawType, 'freshman') => 'Y1S1',
                str_contains($rawType, 'year 1 sem 2') || str_contains($rawType, 'y1s2') => 'Y1S2',
                str_contains($rawType, 'year 2 sem 1') || str_contains($rawType, 'year 2 sem 3') || str_contains($rawType, 'y2s1') || str_contains($rawType, 'y2s3') => 'Y2S1',
                str_contains($rawType, 'year 2 sem 2') || str_contains($rawType, 'year 2 sem 4') || str_contains($rawType, 'y2s2') || str_contains($rawType, 'y2s4') => 'Y2S2',
                str_contains($rawType, 'year 3 sem 1') || str_contains($rawType, 'y3s1') => 'Y3S1',
                str_contains($rawType, 'year 3 sem 2') || str_contains($rawType, 'y3s2') => 'Y3S2',
                str_contains($rawType, 'year 4 sem 1') || str_contains($rawType, 'y4s1') => 'Y4S1',
                str_contains($rawType, 'year 4 sem 2') || str_contains($rawType, 'y4s2') => 'Y4S2',
                str_contains($rawType, 'continuing') => 'Y2S1',
                default => 'Y1S1',
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

    private function resolveStudyProgram(?string $value): StudyProgram
    {
        if (!$value) {
            $value = 'Umum';
        }

        $normalized = $this->normalizeProgramName($value);

        $found = StudyProgram::query()
            ->with('faculty')
            ->get()
            ->first(function (StudyProgram $program) use ($value, $normalized): bool {
                $pNorm = $this->normalizeProgramName($program->name);
                return Str::lower($program->code) === Str::lower($value)
                    || $pNorm === $normalized
                    || str_contains($pNorm, $normalized)
                    || str_contains($normalized, $pNorm);
            });

        if ($found) {
            return $found;
        }

        $faculty = Faculty::first() ?? Faculty::create(['code' => 'FHS', 'name' => 'Fakultas Kesehatan & Sains']);
        $code = Str::upper(Str::slug($value, ''));
        $code = strlen($code) > 10 ? substr($code, 0, 10) : ($code ?: 'PRODI');

        return StudyProgram::firstOrCreate(
            ['name' => trim($value)],
            [
                'code' => $code,
                'faculty_id' => $faculty->id,
            ]
        );
    }

    private function resolveProgramLevel(?string $nim): StudentGeneration
    {
        return app(GenerationResolverService::class)->resolveFromNim($nim)
            ?? StudentGeneration::first()
            ?? StudentGeneration::create(['code' => '2526', 'name' => '2025/2026']);
    }

    private function normalizeGender(?string $gender): string
    {
        if (!$gender) return 'L';
        $g = Str::upper(trim($gender));
        if ($g === 'P' || str_contains($g, 'PEREMPUAN') || str_contains($g, 'FEMALE')) {
            return 'P';
        }
        return 'L';
    }

    private function normalizeProgramName(string $value): string
    {
        return Str::of($value)
            ->upper()
            ->replaceMatches('/\s+\d+$/', '')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();
    }

    private function clean(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value) && (str_contains(strtolower((string)$value), 'e+') || is_float($value))) {
            $value = number_format((float)$value, 0, '', '');
        }

        $value = ltrim(trim((string) $value), "'");

        return $value === '' ? null : $value;
    }
}
