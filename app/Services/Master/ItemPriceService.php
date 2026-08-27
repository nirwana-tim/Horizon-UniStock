<?php

namespace App\Services\Master;

use App\Models\ItemPrice;

class ItemPriceService
{
    public function store(array $data): ItemPrice
    {
        $itemPrice = ItemPrice::create($data);

        return $itemPrice;
    }

    public function update(ItemPrice $itemPrice, array $data): ItemPrice
    {
        $itemPrice->update($data);

        return $itemPrice;
    }

    public function destroy(ItemPrice $itemPrice): void
    {
        $itemPrice->delete();
    }
}
