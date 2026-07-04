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
        $variant = $item->variants()->create($request->validated());

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
