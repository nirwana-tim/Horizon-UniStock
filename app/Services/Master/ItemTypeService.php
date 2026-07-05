<?php

namespace App\Services\Master;

use App\Models\ItemType;
use App\Services\AuditService;

class ItemTypeService
{
    public function store(array $data): ItemType
    {
        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        $type = ItemType::create($data);
        $type->categories()->sync($categories);

        AuditService::log('create', 'item_type', $type->id, null, $data);
        return $type;
    }

    public function update(ItemType $itemType, array $data): ItemType
    {
        $old = $itemType->toArray();

        $categories = $data['categories'] ?? [];
        unset($data['categories']);
        unset($data['code']); // Protect code from modification

        $itemType->update($data);
        $itemType->categories()->sync($categories);

        AuditService::log('update', 'item_type', $itemType->id, $old, $data);
        return $itemType;
    }

    public function destroy(ItemType $itemType): void
    {
        AuditService::log('delete', 'item_type', $itemType->id, $itemType->toArray(), null);
        $itemType->delete([]);
    }
}
