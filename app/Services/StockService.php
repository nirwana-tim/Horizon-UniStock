<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Item;
use App\Models\ItemVariant;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockReceive;
use App\Models\StockReceiveItem;
use App\Models\Vendor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function receiveStock(array $data): StockReceive
    {
        return DB::transaction(function () use ($data) {
            $receive = StockReceive::create([
                'reference_number' => $data['reference_number'] ?? $this->generateReferenceNumber(),
                'vendor_id' => $data['vendor_id'],
                'receive_date' => $data['receive_date'],
                'status' => 'received',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);
                $variant = ItemVariant::findOrFail($itemData['variant_id']);

                $receiveItem = StockReceiveItem::create([
                    'stock_receive_id' => $receive->id,
                    'item_id' => $item->id,
                    'variant_id' => $variant->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'hpp' => $itemData['hpp'] ?? $itemData['unit_price'] ?? 0,
                ]);

                StockMovement::create([
                    'item_id' => $item->id,
                    'variant_id' => $variant->id,
                    'type' => 'IN',
                    'quantity' => $itemData['quantity'],
                    'hpp' => $receiveItem->hpp,
                    'reference_type' => StockReceive::class,
                    'reference_id' => $receive->id,
                    'notes' => "Penerimaan dari vendor: {$receive->reference_number}",
                ]);

                $balance = StockBalance::firstOrNew([
                    'item_id' => $item->id,
                    'variant_id' => $variant->id,
                ]);

                $oldQty = $balance->quantity ?? 0;
                $oldHpp = $balance->last_hpp ?? 0;
                $newQty = $itemData['quantity'];
                $newHpp = $receiveItem->hpp;

                // Weighted-average HPP
                $totalQty = $oldQty + $newQty;
                $avgHpp = $totalQty > 0
                    ? (($oldQty * $oldHpp) + ($newQty * $newHpp)) / $totalQty
                    : $newHpp;

                $balance->quantity = $totalQty;
                $balance->last_hpp = round($avgHpp, 2);
                $balance->save();
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'model_type' => StockReceive::class,
                'model_id' => $receive->id,
                'new_values' => $receive->toArray(),
                'ip_address' => request()->ip(),
            ]);

            return $receive->fresh(['items.item', 'items.variant', 'vendor']);
        });
    }

    public function getBalance(int $itemId, ?int $variantId = null): ?StockBalance
    {
        return StockBalance::where('item_id', $itemId)
            ->where('variant_id', $variantId)
            ->first();
    }

    public function getBalanceByItem(Item $item): Collection
    {
        return StockBalance::with('variant')
            ->where('item_id', $item->id)
            ->get();
    }

    public function getAllBalances(?string $category = null, ?string $gender = null): Collection
    {
        $query = StockBalance::with(['item.category', 'variant'])
            ->join('items', 'stock_balances.item_id', '=', 'items.id')
            ->leftJoin('item_variants', 'stock_balances.variant_id', '=', 'item_variants.id')
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->select(
                'stock_balances.*',
                'items.name as item_name',
                'items.code as item_code',
                'items.selling_price',
                'items.unit',
                'item_categories.label as category_name',
                'item_categories.code as category_code',
                'item_variants.size as variant_size'
            )
            ->orderBy('item_categories.code')
            ->orderBy('items.name');

        if ($category) {
            $query->where('item_categories.code', $category);
        }

        return $query->get();
    }

    public function getMovements(int $itemId, ?int $variantId = null, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = StockMovement::with(['item', 'variant'])
            ->where('item_id', $itemId)
            ->orderBy('created_at')
            ->orderBy('id');

        if ($variantId) {
            $query->where('variant_id', $variantId);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->get();
    }

    public function deductStock(int $itemId, int $variantId, int $quantity, string $referenceType, int $referenceId, ?float $hpp = null, ?string $notes = null): void
    {
        DB::transaction(function () use ($itemId, $variantId, $quantity, $referenceType, $referenceId, $hpp, $notes) {
            $balance = StockBalance::where('item_id', $itemId)
                ->where('variant_id', $variantId)
                ->lockForUpdate()
                ->first();

            if (!$balance || $balance->quantity < $quantity) {
                throw new \Exception("Stok tidak mencukupi untuk item #{$itemId} varian #{$variantId}.");
            }

            $actualHpp = $hpp ?? $balance->last_hpp ?? 0;

            $balance->decrement('quantity', $quantity);

            StockMovement::create([
                'item_id' => $itemId,
                'variant_id' => $variantId,
                'type' => 'OUT',
                'quantity' => $quantity,
                'hpp' => $actualHpp,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes ?? 'Pengeluaran stok',
            ]);
        });
    }

    public function getLowStockItems(int $threshold = 5): Collection
    {
        return StockBalance::with(['item.category', 'variant'])
            ->where('quantity', '<=', $threshold)
            ->where('quantity', '>', 0)
            ->orderBy('quantity')
            ->get();
    }

    public function getOutOfStockItems(): Collection
    {
        return StockBalance::with(['item.category', 'variant'])
            ->where('quantity', '<=', 0)
            ->orderBy('item_id')
            ->get();
    }

    private function generateReferenceNumber(): string
    {
        return 'SR-' . date('Ymd') . '-' . str_pad(StockReceive::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function getVendorReceivedItems(int $vendorId): Collection
    {
        return StockReceive::with(['items.item', 'items.variant'])
            ->where('vendor_id', $vendorId)
            ->latest()
            ->get();
    }
}
