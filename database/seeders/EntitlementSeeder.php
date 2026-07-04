<?php

namespace Database\Seeders;

use App\Models\Entitlement;
use App\Models\EntitlementItem;
use App\Models\Item;
use App\Models\ProgramLevel;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class EntitlementSeeder extends Seeder
{
    private array $programs = [
        [
            'program' => 'D3-KEP',
            'level' => 'ANG-2024',
            'type' => 'freshman',
            'semester' => 'ganjil',
            'desc' => 'Paket Freshman D3 Keperawatan 2025/2026',
            'items' => [
                'UNF-U-ALM-02' => 1,
                'UNF-P-CLG-02' => 2,
                'SHO-P-CLC-02' => 1,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
                'KIT-U-NUR-06' => 1,
            ],
        ],
        [
            'program' => 'S1-KEP',
            'level' => 'ANG-2024',
            'type' => 'freshman',
            'semester' => 'ganjil',
            'desc' => 'Paket Freshman S1 Keperawatan 2025/2026',
            'items' => [
                'UNF-U-ALM-02' => 1,
                'UNF-P-CLG-02' => 2,
                'SHO-P-CLC-02' => 1,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
                'KIT-U-NUR-05' => 1,
            ],
        ],
        [
            'program' => 'D3-KEB',
            'level' => 'ANG-2024',
            'type' => 'freshman',
            'semester' => 'ganjil',
            'desc' => 'Paket Freshman D3 Kebidanan 2025/2026',
            'items' => [
                'UNF-U-ALM-02' => 1,
                'UNF-P-CLG-02' => 2,
                'SHO-P-CLC-02' => 1,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
                'KIT-U-MID-02' => 1,
            ],
        ],
        [
            'program' => 'PROF-NERS',
            'level' => 'ANG-2024',
            'type' => 'continuing',
            'semester' => 'ganjil',
            'desc' => 'Paket Profesi Ners 2025/2026',
            'items' => [
                'UNF-P-CLC-02' => 2,
                'SHO-P-CLC-02' => 1,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
                'KIT-U-NUR-09' => 1,
            ],
        ],
        [
            'program' => 'S1-INF',
            'level' => 'ANG-2024',
            'type' => 'freshman',
            'semester' => 'ganjil',
            'desc' => 'Paket Freshman S1 Informatika 2025/2026',
            'items' => [
                'UNF-U-ALM-03' => 1,
                'UNF-L-CLG-03' => 2,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
            ],
        ],
        [
            'program' => 'S1-MNJ',
            'level' => 'ANG-2024',
            'type' => 'freshman',
            'semester' => 'ganjil',
            'desc' => 'Paket Freshman S1 Manajemen 2025/2026',
            'items' => [
                'UNF-U-ALM-04' => 1,
                'UNF-P-CLG-04' => 2,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
            ],
        ],
    ];

    public function run(): void
    {
        $created = 0;
        $skipped = 0;

        foreach ($this->programs as $prog) {
            $studyProgram = StudyProgram::where('code', $prog['program'])->first();
            $programLevel = ProgramLevel::where('code', $prog['level'])->first();

            if (!$studyProgram || !$programLevel) {
                continue;
            }

            $entitlement = Entitlement::firstOrCreate(
                [
                    'study_program_id' => $studyProgram->id,
                    'program_level_id' => $programLevel->id,
                    'student_type' => $prog['type'],
                    'semester' => $prog['semester'],
                ],
                ['description' => $prog['desc']]
            );

            if ($entitlement->wasRecentlyCreated) {
                $created++;
            } else {
                $skipped++;
            }

            foreach ($prog['items'] as $itemCode => $quantity) {
                $item = Item::where('code', $itemCode)->first();
                if (!$item) {
                    continue;
                }

                EntitlementItem::firstOrCreate(
                    [
                        'entitlement_id' => $entitlement->id,
                        'item_id' => $item->id,
                    ],
                    ['quantity' => $quantity]
                );
            }
        }

        $this->command->info("Entitlements created: $created, skipped: $skipped");
    }
}
