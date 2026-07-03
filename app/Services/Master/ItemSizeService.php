<?php

namespace App\Services\Master;

use App\Models\ItemSize;
use App\Services\AuditService;

class ItemSizeService
{
    public function store(array $data): ItemSize
    {
        $size = ItemSize::create($data);
        AuditService::log('create', 'item_size', $size->id, null, $data);
        return $size;
    }

    public function update(ItemSize $itemSize, array $data): ItemSize
    {
        $old = $itemSize->toArray();
        $itemSize->update($data);
        AuditService::log('update', 'item_size', $itemSize->id, $old, $data);
        return $itemSize;
    }

    public function destroy(ItemSize $itemSize): void
    {
        $itemSize->delete();
        AuditService::log('delete', 'item_size', $itemSize->id);
    }
}
