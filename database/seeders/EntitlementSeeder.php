<?php

namespace Database\Seeders;

use App\Models\Entitlement;
use App\Models\EntitlementItem;
use App\Models\Item;
use Illuminate\Database\Seeder;

class EntitlementSeeder extends Seeder
{
    private array $entitlements = [
        [
            'code' => '2425FHSD3-KEP',
            'desc' => 'D3 Keperawatan Angkatan 2024/2025',
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
            'code' => '2425FHSS1-KEP',
            'desc' => 'S1 Keperawatan Angkatan 2024/2025',
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
            'code' => '2425FHSD3-KEB',
            'desc' => 'D3 Kebidanan Angkatan 2024/2025',
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
            'code' => '2425FCSS1-INF',
            'desc' => 'S1 Informatika Angkatan 2024/2025',
            'items' => [
                'UNF-U-ALM-03' => 1,
                'UNF-L-CLG-03' => 2,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
            ],
        ],
        [
            'code' => '2425FCSS1-TI',
            'desc' => 'S1 Teknologi Informasi Angkatan 2024/2025',
            'items' => [
                'UNF-U-ALM-03' => 1,
                'UNF-L-CLG-03' => 2,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
            ],
        ],
        [
            'code' => '2425FCSS1-SI',
            'desc' => 'S1 Sistem Informasi Angkatan 2024/2025',
            'items' => [
                'UNF-U-ALM-03' => 1,
                'UNF-L-CLG-03' => 2,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
            ],
        ],
        [
            'code' => '2425FEBS1-MNJ',
            'desc' => 'S1 Manajemen Angkatan 2024/2025',
            'items' => [
                'UNF-U-ALM-04' => 1,
                'UNF-P-CLG-04' => 2,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
            ],
        ],
        [
            'code' => '2425FEBS1-AKT',
            'desc' => 'S1 Akuntansi Angkatan 2024/2025',
            'items' => [
                'UNF-U-ALM-04' => 1,
                'UNF-P-CLG-04' => 2,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
            ],
        ],
        [
            'code' => '2425FTHS1-PAR',
            'desc' => 'S1 Pariwisata Angkatan 2024/2025',
            'items' => [
                'UNF-U-ALM-05' => 1,
                'UNF-P-CLG-05' => 2,
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
            ],
        ],
        [
            'code' => '2324FHSS1-KEP',
            'desc' => 'S1 Keperawatan Angkatan 2023/2024',
            'items' => [
                'KTM-U-KTM-01' => 1,
                'KTM-U-TAG-02' => 1,
            ],
        ],
    ];

    public function run(): void
    {
        $levels = \App\Models\ProgramLevel::all();
        $created = 0;
        $skipped = 0;

        foreach ($levels as $level) {
            foreach ($this->entitlements as $data) {
                // Replace the cohort code prefix (e.g. "2425" or "2324") with the current level's code
                $originalCode = $data['code'];
                $restPart = substr($originalCode, 4);
                $newCode = $level->code . $restPart;

                // Adjust description for current level
                $newDesc = str_replace('Angkatan 2024/2025', $level->name, $data['desc']);
                $newDesc = str_replace('Angkatan 2023/2024', $level->name, $newDesc);

                $entitlement = Entitlement::firstOrCreate(
                    ['code' => $newCode],
                    ['description' => $newDesc]
                );

                if ($entitlement->wasRecentlyCreated) {
                    $created++;
                } else {
                    $skipped++;
                }

                foreach ($data['items'] as $baseCode => $quantity) {
                    // Find representative item by base_code
                    $item = Item::where('base_code', '=', $baseCode, 'and')->first();
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
        }

        $this->command->info("Entitlements created: $created, skipped: $skipped");
    }
}
