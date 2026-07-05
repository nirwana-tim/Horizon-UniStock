<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemSizeRequest;
use App\Models\ItemCategory;
use App\Models\ItemSize;
use App\Services\Master\ItemSizeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemSizeController extends Controller
{
    public function __construct(
        protected ItemSizeService $sizeService
    ) {}

    public function index(): View
    {
        $sizes = ItemSize::with('categories')->orderBy('code')->paginate(15);

        return view('master.item-size.index', compact('sizes'));
    }

    public function create(): View
    {
        $categories = ItemCategory::orderBy('code')->get();

        return view('master.item-size.create', compact('categories'));
    }

    public function store(ItemSizeRequest $request): RedirectResponse
    {
        $this->sizeService->store($request->validated());

        return redirect()->route('master-data.item-size.index')->with('success', 'Ukuran item berhasil ditambahkan.');
    }

    public function show(ItemSize $itemSize): View
    {
        $itemSize->load('categories');

        return view('master.item-size.show', compact('itemSize'));
    }

    public function edit(ItemSize $itemSize): View
    {
        $itemSize->load('categories');
        $categories = ItemCategory::orderBy('code')->get();

        return view('master.item-size.edit', compact('itemSize', 'categories'));
    }

    public function update(ItemSizeRequest $request, ItemSize $itemSize): RedirectResponse
    {
        $this->sizeService->update($itemSize, $request->validated());

        return redirect()->route('master-data.item-size.index')->with('success', 'Ukuran item berhasil diperbarui.');
    }

    public function destroy(ItemSize $itemSize): RedirectResponse
    {
        $this->sizeService->destroy($itemSize);

        return redirect()->route('master-data.item-size.index')->with('success', 'Ukuran item berhasil dihapus.');
    }
}
