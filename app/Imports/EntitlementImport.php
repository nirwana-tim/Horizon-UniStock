<?php

namespace App\Imports;

use App\Models\Entitlement;
use App\Models\EntitlementItem;
use App\Models\Item;
use App\Models\StudentGeneration;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;

class EntitlementImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function headingRow(): int
    {
        return 4;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $generation = StudentGeneration::where('name', $row['prodi_level'])->first();
            if (!$generation) {
                continue;
            }

            $rawType = strtolower(trim($row['tipe']));
            $studentLevel = match (true) {
                str_contains($rawType, 'year 1 sem 1') || str_contains($rawType, 'y1s1') || str_contains($rawType, 'freshman') => 'Y1S1',
                str_contains($rawType, 'year 1 sem 2') || str_contains($rawType, 'y1s2') => 'Y1S2',
                str_contains($rawType, 'year 2 sem 1') || str_contains($rawType, 'year 2 sem 3') || str_contains($rawType, 'y2s1') || str_contains($rawType, 'y2s3') => 'Y2S1',
                str_contains($rawType, 'year 2 sem 2') || str_contains($rawType, 'year 2 sem 4') || str_contains($rawType, 'y2s2') || str_contains($rawType, 'y2s4') => 'Y2S2',
                str_contains($rawType, 'year 3 sem 1') || str_contains($rawType, 'y3s1') => 'Y3S1',
                str_contains($rawType, 'year 3 sem 2') || str_contains($rawType, 'y3s2') => 'Y3S2',
                str_contains($rawType, 'year 4 sem 1') || str_contains($rawType, 'y4s1') => 'Y4S1',
                str_contains($rawType, 'year 4 sem 2') || str_contains($rawType, 'y4s2') => 'Y4S2',
                str_contains($rawType, 'continuing') => 'Y2S1',
                default => 'Y2S1',
            };

            $codes = Student::where('generation_id', $generation->id)
                ->where('student_level', $studentLevel)
                ->whereNotNull('entitlement_code')
                ->distinct()
                ->pluck('entitlement_code');

            if ($codes->isEmpty()) {
                continue;
            }

            foreach ($codes as $code) {
                $entitlement = Entitlement::updateOrCreate(
                    [
                        'code' => $code,
                        'student_level' => $studentLevel,
                    ],
                    [
                        'description' => "Hak barang {$studentLevel} {$row['prodi_level']}",
                        'is_active' => true,
                    ]
                );

                $itemColumns = collect($row)->except(['prodi_level', 'tipe']);
                foreach ($itemColumns as $itemName => $quantity) {
                    if (empty($quantity) || (int) $quantity <= 0) {
                        continue;
                    }

                    $item = $this->findItemByName($itemName);
                    if (!$item) {
                        continue;
                    }

                    EntitlementItem::updateOrCreate(
                        [
                            'entitlement_id' => $entitlement->id,
                            'item_id' => $item->id,
                        ],
                        ['quantity' => (int) $quantity]
                    );
                }
            }
        }
    }

    protected function findItemByName(string $name): ?Item
    {
        $trimmed = trim($name);
        return Item::where('name', $trimmed)->first()
            ?? Item::where('name', 'like', "%{$trimmed}%")
                ->orderByRaw('LENGTH(name) ASC')
                ->first()
            ?? Item::whereHas('category', fn ($q) => $q->where('label', 'like', "%{$trimmed}%"))
                ->orderByRaw('LENGTH(name) ASC')
                ->first();
    }

    public function rules(): array
    {
        return [
            'prodi_level' => ['required', 'string'],
            'tipe' => ['required', 'string'],
        ];
    }
}
