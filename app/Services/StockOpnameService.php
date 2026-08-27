<?php

namespace App\Services;

use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameAdjustment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameService
{
    public function __construct(
        private readonly StockService $stockService
    ) {}

    public function createBatch(array $data): StockOpname
    {
        $period = $data['period'];
        $seq = $this->stockService->nextSequence('SO', $period);

        $referenceNumber = 'SO-'.$period.'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

        $batch = StockOpname::create([
            'reference_number' => $referenceNumber,
            'opname_date' => $data['opname_date'],
            'period' => $data['period'],
            'notes' => $data['notes'] ?? null,
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

        return $batch;
    }

    public function calculateVariance(StockOpname $batch): void
    {
        DB::transaction(function () use ($batch) {
            $batch->load('items');

            foreach ($batch->items as $item) {
                $item->update([
                    'notes' => $item->variance != 0
                        ? ($item->variance > 0 ? 'Surplus: +'.$item->variance : 'Shortage: '.$item->variance)
                        : 'Sesuai',
                ]);
            }
        });
    }

    public function createAdjustments(StockOpname $batch, User $approver): void
    {
        DB::transaction(function () use ($batch, $approver) {
            $batch = StockOpname::whereKey($batch->id)->lockForUpdate()->first();
            if (! $batch || $batch->status !== 'counted') {
                throw new \Exception('Stock opname sudah di-approve atau tidak dalam status counted.');
            }

            if (! $batch->items()->exists()) {
                throw new \Exception('Stock opname tidak memiliki item. Silakan upload data terlebih dahulu.');
            }

            $batch->load('items.item', 'items.variant');

            foreach ($batch->items as $item) {
                $balance = $this->stockService->getBalance($item->item_id, $item->variant_id);
                $currentSystemQuantity = $balance?->quantity ?? 0;

                $item->update(['system_quantity' => $currentSystemQuantity]);

                if ($item->variance == 0) {
                    continue;
                }

                $type = $item->variance > 0 ? 'IN' : 'OUT';
                $quantity = abs($item->variance);

                if ($type === 'OUT') {
                    $balance = $this->stockService->getBalance($item->item_id, $item->variant_id);
                    $availableStock = $balance?->quantity ?? 0;

                    if ($availableStock <= 0) {
                        $item->update(['notes' => 'Shortage: '.(-1 * $item->variance).' (stok sudah 0, tidak ada adjustment)']);

                        continue;
                    }

                    if ($quantity > $availableStock) {
                        $item->update([
                            'notes' => 'Shortage: '.(-1 * $item->variance).' (diselaraskan ke stok tersedia '.$availableStock.')',
                        ]);
                        $quantity = $availableStock;
                    }

                    $fifoResult = $this->stockService->deductStockFifo(
                        $item->item_id,
                        $item->variant_id,
                        $quantity,
                        StockOpname::class,
                        $batch->id,
                        "Stock Opname adjustment (shortage) - {$batch->reference_number}"
                    );

                    $movements = StockMovement::where('reference_type', StockOpname::class)
                        ->where('reference_id', $batch->id)
                        ->where('item_id', $item->item_id)
                        ->where('variant_id', $item->variant_id)
                        ->latest()
                        ->get();

                    foreach ($movements as $movement) {
                        StockOpnameAdjustment::create([
                            'stock_opname_id' => $batch->id,
                            'stock_movement_id' => $movement->id,
                            'type' => 'shortage',
                            'quantity' => $movement->quantity,
                            'reason' => 'Shortage',
                            'approved_by' => $approver->id,
                            'approved_at' => now(),
                        ]);
                    }
                } else {
                    $balance = $this->stockService->getBalance($item->item_id, $item->variant_id);
                    $unitHpp = $balance?->last_hpp ?? 0;

                    $newBatch = StockBatch::create([
                        'item_id' => $item->item_id,
                        'variant_id' => $item->variant_id,
                        'quantity_remaining' => $quantity,
                        'unit_hpp' => $unitHpp,
                        'received_date' => today(),
                    ]);

                    $stockMovement = StockMovement::create([
                        'item_id' => $item->item_id,
                        'variant_id' => $item->variant_id,
                        'type' => 'IN',
                        'quantity' => $quantity,
                        'hpp' => $unitHpp,
                        'stock_batch_id' => $newBatch->id,
                        'reference_type' => StockOpname::class,
                        'reference_id' => $batch->id,
                        'notes' => "Stock Opname adjustment (surplus) - {$batch->reference_number}",
                    ]);

                    StockOpnameAdjustment::create([
                        'stock_opname_id' => $batch->id,
                        'stock_movement_id' => $stockMovement->id,
                        'type' => 'surplus',
                        'quantity' => $quantity,
                        'reason' => 'Surplus',
                        'approved_by' => $approver->id,
                        'approved_at' => now(),
                    ]);

                    $this->stockService->increaseBalance($item->item_id, $item->variant_id, $quantity, $unitHpp);
                }
            }

            $batch->update([
                'status' => 'approved',
            ]);
        }, attempts: 5);
    }
}
