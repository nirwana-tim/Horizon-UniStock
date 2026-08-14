<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemVariant;
use App\Models\StockBalance;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\StockReceive;
use App\Models\StockReceiveItem;
use Illuminate\Support\Collection;
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

            $itemIds = array_column($data['items'], 'item_id');
            $variantIds = array_column($data['items'], 'variant_id');
            $preloadedItems = Item::whereIn('id', $itemIds)->get()->keyBy('id');
            $preloadedVariants = ItemVariant::whereIn('id', $variantIds)->get()->keyBy('id');

            foreach ($data['items'] as $itemData) {
                $item = $preloadedItems->get($itemData['item_id']);
                $variant = $preloadedVariants->get($itemData['variant_id']);
                if (! $item || ! $variant) {
                    continue;
                }

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

                StockBatch::create([
                    'item_id' => $item->id,
                    'variant_id' => $variant->id,
                    'quantity_remaining' => $itemData['quantity'],
                    'unit_hpp' => $receiveItem->hpp,
                    'received_date' => $data['receive_date'],
                    'stock_receive_item_id' => $receiveItem->id,
                ]);

                $this->upsertBalance(
                    $item->id,
                    $variant->id,
                    $itemData['quantity'],
                    $receiveItem->hpp
                );
            }

            AuditService::log('create', StockReceive::class, $receive->id, null, $receive->toArray());

            return $receive->fresh(['items.item', 'items.variant', 'vendor']);
        }, attempts: 5);
    }

    private function upsertBalance(int $itemId, ?int $variantId, int $addedQty, float $newHpp): void
    {
        $balance = StockBalance::where('item_id', $itemId)
            ->where('variant_id', $variantId)
            ->lockForUpdate()
            ->first();

        if ($balance) {
            $oldQty = $balance->quantity;
            $oldHpp = $balance->last_hpp;
            $totalQty = $oldQty + $addedQty;
            $avgHpp = $totalQty > 0
                ? (($oldQty * $oldHpp) + ($addedQty * $newHpp)) / $totalQty
                : $newHpp;

            $balance->update([
                'quantity' => $totalQty,
                'last_hpp' => round($avgHpp, 2),
            ]);
        } else {
            retry(5, function () use ($itemId, $variantId, $addedQty, $newHpp) {
                try {
                    StockBalance::create([
                        'item_id' => $itemId,
                        'variant_id' => $variantId,
                        'quantity' => $addedQty,
                        'last_hpp' => round($newHpp, 2),
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    if ((int) $e->getCode() !== 23000) {
                        throw $e;
                    }

                    $existing = StockBalance::where('item_id', $itemId)
                        ->where('variant_id', $variantId)
                        ->lockForUpdate()
                        ->first();

                    if ($existing) {
                        $oldQty = $existing->quantity;
                        $oldHpp = $existing->last_hpp;
                        $totalQty = $oldQty + $addedQty;
                        $avgHpp = $totalQty > 0
                            ? (($oldQty * $oldHpp) + ($addedQty * $newHpp)) / $totalQty
                            : $newHpp;

                        $existing->update([
                            'quantity' => $totalQty,
                            'last_hpp' => round($avgHpp, 2),
                        ]);

                        return;
                    }

                    throw $e;
                }
            }, 100);
        }
    }

    public function increaseBalance(int $itemId, ?int $variantId, int $quantity, float $unitHpp = 0): void
    {
        $balance = StockBalance::where('item_id', $itemId)
            ->where('variant_id', $variantId)
            ->lockForUpdate()
            ->first();

        if ($balance) {
            $balance->increment('quantity', $quantity);
        } else {
            StockBalance::create([
                'item_id' => $itemId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'last_hpp' => round($unitHpp, 2),
            ]);
        }
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

        if ($gender) {
            $query->where('items.gender', $gender);
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

    public function deductStockFifo(int $itemId, int $variantId, int $quantity, string $referenceType, int $referenceId, ?string $notes = null): array
    {
        return DB::transaction(function () use ($itemId, $variantId, $quantity, $referenceType, $referenceId, $notes) {
            $balance = StockBalance::where('item_id', $itemId)
                ->where('variant_id', $variantId)
                ->lockForUpdate()
                ->first();

            if (! $balance || $balance->quantity < $quantity) {
                throw new \Exception("Stok tidak mencukupi untuk item #{$itemId} varian #{$variantId}.");
            }

            $batches = StockBatch::where('item_id', $itemId)
                ->where('variant_id', $variantId)
                ->where('quantity_remaining', '>', 0)
                ->orderBy('received_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $totalInBatches = $batches->sum('quantity_remaining');
            if ($totalInBatches < $quantity) {
                throw new \Exception(
                    "Saldo batch tidak mencukupi untuk item #{$itemId} varian #{$variantId} (batch: {$totalInBatches}, diminta: {$quantity}). Lakukan Stock Opname untuk menyelaraskan saldo."
                );
            }

            $remaining = $quantity;
            $totalCost = 0;
            $consumedBatches = [];

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $consume = min($remaining, $batch->quantity_remaining);

                $batch->decrement('quantity_remaining', $consume);

                StockMovement::create([
                    'item_id' => $itemId,
                    'variant_id' => $variantId,
                    'type' => 'OUT',
                    'quantity' => $consume,
                    'hpp' => $batch->unit_hpp,
                    'stock_batch_id' => $batch->id,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'notes' => $notes ?? 'Pengeluaran stok (FIFO)',
                ]);

                $totalCost += $consume * $batch->unit_hpp;
                $consumedBatches[] = [
                    'batch_id' => $batch->id,
                    'quantity' => $consume,
                    'unit_hpp' => $batch->unit_hpp,
                ];

                $remaining -= $consume;
            }

            $balance->decrement('quantity', $quantity);

            $blendedHpp = $quantity > 0 ? round($totalCost / $quantity, 2) : 0;

            return [
                'blended_hpp' => $blendedHpp,
                'total_cost' => $totalCost,
                'consumed_batches' => $consumedBatches,
            ];
        }, attempts: 5);
    }

    public function getDemandShortageItems(): Collection
    {
        return DB::table('student_size_items')
            ->join('student_size_profiles', 'student_size_items.size_profile_id', '=', 'student_size_profiles.id')
            ->join('items', 'student_size_items.item_id', '=', 'items.id')
            ->leftJoin('item_variants', function ($join) {
                $join->on('student_size_items.item_id', '=', 'item_variants.item_id')
                     ->on('student_size_items.size', '=', 'item_variants.size');
            })
            ->leftJoin('stock_balances', function ($join) {
                $join->on('item_variants.id', '=', 'stock_balances.variant_id');
            })
            ->select(
                'items.id as item_id',
                'items.name as item_name',
                'items.code as item_code',
                'items.unit',
                'student_size_items.size',
                DB::raw('COALESCE(item_variants.size_label, student_size_items.size) as size_label'),
                DB::raw('COALESCE(item_variants.sku, CONCAT(items.code, \'-\', student_size_items.size)) as sku'),
                DB::raw('COUNT(*) as demand'),
                DB::raw('COALESCE(MAX(stock_balances.quantity), 0) as stock')
            )
            ->groupBy(
                'items.id',
                'items.name',
                'items.code',
                'items.unit',
                'student_size_items.size',
                'item_variants.size_label',
                'item_variants.sku'
            )
            ->havingRaw('COUNT(*) > COALESCE(MAX(stock_balances.quantity), 0)')
            ->orderBy('items.name')
            ->orderBy('student_size_items.size')
            ->get()
            ->map(function ($row) {
                $row->shortage = $row->demand - $row->stock;
                $row->status = $row->stock <= 0 ? 'out_of_stock' : 'shortage';
                return $row;
            });
    }

    public function getDemandData(): Collection
    {
        return DB::table('student_size_items')
            ->join('student_size_profiles', 'student_size_items.size_profile_id', '=', 'student_size_profiles.id')
            ->join('items', 'student_size_items.item_id', '=', 'items.id')
            ->leftJoin('item_variants', function ($join) {
                $join->on('student_size_items.item_id', '=', 'item_variants.item_id')
                     ->on('student_size_items.size', '=', 'item_variants.size');
            })
            ->leftJoin('stock_balances', function ($join) {
                $join->on('item_variants.id', '=', 'stock_balances.variant_id');
            })
            ->select(
                'items.id as item_id',
                'items.name as item_name',
                'items.code as item_code',
                'items.unit',
                'student_size_items.size',
                DB::raw('COALESCE(item_variants.size_label, student_size_items.size) as size_label'),
                DB::raw('COALESCE(item_variants.sku, CONCAT(items.code, \'-\', student_size_items.size)) as sku'),
                DB::raw('COUNT(*) as demand'),
                DB::raw('COALESCE(MAX(stock_balances.quantity), 0) as stock')
            )
            ->groupBy(
                'items.id',
                'items.name',
                'items.code',
                'items.unit',
                'student_size_items.size',
                'item_variants.size_label',
                'item_variants.sku'
            )
            ->orderBy('items.name')
            ->orderBy('student_size_items.size')
            ->get()
            ->map(function ($row) {
                $row->shortage = max(0, $row->demand - $row->stock);
                if ($row->stock <= 0 && $row->demand > 0) {
                    $row->status = 'out_of_stock';
                } elseif ($row->shortage > 0) {
                    $row->status = 'shortage';
                } elseif ($row->demand > 0 && $row->stock >= $row->demand) {
                    $row->status = 'fulfilled';
                } else {
                    $row->status = 'excess';
                }
                return $row;
            });
    }

    public function getOutOfStockItems(): Collection
    {
        $balances = StockBalance::with(['item.category', 'variant'])
            ->where('quantity', '<=', 0)
            ->orderBy('item_id')
            ->get();

        $demands = DB::table('student_size_items')
            ->select('item_id', 'size')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('item_id', 'size')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->item_id.'|'.$row->size => (int) $row->total]);

        return $balances->map(function (StockBalance $balance) use ($demands) {
            $balance->demand = $demands[$balance->item_id.'|'.$balance->variant?->size] ?? 0;

            return $balance;
        });
    }

    private function generateReferenceNumber(): string
    {
        $dateStr = date('Ymd');
        $seq = $this->nextSequence('SR', now()->toDateString());

        return 'SR-'.$dateStr.'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function nextSequence(string $type, string $period): int
    {
        return DB::transaction(function () use ($type, $period) {
            DB::table('document_sequences')->insertOrIgnore([
                'type' => $type,
                'period' => $period,
                'value' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('document_sequences')
                ->where('type', $type)
                ->where('period', $period)
                ->update(['value' => DB::raw('LAST_INSERT_ID(value + 1)')]);

            return (int) DB::scalar('SELECT LAST_INSERT_ID()');
        });
    }

    public function reverseStockReceive(StockReceive $receive): void
    {
        DB::transaction(function () use ($receive) {
            $receive->load('items');

            foreach ($receive->items as $item) {
                $batchRemaining = StockBatch::where('stock_receive_item_id', $item->id)
                    ->sum('quantity_remaining');

                if ($batchRemaining < $item->quantity) {
                    throw new \Exception(
                        "Penerimaan {$item->item?->name} sudah ada distribusi/pengeluaran stok. "
                        .'Gunakan Stock Opname untuk koreksi stok.'
                    );
                }
            }

            foreach ($receive->items as $item) {
                StockBatch::where('stock_receive_item_id', $item->id)->forceDelete();

                $balance = StockBalance::where('item_id', $item->item_id)
                    ->where('variant_id', $item->variant_id)
                    ->lockForUpdate()
                    ->first();
                if ($balance) {
                    $balance->decrement('quantity', $item->quantity);
                }
                StockMovement::where('reference_type', StockReceive::class)
                    ->where('reference_id', $receive->id)
                    ->delete();
            }
            $receive->items()->delete();
            $old = $receive->toArray();
            $receive->delete();
            AuditService::log('delete', StockReceive::class, $receive->id, $old, null);
        }, attempts: 5);
    }

    public function returnStockToBatch(int $itemId, int $variantId, int $quantity, string $referenceType, int $referenceId, ?string $notes = null): void
    {
        DB::transaction(function () use ($itemId, $variantId, $quantity, $referenceType, $referenceId, $notes) {
            $outMovements = StockMovement::where('item_id', $itemId)
                ->where('variant_id', $variantId)
                ->where('type', 'OUT')
                ->where('stock_batch_id', '!=', null)
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->get();

            $returnQty = $quantity;

            foreach ($outMovements as $movement) {
                if ($returnQty <= 0) {
                    break;
                }
                $batch = StockBatch::find($movement->stock_batch_id);
                if (! $batch) {
                    continue;
                }

                $returnToBatch = min($returnQty, $movement->quantity);
                $batch->increment('quantity_remaining', $returnToBatch);

                StockMovement::create([
                    'item_id' => $itemId,
                    'variant_id' => $variantId,
                    'type' => 'IN',
                    'quantity' => $returnToBatch,
                    'hpp' => $batch->unit_hpp,
                    'stock_batch_id' => $batch->id,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'notes' => $notes ?? 'Retur barang ke batch asal',
                ]);

                $returnQty -= $returnToBatch;
            }

            if ($returnQty > 0) {
                throw new \Exception("Tidak dapat mengembalikan {$returnQty} unit: batch asal tidak ditemukan.");
            }

            $this->increaseBalance($itemId, $variantId, $quantity);

            AuditService::log('return_stock', StockMovement::class, $referenceId, ['quantity' => $quantity], ['item_id' => $itemId, 'variant_id' => $variantId, 'reference' => $referenceType]);
        }, attempts: 5);
    }

    public function getVendorReceivedItems(int $vendorId): Collection
    {
        return StockReceive::with(['items.item', 'items.variant'])
            ->where('vendor_id', $vendorId)
            ->latest()
            ->get();
    }
}
