<?php

namespace App\Services\Master;

use App\Models\ItemType;
use App\Services\AuditService;

class ItemTypeService
{
    public function store(array $data): ItemType
    {
        $type = ItemType::create($data);
        AuditService::log('create', 'item_type', $type->id, null, $data);
        return $type;
    }

    public function update(ItemType $itemType, array $data): ItemType
    {
        $old = $itemType->toArray();
        $itemType->update($data);
        AuditService::log('update', 'item_type', $itemType->id, $old, $data);
        return $itemType;
    }

    public function destroy(ItemType $itemType): void
    {
        $itemType->delete();
        AuditService::log('delete', 'item_type', $itemType->id);
    }
}
