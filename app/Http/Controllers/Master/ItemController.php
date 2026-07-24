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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        protected ItemService $itemService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = Item::with(['category', 'variants.itemSize']);

        if ($search = $request->input('q')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('base_code', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('code')->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('master.item._table', compact('data'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $data])->render(),
            ]);
        }

        return view('master.item.index', compact('data'));
    }

    public function create(): View
    {
        $categories = ItemCategory::orderBy('code')->get();
        $types = ItemType::orderBy('code')->get();
        $departments = ItemDepartment::orderBy('code')->get();

        return view('master.item.create', compact('categories', 'types', 'departments'));
    }

    public function sizesTypesByCategory(Request $request): JsonResponse
    {
        $category = ItemCategory::with(['sizes'])->findOrFail($request->input('category_id'));

        return response()->json([
            'sizes' => $category->sizes,
        ]);
    }

    public function store(ItemRequest $request): RedirectResponse
    {
        $this->itemService->store($request->validated());

        return redirect()->route('master-data.item.index')->with('success', 'Item berhasil ditambahkan.');
    }

    public function show(Item $item): View
    {
        $item->load(['category', 'type', 'department', 'variants.itemSize', 'stockBalances.variant']);
        $sizes = $item->category ? $item->category->sizes()->orderBy('code')->get() : collect();

        return view('master.item.show', compact('item', 'sizes'));
    }

    public function edit(Item $item): View
    {
        $item->load(['category.sizes', 'variants']);
        $categories = ItemCategory::orderBy('code')->get();
        $types = ItemType::orderBy('code')->get();
        $departments = ItemDepartment::orderBy('code')->get();
        $sizes = $item->category ? $item->category->sizes()->orderBy('code')->get() : collect();

        return view('master.item.edit', compact('item', 'categories', 'types', 'departments', 'sizes'));
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
