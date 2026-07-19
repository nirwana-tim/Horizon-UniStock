<?php

namespace Database\Seeders\Master;

use App\Models\Item;
use App\Models\ItemPrice;
use Illuminate\Database\Seeder;

class ItemPriceSeeder extends Seeder
{
    public function run(): void
    {
        $item = Item::where('code', 'UNF-L-SCB-02-03')->first();

        if (!$item) {
            $this->command->warn('Item not found. Run ItemSeeder first.');

            return;
        }

        ItemPrice::firstOrCreate(
            [
                'item_id' => $item->id,
                'effective_date' => null,
            ],
            [
                'selling_price' => 180000,
                'hpp' => 0,
            ]
        );
    }
}
