<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemRequest;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(): View
    {
        $items = Item::with('category')->latest()->paginate(15);

        return view('master.item.index', compact('items'));
    }

    public function create(): View
    {
        $categories = ItemCategory::orderBy('name')->get();

        return view('master.item.create', compact('categories'));
    }

    public function store(ItemRequest $request): RedirectResponse
    {
        $item = Item::create($request->validated());

        if ($request->has('sizes')) {
            foreach ($request->sizes as $size) {
                if (!empty($size['size'])) {
                    $item->variants()->create([
                        'size' => $size['size'],
                        'sku' => $item->code . '-' . $size['size'],
                    ]);
                }
            }
        }

        return redirect()->route('master.item.index')->with('success', 'Item berhasil ditambahkan.');
    }

    public function show(Item $item): View
    {
        $item->load(['category', 'variants']);

        return view('master.item.show', compact('item'));
    }

    public function edit(Item $item): View
    {
        $item->load('variants');
        $categories = ItemCategory::orderBy('name')->get();

        return view('master.item.edit', compact('item', 'categories'));
    }

    public function update(ItemRequest $request, Item $item): RedirectResponse
    {
        $item->update($request->validated());

        if ($request->has('sizes')) {
            $existingVariants = $item->variants->pluck('size')->toArray();
            $newSizes = collect($request->sizes)->pluck('size')->filter()->toArray();

            $item->variants()->whereNotIn('size', $newSizes)->delete();

            foreach ($request->sizes as $size) {
                if (!empty($size['size']) && !in_array($size['size'], $existingVariants)) {
                    $item->variants()->create([
                        'size' => $size['size'],
                        'sku' => $item->code . '-' . $size['size'],
                    ]);
                }
            }
        }

        return redirect()->route('master.item.index')->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect()->route('master.item.index')->with('success', 'Item berhasil dihapus.');
    }
}
