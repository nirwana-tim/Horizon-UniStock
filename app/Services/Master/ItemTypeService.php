<?php

namespace App\Services\Master;

use App\Models\ItemType;

class ItemTypeService
{
    public function store(array $data): ItemType
    {
        $type = ItemType::create($data);

        return $type;
    }

    public function update(ItemType $itemType, array $data): ItemType
    {
        unset($data['code']);

        $itemType->update($data);

        return $itemType;
    }

    public function destroy(ItemType $itemType): void
    {
        $itemType->delete();
    }
}
