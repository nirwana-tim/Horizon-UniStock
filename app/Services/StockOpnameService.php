<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Item;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameAdjustment;
use App\Models\StockOpnameItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameService
{
    public function createBatch(array $data): StockOpname
    {
        $referenceNumber = 'SO-' . date('Ym') . '-' . str_pad(StockOpname::count() + 1, 4, '0', STR_PAD_LEFT);

        $batch = StockOpname::create([
            'reference_number' => $referenceNumber,
            'opname_date' => $data['opname_date'],
            'period' => $data['period'],
            'notes' => $data['notes'] ?? null,
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model_type' => StockOpname::class,
            'model_id' => $batch->id,
            'new_values' => $batch->toArray(),
            'ip_address' => request()->ip(),
        ]);

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
                    ? ($item->variance > 0 ? 'Surplus: +' . $item->variance : 'Shortage: ' . $item->variance)
                    : 'Sesuai',
            ]);
        }
    }

    public function createAdjustments(StockOpname $batch, User $approver): void
    {
        DB::transaction(function () use ($batch, $approver) {
            $batch = StockOpname::whereKey($batch->id)->lockForUpdate()->first();
            if (!$batch || $batch->status !== 'counted') {
                throw new \Exception('Stock opname sudah di-approve atau tidak dalam status counted.');
            }
            $batch->load('items.item', 'items.variant');

            foreach ($batch->items as $item) {
                if ($item->variance == 0) {
                    continue;
                }

                $type = $item->variance > 0 ? 'IN' : 'OUT';
                $quantity = abs($item->variance);

                $stockMovement = StockMovement::create([
                    'item_id' => $item->item_id,
                    'variant_id' => $item->variant_id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'reference_type' => StockOpname::class,
                    'reference_id' => $batch->id,
                    'notes' => "Stock Opname adjustment - {$batch->reference_number}",
                ]);

                StockOpnameAdjustment::create([
                    'stock_opname_id' => $batch->id,
                    'stock_movement_id' => $stockMovement->id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'reason' => $item->variance > 0 ? 'Surplus' : 'Shortage',
                    'approved_by' => $approver->id,
                    'approved_at' => now(),
                ]);

                $stockBalance = StockBalance::where('item_id', $item->item_id)
                    ->where('variant_id', $item->variant_id)
                    ->lockForUpdate()
                    ->first();

                if ($stockBalance) {
                    $newQuantity = $type === 'IN'
                        ? $stockBalance->quantity + $quantity
                        : $stockBalance->quantity - $quantity;

                    $stockBalance->update(['quantity' => max(0, $newQuantity)]);
                }
            }

            $batch->update([
                'status' => 'approved',
            ]);

            AuditLog::create([
                'user_id' => $approver->id,
                'action' => 'approve',
                'model_type' => StockOpname::class,
                'model_id' => $batch->id,
                'old_values' => ['status' => 'counted'],
                'new_values' => ['status' => 'approved'],
                'ip_address' => request()->ip(),
            ]);
        });
    }
}
