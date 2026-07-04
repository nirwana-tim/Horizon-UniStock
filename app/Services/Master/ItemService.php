<?php

namespace App\Services\Master;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemDepartment;
use App\Models\ItemSize;
use App\Models\ItemType;
use App\Services\AuditService;
use Illuminate\Validation\ValidationException;

class ItemService
{
    public function store(array $data): Item
    {
        $category = ItemCategory::findOrFail($data['category_id']);
        $type = !empty($data['type_id']) ? ItemType::findOrFail($data['type_id']) : null;
        $department = !empty($data['department_id']) ? ItemDepartment::findOrFail($data['department_id']) : null;
        $size = ItemSize::findOrFail($data['size_id']);

        $genderLabels = ['L' => 'Laki - Laki', 'P' => 'Perempuan', 'U' => 'Unisex'];

        $code = $category->code . '-' . $data['gender'] . '-' . ($type?->code ?? 'XX') . '-' . ($department?->code ?? '00') . '-' . $size->code;
        $name = $category->label . ' ' . ($genderLabels[$data['gender']] ?? '') . ' ' . ($type?->label ?? '') . ' ' . ($department?->label ?? '');

        if (Item::where('code', $code)->exists()) {
            throw ValidationException::withMessages([
                'category_id' => "Item dengan kode {$code} sudah ada. Kombinasi Kategori-Gender-Tipe-Departemen harus unik.",
            ]);
        }

        $item = Item::create([
            'code' => $code,
            'name' => trim($name),
            'gender' => $data['gender'],
            'category_id' => $data['category_id'],
            'type_id' => $data['type_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'unit' => $data['unit'] ?? 'pcs',
            'selling_price' => $data['selling_price'] ?? 0,
            'hpp' => $data['hpp'] ?? 0,
        ]);

        $item->variants()->create([
            'size_id' => $size->id,
            'size' => $size->code,
            'size_label' => $size->label,
            'sku' => $code,
        ]);

        $auditData = $data;
        unset($auditData['size_id']);
        AuditService::log('create', 'item', $item->id, null, $auditData);

        return $item;
    }

    public function update(Item $item, array $data): Item
    {
        $old = $item->toArray();

        $category = ItemCategory::findOrFail($data['category_id']);
        $type = !empty($data['type_id']) ? ItemType::findOrFail($data['type_id']) : null;
        $department = !empty($data['department_id']) ? ItemDepartment::findOrFail($data['department_id']) : null;
        $size = ItemSize::findOrFail($data['size_id']);

        $genderLabels = ['L' => 'Laki - Laki', 'P' => 'Perempuan', 'U' => 'Unisex'];

        $newCode = $category->code . '-' . $data['gender'] . '-' . ($type?->code ?? 'XX') . '-' . ($department?->code ?? '00') . '-' . $size->code;
        $newName = trim($category->label . ' ' . ($genderLabels[$data['gender']] ?? '') . ' ' . ($type?->label ?? '') . ' ' . ($department?->label ?? ''));

        if ($newCode !== $item->code && Item::where('code', $newCode)->exists()) {
            throw ValidationException::withMessages([
                'category_id' => "Item dengan kode {$newCode} sudah ada. Kombinasi Kategori-Gender-Tipe-Departemen harus unik.",
            ]);
        }

        $updateData = $data;
        $updateData['code'] = $newCode;
        $updateData['name'] = $newName;
        unset($updateData['size_id']);

        $item->update($updateData);

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

        AuditService::log('update', 'item', $item->id, $old, $updateData);

        return $item;
    }

    public function destroy(Item $item): void
    {
        AuditService::log('delete', 'item', $item->id, $item->toArray(), null);
        $item->delete();
    }
}
