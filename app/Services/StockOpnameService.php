<?php

namespace App\Services;

use App\Models\StockBalance;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameAdjustment;
use App\Models\StockOpnameItem;
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
        $seq = StockOpname::where('period', $period)->count() + 1;

        do {
            $referenceNumber = 'SO-'.$period.'-'.str_pad($seq, 4, '0', STR_PAD_LEFT);
            $seq++;
        } while (StockOpname::where('reference_number', $referenceNumber)->exists());

        $batch = StockOpname::create([
            'reference_number' => $referenceNumber,
            'opname_date' => $data['opname_date'],
            'period' => $data['period'],
            'notes' => $data['notes'] ?? null,
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

        AuditService::log('create', StockOpname::class, $batch->id, null, $batch->toArray());

        return $batch;
    }

    public function processUpload(StockOpname $batch, array $items): void
    {
        DB::transaction(function () use ($batch, $items) {
            foreach ($items as $itemData) {
                $stockBalance = StockBalance::where('item_id', $itemData['item_id'])
                    ->where('variant_id', $itemData['variant_id'] ?? null)
                    ->first();

                $systemQuantity = $stockBalance ? $stockBalance->quantity : 0;

                StockOpnameItem::create([
                    'stock_opname_id' => $batch->id,
                    'item_id' => $itemData['item_id'],
                    'variant_id' => $itemData['variant_id'] ?? null,
                    'system_quantity' => $systemQuantity,
                    'physical_quantity' => $itemData['physical_quantity'],
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            $batch->update(['status' => 'counted']);
        });
    }

    public function calculateVariance(StockOpname $batch): void
    {
        $batch->load('items');

        foreach ($batch->items as $item) {
            $item->update([
                'notes' => $item->variance != 0
                    ? ($item->variance > 0 ? 'Surplus: +'.$item->variance : 'Shortage: '.$item->variance)
                    : 'Sesuai',
            ]);
        }
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
                if ($item->variance == 0) {
                    continue;
                }

                $type = $item->variance > 0 ? 'IN' : 'OUT';
                $quantity = abs($item->variance);

                if ($type === 'OUT') {
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
                            'type' => 'OUT',
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
                        'type' => 'IN',
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

            AuditService::log('approve', StockOpname::class, $batch->id, ['status' => 'counted'], ['status' => 'approved']);
        }, attempts: 5);
    }
}
