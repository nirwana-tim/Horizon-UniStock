<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemPriceRequest;
use App\Models\Item;
use App\Models\ItemPrice;
use App\Services\Master\ItemPriceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemPriceController extends Controller
{
    public function __construct(
        protected ItemPriceService $itemPriceService
    ) {}

    public function index(): View
    {
        $itemPrices = ItemPrice::with('item')
            ->latest('effective_date')
            ->paginate(15);

        return view('master.item-price.index', compact('itemPrices'));
    }

    public function create(): View
    {
        $items = Item::orderBy('code')->get();

        return view('master.item-price.create', compact('items'));
    }

    public function store(ItemPriceRequest $request): RedirectResponse
    {
        $this->itemPriceService->store($request->validated());

        return redirect()->route('master.item-price.index')->with('success', 'Harga item berhasil ditambahkan.');
    }

    public function show(ItemPrice $itemPrice): View
    {
        $itemPrice->load('item');

        return view('master.item-price.show', compact('itemPrice'));
    }

    public function edit(ItemPrice $itemPrice): View
    {
        $items = Item::orderBy('code')->get();

        return view('master.item-price.edit', compact('itemPrice', 'items'));
    }

    public function update(ItemPriceRequest $request, ItemPrice $itemPrice): RedirectResponse
    {
        $this->itemPriceService->update($itemPrice, $request->validated());

        return redirect()->route('master.item-price.index')->with('success', 'Harga item berhasil diperbarui.');
    }

    public function destroy(ItemPrice $itemPrice): RedirectResponse
    {
        $this->itemPriceService->destroy($itemPrice);

        return redirect()->route('master.item-price.index')->with('success', 'Harga item berhasil dihapus.');
    }
}
