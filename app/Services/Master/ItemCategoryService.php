<?php

namespace App\Services\Master;

use App\Models\ItemCategory;
use App\Services\AuditService;

class ItemCategoryService
{
    public function store(array $data): ItemCategory
    {
        $category = ItemCategory::create($data);
        AuditService::log('create', 'item_category', $category->id, null, $data);
        return $category;
    }

    public function update(ItemCategory $category, array $data): ItemCategory
    {
        $old = $category->toArray();
        unset($data['code']);
        $category->update($data);
        AuditService::log('update', 'item_category', $category->id, $old, $data);
        return $category;
    }

    public function destroy(ItemCategory $category): void
    {
        AuditService::log('delete', 'item_category', $category->id, $category->toArray(), null);
        $category->delete([]);
    }
}
