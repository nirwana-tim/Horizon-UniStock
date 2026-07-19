<?php

namespace Database\Seeders\Master;

use App\Models\Item;
use App\Models\ItemSize;
use App\Models\ItemVariant;
use Illuminate\Database\Seeder;

class ItemVariantSeeder extends Seeder
{
    public function run(): void
    {
        $item = Item::where('code', 'UNF-L-SCB-02-03')->first();
        $size = ItemSize::where('code', '03')->first();

        if (!$item || !$size) {
            $this->command->warn('Item or Size not found. Run ItemSeeder and ItemSizeSeeder first.');

            return;
        }

        ItemVariant::firstOrCreate(
            ['sku' => 'UNF-L-SCB-02-03'],
            [
                'item_id' => $item->id,
                'size_id' => $size->id,
                'size' => '03',
                'size_label' => 'S',
            ]
        );
    }
}
