<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemTypeRequest;
use App\Models\ItemType;
use App\Services\Master\ItemTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemTypeController extends Controller
{
    public function __construct(
        protected ItemTypeService $typeService
    ) {}

    public function index(): View
    {
        $types = ItemType::with('categories')->withCount('items')->orderBy('code')->paginate(15);

        return view('master.item-type.index', compact('types'));
    }

    public function create(): View
    {
        $categories = \App\Models\ItemCategory::orderBy('code')->get();
        return view('master.item-type.create', compact('categories'));
    }

    public function store(ItemTypeRequest $request): RedirectResponse
    {
        $this->typeService->store($request->validated());

        return redirect()->route('master-data.item-type.index')->with('success', 'Tipe item berhasil ditambahkan.');
    }

    public function show(ItemType $itemType): View
    {
        $itemType->load('categories');
        return view('master.item-type.show', compact('itemType'));
    }

    public function edit(ItemType $itemType): View
    {
        $categories = \App\Models\ItemCategory::orderBy('code')->get();
        $itemType->load('categories');
        return view('master.item-type.edit', compact('itemType', 'categories'));
    }

    public function update(ItemTypeRequest $request, ItemType $itemType): RedirectResponse
    {
        $this->typeService->update($itemType, $request->validated());

        return redirect()->route('master-data.item-type.index')->with('success', 'Tipe item berhasil diperbarui.');
    }

    public function destroy(ItemType $itemType): RedirectResponse
    {
        $this->typeService->destroy($itemType);

        return redirect()->route('master-data.item-type.index')->with('success', 'Tipe item berhasil dihapus.');
    }
}
