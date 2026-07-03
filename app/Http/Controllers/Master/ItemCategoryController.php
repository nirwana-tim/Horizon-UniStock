<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemCategoryRequest;
use App\Models\ItemCategory;
use App\Services\Master\ItemCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemCategoryController extends Controller
{
    public function __construct(
        protected ItemCategoryService $categoryService
    ) {}

    public function index(): View
    {
        $categories = ItemCategory::withCount('items')->orderBy('code')->paginate(15);

        return view('master.item-category.index', compact('categories'));
    }

    public function create(): View
    {
        return view('master.item-category.create');
    }

    public function store(ItemCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->store($request->validated());

        return redirect()->route('master.item-category.index')->with('success', 'Kategori item berhasil ditambahkan.');
    }

    public function show(ItemCategory $category): View
    {
        $category->load('items');

        return view('master.item-category.show', compact('category'));
    }

    public function edit(ItemCategory $category): View
    {
        return view('master.item-category.edit', compact('category'));
    }

    public function update(ItemCategoryRequest $request, ItemCategory $category): RedirectResponse
    {
        $this->categoryService->update($category, $request->validated());

        return redirect()->route('master.item-category.index')->with('success', 'Kategori item berhasil diperbarui.');
    }

    public function destroy(ItemCategory $category): RedirectResponse
    {
        $this->categoryService->destroy($category);

        return redirect()->route('master.item-category.index')->with('success', 'Kategori item berhasil dihapus.');
    }
}
