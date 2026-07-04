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
use App\Services\AuditService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemController extends Controller
{
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
        $category = ItemCategory::findOrFail($request->category_id);
        $type = $request->type_id ? ItemType::findOrFail($request->type_id) : null;
        $department = $request->department_id ? ItemDepartment::findOrFail($request->department_id) : null;
        $size = ItemSize::findOrFail($request->size_id);

        $genderLabels = ['L' => 'Laki - Laki', 'P' => 'Perempuan', 'U' => 'Unisex'];

        $code = $category->code . '-' . $request->gender . '-' . ($type?->code ?? 'XX') . '-' . ($department?->code ?? '00') . '-' . $size->code;
        $name = $category->label . ' ' . ($genderLabels[$request->gender] ?? '') . ' ' . ($type?->label ?? '') . ' ' . ($department?->label ?? '');

        if (Item::where('code', $code)->exists()) {
            throw ValidationException::withMessages([
                'category_id' => "Item dengan kode {$code} sudah ada. Kombinasi Kategori-Gender-Tipe-Departemen harus unik.",
            ]);
        }

        $item = Item::create([
            'code' => $code,
            'name' => trim($name),
            'gender' => $request->gender,
            'category_id' => $request->category_id,
            'type_id' => $request->type_id,
            'department_id' => $request->department_id,
            'unit' => $request->unit ?? 'pcs',
            'selling_price' => $request->selling_price ?? 0,
            'hpp' => $request->hpp ?? 0,
        ]);

        $item->variants()->create([
            'size_id' => $size->id,
            'size' => $size->code,
            'size_label' => $size->label,
            'sku' => $code,
        ]);

        $auditData = $request->validated();
        unset($auditData['size_id']);
        AuditService::log('create', 'item', $item->id, null, $auditData);

        return redirect()->route('master.item.index')->with('success', 'Item berhasil ditambahkan.');
    }

    public function show(Item $item): View
    {
        $item->load(['category', 'type', 'department', 'variants.itemSize']);

        return view('master.item.show', compact('item'));
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
        $old = $item->toArray();

        $category = ItemCategory::findOrFail($request->category_id);
        $type = $request->type_id ? ItemType::findOrFail($request->type_id) : null;
        $department = $request->department_id ? ItemDepartment::findOrFail($request->department_id) : null;
        $size = ItemSize::findOrFail($request->size_id);

        $genderLabels = ['L' => 'Laki - Laki', 'P' => 'Perempuan', 'U' => 'Unisex'];

        $newCode = $category->code . '-' . $request->gender . '-' . ($type?->code ?? 'XX') . '-' . ($department?->code ?? '00') . '-' . $size->code;
        $newName = trim($category->label . ' ' . ($genderLabels[$request->gender] ?? '') . ' ' . ($type?->label ?? '') . ' ' . ($department?->label ?? ''));

        $data = $request->validated();
        $data['code'] = $newCode;
        $data['name'] = $newName;
        unset($data['size_id']);

        if ($newCode !== $item->code && Item::where('code', $newCode)->where('id', '!=', $item->id)->exists()) {
            throw ValidationException::withMessages([
                'category_id' => "Item dengan kode {$newCode} sudah ada. Kombinasi Kategori-Gender-Tipe-Departemen harus unik.",
            ]);
        }

        $item->update($data);

        $item->variants()->where('size_id', '!=', $size->id)->delete();

        $variant = $item->variants()->where('size_id', $size->id)->first();
        if ($variant) {
            $variant->update([
                'size' => $size->code,
                'size_label' => $size->label,
                'sku' => $newCode,
            ]);
        } else {
            $item->variants()->create([
                'size_id' => $size->id,
                'size' => $size->code,
                'size_label' => $size->label,
                'sku' => $newCode,
            ]);
        }

        AuditService::log('update', 'item', $item->id, $old, $data);

        return redirect()->route('master.item.index')->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        AuditService::log('delete', 'item', $item->id);

        return redirect()->route('master.item.index')->with('success', 'Item berhasil dihapus.');
    }
}
