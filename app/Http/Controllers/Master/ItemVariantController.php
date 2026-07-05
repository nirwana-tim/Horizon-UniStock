<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemVariantRequest;
use App\Models\Item;
use App\Models\ItemVariant;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;

class ItemVariantController extends Controller
{
    public function store(Item $item, ItemVariantRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Auto-generate SKU if empty
        if (empty($data['sku'])) {
            $baseCode = $item->base_code;
            if (empty($baseCode)) {
                $parts = explode('-', $item->code);
                array_pop($parts);
                $baseCode = implode('-', $parts);
            }
            $data['sku'] = $baseCode . '-' . $data['size'];
        }

        // Auto-populate size_label from ItemSize model
        if (empty($data['size_label'])) {
            $itemSize = \App\Models\ItemSize::find($data['size_id']);
            if ($itemSize) {
                $data['size_label'] = $itemSize->label;
            }
        }

        $variant = $item->variants()->create($data);

        AuditService::log('create', ItemVariant::class, $variant->id, null, $variant->toArray());

        return back()->with('success', 'Varian berhasil ditambahkan.');
    }

    public function destroy(Item $item, ItemVariant $variant): RedirectResponse
    {
        $oldValues = $variant->toArray();
        $variant->delete();

        AuditService::log('delete', ItemVariant::class, $variant->id, $oldValues, null);

        return back()->with('success', 'Varian berhasil dihapus.');
    }
}
