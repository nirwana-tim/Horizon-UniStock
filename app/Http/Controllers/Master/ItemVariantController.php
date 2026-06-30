<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemVariant;
use Illuminate\Http\RedirectResponse;

class ItemVariantController extends Controller
{
    public function store(Item $item): RedirectResponse
    {
        request()->validate([
            'size' => 'required|string|max:10',
            'sku' => 'required|string|max:50|unique:item_variants,sku',
        ]);

        $item->variants()->create(request()->only('size', 'sku'));

        return back()->with('success', 'Varian berhasil ditambahkan.');
    }

    public function destroy(Item $item, ItemVariant $variant): RedirectResponse
    {
        $variant->delete();

        return back()->with('success', 'Varian berhasil dihapus.');
    }
}
