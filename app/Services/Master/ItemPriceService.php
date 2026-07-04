<?php

namespace App\Services\Master;

use App\Models\ItemPrice;
use App\Services\AuditService;

class ItemPriceService
{
    public function store(array $data): ItemPrice
    {
        $itemPrice = ItemPrice::create($data);
        AuditService::log('create', 'item_price', $itemPrice->id, null, $itemPrice->toArray());
        return $itemPrice;
    }

    public function update(ItemPrice $itemPrice, array $data): ItemPrice
    {
        $old = $itemPrice->toArray();
        $itemPrice->update($data);
        AuditService::log('update', 'item_price', $itemPrice->id, $old, $itemPrice->fresh()->toArray());
        return $itemPrice;
    }

    public function destroy(ItemPrice $itemPrice): void
    {
        AuditService::log('delete', 'item_price', $itemPrice->id, $itemPrice->toArray(), null);
        $itemPrice->delete();
    }
}
