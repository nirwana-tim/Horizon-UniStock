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
            ->join('items', 'item_variants.item_id', '=', 'items.id')
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->select(
                'item_variants.*',
                'items.name as item_name',
                'items.code as item_code',
                'items.unit',
                'items.selling_price',
                'items.hpp',
                'items.category_id',
                'item_categories.label as category_name',
                'item_categories.code as category_code'
            )
            ->orderBy('item_categories.code')
            ->orderBy('items.code')
            ->orderBy('item_variants.size')
            ->paginate(25);

        return view('master.item.index', compact('variants'));
    }

    public function create(): View
    {
        $categories = ItemCategory::with('sizes')->orderBy('code')->get();
        $types = ItemType::orderBy('code')->get();
        $departments = ItemDepartment::orderBy('code')->get();

        $sizesByCategory = $categories->mapWithKeys(fn ($cat) => [$cat->id => $cat->sizes]);

        return view('master.item.create', compact('categories', 'types', 'departments', 'sizesByCategory'));
    }

    public function store(ItemRequest $request): RedirectResponse
    {
        $this->itemService->store($request->validated());

        return redirect()->route('master-data.item.index')->with('success', 'Item berhasil ditambahkan.');
    }

    public function show(Item $item): View
    {
        $item->load(['category', 'type', 'department', 'variants.itemSize']);
        $sizes = ItemSize::orderBy('code')->get();

        return view('master.item.show', compact('item', 'sizes'));
    }

    public function edit(Item $item): View
    {
        $item->load('variants');
        $categories = ItemCategory::with('sizes')->orderBy('code')->get();
        $types = ItemType::orderBy('code')->get();
        $departments = ItemDepartment::orderBy('code')->get();

        $sizesByCategory = $categories->mapWithKeys(fn ($cat) => [$cat->id => $cat->sizes]);

        return view('master.item.edit', compact('item', 'categories', 'types', 'departments', 'sizesByCategory'));
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
