<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemVariantRequest;
use App\Models\Item;
use App\Models\ItemVariant;
use App\Services\Master\ItemVariantService;
use Illuminate\Http\RedirectResponse;

class ItemVariantController extends Controller
{
    public function __construct(
        protected ItemVariantService $itemVariantService
    ) {}

    public function store(Item $item, ItemVariantRequest $request): RedirectResponse
    {
        $this->itemVariantService->store($item, $request->validated());

        return back()->with('success', 'Varian berhasil ditambahkan.');
    }

    public function destroy(Item $item, ItemVariant $variant): RedirectResponse
    {
        $this->itemVariantService->destroy($item, $variant);

        return back()->with('success', 'Varian berhasil dihapus.');
    }
}
