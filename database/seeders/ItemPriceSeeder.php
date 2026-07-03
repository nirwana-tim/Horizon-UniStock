<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemPrice;
use App\Models\DistributionPeriod;
use Illuminate\Database\Seeder;

class ItemPriceSeeder extends Seeder
{
    private array $priceGroups = [
        'KTM-U-YDH' => [22 => 5000, 23 => 5000, 24 => 5000, 25 => 5000],
        'KTM-U-KTM' => [22 => 10000, 23 => 10000, 24 => 10000, 25 => 10000],
        'KTM-U-TAG' => [22 => 25000, 23 => 25000, 24 => 25000, 25 => 25000],
        'MRC-U-TBR' => [22 => 110000, 23 => 110000, 24 => 110000, 25 => 110000],
        'KIT-U-MID' => [22 => 150000, 23 => 150000, 24 => 150000, 25 => 150000],
        'UNF-L-SCB' => [22 => 180000, 23 => 185000, 24 => 190000, 25 => 200000],
        'UNF-L-CLC' => [22 => 180000, 23 => 185000, 24 => 190000, 25 => 200000],
        'UNF-L-CLG' => [22 => 180000, 23 => 185000, 24 => 190000, 25 => 200000],
        'UNF-L-COM' => [22 => 180000, 23 => 185000, 24 => 190000, 25 => 200000],
        'UNF-P-SCB' => [22 => 230000, 23 => 235000, 24 => 240000, 25 => 250000],
        'UNF-P-CLC' => [22 => 230000, 23 => 235000, 24 => 240000, 25 => 250000],
        'UNF-P-CLG' => [22 => 230000, 23 => 235000, 24 => 240000, 25 => 250000],
        'UNF-P-COM' => [22 => 230000, 23 => 235000, 24 => 240000, 25 => 250000],
        'UNF-U-LAB' => [22 => 200000, 23 => 236000, 24 => 200000, 25 => 200000],
        'SHO-L-CLC' => [22 => 50000, 23 => 50000, 24 => 50000, 25 => 50000],
        'SHO-L-CLG' => [22 => 50000, 23 => 50000, 24 => 50000, 25 => 50000],
        'SHO-P-CLC' => [22 => 50000, 23 => 50000, 24 => 50000, 25 => 50000],
        'SHO-P-CLG' => [22 => 50000, 23 => 50000, 24 => 50000, 25 => 50000],
        'SHO-U-SCB' => [22 => 50000, 23 => 50000, 24 => 50000, 25 => 50000],
    ];

    public function run(): void
    {
        $periods = DistributionPeriod::all()->keyBy(fn ($p) => $p->name);

        $periodMap = [
            24 => 'Tahun Akademik 2024/2025',
            25 => 'Tahun Akademik 2025/2026',
        ];

        $created = 0;
        $skipped = 0;
        $itemsUpdated = 0;

        foreach ($this->priceGroups as $prefix => $pricesByYear) {
            $items = Item::where('code', 'LIKE', "$prefix-%")->get();

            if ($items->isEmpty()) {
                $this->command->warn("No items found for prefix: $prefix");
                continue;
            }

            $latestPrice = $pricesByYear[25] ?? $pricesByYear[24] ?? 0;

            foreach ($items as $item) {
                foreach ($pricesByYear as $year => $price) {
                    $periodName = $periodMap[$year] ?? null;
                    if (!$periodName || !isset($periods[$periodName])) {
                        continue;
                    }

                    $result = ItemPrice::firstOrCreate(
                        [
                            'item_id' => $item->id,
                            'period_id' => $periods[$periodName]->id,
                        ],
                        [
                            'selling_price' => $price,
                            'hpp' => 0,
                            'effective_date' => "{$year}-09-01",
                        ]
                    );

                    if ($result->wasRecentlyCreated) {
                        $created++;
                    } else {
                        $skipped++;
                    }
                }

                if ($item->selling_price != $latestPrice) {
                    $item->update(['selling_price' => $latestPrice]);
                    $itemsUpdated++;
                }
            }
        }

        $this->command->info("Created: $created ItemPrice records, Skipped: $skipped, Items updated: $itemsUpdated");
    }
}
