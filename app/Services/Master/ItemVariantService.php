<?php

namespace App\Services\Master;

use App\Models\Item;
use App\Models\ItemVariant;
use App\Services\AuditService;

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
            $data['sku'] = $baseCode . '-' . $data['size'];
        }

        if (empty($data['size_label'])) {
            $itemSize = \App\Models\ItemSize::find($data['size_id']);
            if ($itemSize) {
                $data['size_label'] = $itemSize->label;
            }
        }

        $variant = $item->variants()->create($data);

        AuditService::log('create', ItemVariant::class, $variant->id, null, $variant->toArray());

        return $variant;
    }

    public function destroy(Item $item, ItemVariant $variant): void
    {
        $oldValues = $variant->toArray();
        $variant->delete();

        AuditService::log('delete', ItemVariant::class, $variant->id, $oldValues, null);
    }
}
