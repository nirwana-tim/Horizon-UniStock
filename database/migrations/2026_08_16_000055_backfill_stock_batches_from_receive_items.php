<?php

use App\Models\StockBatch;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockReceive;
use App\Models\StockReceiveItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $receiveItems = StockReceiveItem::with('stockReceive')->get();

        foreach ($receiveItems as $item) {
            $usedFromThisBatch = (int) StockMovement::where('item_id', $item->item_id)
                ->where('variant_id', $item->variant_id)
                ->where('type', 'OUT')
                ->where('reference_type', StockReceive::class)
                ->where('reference_id', $item->stock_receive_id)
                ->sum('quantity');

            $existing = StockBatch::where('stock_receive_item_id', $item->id)->first();
            if ($existing) continue;

            $balance = StockBalance::where('item_id', $item->item_id)
                ->where('variant_id', $item->variant_id)
                ->first();

            $remaining = max(0, $item->quantity - $usedFromThisBatch);

            if ($remaining <= 0) continue;

            StockBatch::create([
                'item_id' => $item->item_id,
                'variant_id' => $item->variant_id,
                'quantity_remaining' => $remaining,
                'unit_hpp' => $item->hpp,
                'received_date' => $item->stockReceive?->receive_date ?? $item->created_at->toDateString(),
                'stock_receive_item_id' => $item->id,
            ]);
        }
    }

    public function down(): void
    {
        StockBatch::whereNotNull('stock_receive_item_id')->delete();
    }
};