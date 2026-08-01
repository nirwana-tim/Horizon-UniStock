<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemCategoryRequest;
use App\Models\ItemCategory;
use App\Services\Master\ItemCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemCategoryController extends Controller
{
    public function __construct(
        protected ItemCategoryService $categoryService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = ItemCategory::withCount('items');

        if ($search = $request->input('q')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('code')->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('master.item-category._table', compact('data'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $data])->render(),
            ]);
        }

        return view('master.item-category.index', compact('data'));
    }

    public function create(): View
    {
        return view('master.item-category.create');
    }

    public function store(ItemCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->store($request->validated());

        return redirect()->route('master-data.item-category.index')->with('success', 'Kategori item berhasil ditambahkan.');
    }

    public function show(ItemCategory $itemCategory): View
    {
        $itemCategory->load('items');

        return view('master.item-category.show', compact('itemCategory'));
    }

    public function edit(ItemCategory $itemCategory): View
    {
        return view('master.item-category.edit', compact('itemCategory'));
    }

    public function update(ItemCategoryRequest $request, ItemCategory $itemCategory): RedirectResponse
    {
        $this->categoryService->update($itemCategory, $request->validated());

        return redirect()->route('master-data.item-category.index')->with('success', 'Kategori item berhasil diperbarui.');
    }

    public function destroy(ItemCategory $itemCategory): RedirectResponse
    {
        $this->categoryService->destroy($itemCategory);

        return redirect()->route('master-data.item-category.index')->with('success', 'Kategori item berhasil dihapus.');
    }
}
