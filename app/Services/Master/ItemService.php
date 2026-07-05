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

        // Item code is general: KATEGORI-GENDER-TIPE-DEPARTEMEN (no size suffix)
        $code = $category->code . '-' . $data['gender'] . '-' . ($type?->code ?? 'XX') . '-' . ($department?->code ?? '00');
        $name = $category->label . ' ' . ($genderLabels[$data['gender']] ?? '') . ' ' . ($type?->label ?? '') . ' ' . ($department?->label ?? '');

        if (Item::where('code', $code)->exists()) {
            throw ValidationException::withMessages([
                'category_id' => "Item dengan kode {$code} sudah ada. Kombinasi Kategori-Gender-Tipe-Departemen harus unik.",
            ]);
        }

        $item = Item::create([
            'code' => $code,
            'base_code' => $code,
            'name' => trim($name),
            'gender' => $data['gender'],
            'category_id' => $data['category_id'],
            'type_id' => $data['type_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'unit' => $data['unit'] ?? 'pcs',
            'selling_price' => $data['selling_price'] ?? 0,
            'hpp' => $data['hpp'] ?? 0,
        ]);

        // Create the first variant SKU with the size suffix
        $item->variants()->create([
            'size_id' => $size->id,
            'size' => $size->code,
            'size_label' => $size->label,
            'sku' => $code . '-' . $size->code,
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

        $newCode = $category->code . '-' . $data['gender'] . '-' . ($type?->code ?? 'XX') . '-' . ($department?->code ?? '00');
        $newName = trim($category->label . ' ' . ($genderLabels[$data['gender']] ?? '') . ' ' . ($type?->label ?? '') . ' ' . ($department?->label ?? ''));

        if ($newCode !== $item->code && Item::where('code', $newCode)->exists()) {
            throw ValidationException::withMessages([
                'category_id' => "Item dengan kode {$newCode} sudah ada. Kombinasi Kategori-Gender-Tipe-Departemen harus unik.",
            ]);
        }

        $updateData = $data;
        $updateData['code'] = $newCode;
        $updateData['base_code'] = $newCode;
        $updateData['name'] = $newName;
        unset($updateData['size_id']);

        $item->update($updateData);

        // Update SKUs for all variants if the base code changed
        if ($newCode !== $old['code']) {
            foreach ($item->variants as $var) {
                $var->update([
                    'sku' => $newCode . '-' . $var->size,
                ]);
            }
        }

        // Update the primary/first variant to match the selected size
        $firstVariant = $item->variants()->orderBy('id')->first();
        if ($firstVariant) {
            $firstVariant->update([
                'size_id' => $size->id,
                'size' => $size->code,
                'size_label' => $size->label,
                'sku' => $newCode . '-' . $size->code,
            ]);
        } else {
            $item->variants()->create([
                'size_id' => $size->id,
                'size' => $size->code,
                'size_label' => $size->label,
                'sku' => $newCode . '-' . $size->code,
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
