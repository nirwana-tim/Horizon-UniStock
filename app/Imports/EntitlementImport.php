<?php

namespace App\Imports;

use App\Models\Entitlement;
use App\Models\EntitlementItem;
use App\Models\Item;
use App\Models\ProgramLevel;
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
            $programLevel = ProgramLevel::where('name', $row['prodi_level'])->first();
            if (!$programLevel) {
                continue;
            }

            $studentType = match (strtolower($row['tipe'])) {
                'year 1 sem 1', 'year_1_sem_1' => 'year_1_sem_1',
                'year 1 sem 2', 'year_1_sem_2' => 'year_1_sem_2',
                'year 2 sem 3', 'year_2_sem_3' => 'year_2_sem_3',
                'year 2 sem 4', 'year_2_sem_4' => 'year_2_sem_4',
                default => 'continuing',
            };

            $codes = Student::where('program_level_id', $programLevel->id)
                ->where('student_type', $studentType)
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
                        'student_type' => $studentType,
                    ],
                    [
                        'description' => "Hak barang {$studentType} {$row['prodi_level']}",
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
            'tipe' => ['required', 'string', 'in:Freshman,Continuing,freshman,continuing'],
        ];
    }
}
