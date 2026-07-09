<?php

namespace App\Imports;

use App\Models\Entitlement;
use App\Models\EntitlementItem;
use App\Models\Item;
use App\Models\ProgramLevel;
use App\Models\StudyProgram;
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
            $studyProgram = StudyProgram::where('name', $row['prodi_level'])->first();
            $programLevel = ProgramLevel::where('name', $row['prodi_level'])->first();
            $studentType = strtolower($row['tipe']) === 'continuing' ? 'continuing' : 'freshman';

            $entitlement = Entitlement::updateOrCreate(
                [
                    'study_program_id' => $studyProgram?->id,
                    'program_level_id' => $programLevel?->id,
                    'student_type' => $studentType,
                ],
                [
                    'description' => "Hak barang {$studentType} {$row['prodi_level']}",
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

    protected function findItemByName(string $name): ?Item
    {
        return Item::where('name', 'like', "%{$name}%")->orWhereHas('category', function ($q) use ($name) {
            $q->where('label', 'like', "%{$name}%");
        })->first();
    }

    public function rules(): array
    {
        return [
            'prodi_level' => ['required', 'string'],
            'tipe' => ['required', 'string', 'in:Freshman,Continuing,freshman,continuing'],
        ];
    }
}
