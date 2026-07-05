<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemRequest;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemDepartment;
use App\Models\ItemSize;
use App\Models\ItemType;
use App\Models\ItemVariant;
use App\Services\Master\ItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        protected ItemService $itemService
    ) {}

    public function index(): View
    {
        $variants = ItemVariant::with(['item.category', 'itemSize'])
            ->orderBy('sku')
            ->paginate(25);

        return view('master.item.index', compact('variants'));
    }

    public function create(): View
    {
        $categories = ItemCategory::with(['sizes', 'types'])->orderBy('code')->get();
        $types = ItemType::orderBy('code')->get();
        $departments = ItemDepartment::orderBy('code')->get();

        $sizesByCategory = $categories->mapWithKeys(fn ($cat) => [$cat->id => $cat->sizes]);
        $typesByCategory = $categories->mapWithKeys(fn ($cat) => [$cat->id => $cat->types]);

        return view('master.item.create', compact('categories', 'types', 'departments', 'sizesByCategory', 'typesByCategory'));
    }

    public function store(ItemRequest $request): RedirectResponse
    {
        $this->itemService->store($request->validated());

        return redirect()->route('master-data.item.index')->with('success', 'Item berhasil ditambahkan.');
    }

    public function show(Item $item): View
    {
        $item->load(['category', 'type', 'department', 'variants.itemSize']);
        $sizes = $item->category ? $item->category->sizes()->orderBy('code')->get() : collect();

        return view('master.item.show', compact('item', 'sizes'));
    }

    public function edit(Item $item): View
    {
        $item->load('variants');
        $categories = ItemCategory::with(['sizes', 'types'])->orderBy('code')->get();
        $types = ItemType::orderBy('code')->get();
        $departments = ItemDepartment::orderBy('code')->get();

        $sizesByCategory = $categories->mapWithKeys(fn ($cat) => [$cat->id => $cat->sizes]);
        $typesByCategory = $categories->mapWithKeys(fn ($cat) => [$cat->id => $cat->types]);

        return view('master.item.edit', compact('item', 'categories', 'types', 'departments', 'sizesByCategory', 'typesByCategory'));
    }

    public function update(ItemRequest $request, Item $item): RedirectResponse
    {
        $this->itemService->update($item, $request->validated());

        return redirect()->route('master-data.item.index')->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $this->itemService->destroy($item);

        return redirect()->route('master-data.item.index')->with('success', 'Item berhasil dihapus.');
    }
}
