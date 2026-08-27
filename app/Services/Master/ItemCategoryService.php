<?php

namespace App\Services\Master;

use App\Models\ItemCategory;

class ItemCategoryService
{
    public function store(array $data): ItemCategory
    {
        $category = ItemCategory::create($data);

        return $category;
    }

    public function update(ItemCategory $category, array $data): ItemCategory
    {
        unset($data['code']);
        $category->update($data);

        return $category;
    }

    public function destroy(ItemCategory $category): void
    {
        $category->delete();
    }
}
