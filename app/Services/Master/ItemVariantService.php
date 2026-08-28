<?php

namespace App\Services\Master;

use App\Models\Item;
use App\Models\ItemSize;
use App\Models\ItemVariant;

class ItemVariantService
{
    public function store(Item $item, array $data): ItemVariant
    {
        if (empty($data['sku'])) {
            $baseCode = $item->base_code;
            if (empty($baseCode)) {
                $parts = explode('-', $item->code);
                array_pop($parts);
                $baseCode = implode('-', $parts);
            }
            $data['sku'] = $baseCode.'-'.$data['size'];
        }

        if (empty($data['size_label'])) {
            $itemSize = ItemSize::find($data['size_id']);
            if ($itemSize) {
                $data['size_label'] = $itemSize->label;
            }
        }

        $variant = $item->variants()->create($data);

        return $variant;
    }

    public function destroy(Item $item, ItemVariant $variant): void
    {
        abort_if($variant->item_id !== $item->id, 404);

        $variant->delete();
    }
}
