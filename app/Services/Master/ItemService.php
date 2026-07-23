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
        $sizes = ItemSize::whereIn('id', $data['size_ids'])->get();

        $genderLabels = ['L' => 'Laki - Laki', 'P' => 'Perempuan', 'U' => 'Unisex'];

        $code = $category->code . '-' . $data['gender'] . '-' . ($type?->code ?? 'XX') . '-' . ($department?->code ?? '00');
        $name = $category->label . ' ' . ($genderLabels[$data['gender']] ?? '') . ' ' . ($type?->label ?? '') . ' ' . ($department?->label ?? '');

        $item = Item::firstOrCreate(
            ['code' => $code],
            [
                'base_code' => $code,
                'name' => trim($name),
                'gender' => $data['gender'],
                'category_id' => $data['category_id'],
                'type_id' => $data['type_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'unit' => $data['unit'] ?? 'pcs',
                'selling_price' => $data['selling_price'] ?? 0,
                'hpp' => $data['hpp'] ?? 0,
            ]
        );

        foreach ($sizes as $size) {
            $item->variants()->firstOrCreate(
                ['size_id' => $size->id],
                [
                    'size' => $size->code,
                    'size_label' => $size->label,
                    'sku' => $code . '-' . $size->code,
                ]
            );
        }

        $auditData = $data;
        unset($auditData['size_ids']);
        AuditService::log('create', 'item', $item->id, null, $auditData);

        return $item;
    }

    public function update(Item $item, array $data): Item
    {
        $old = $item->toArray();

        $category = ItemCategory::findOrFail($data['category_id']);
        $type = !empty($data['type_id']) ? ItemType::findOrFail($data['type_id']) : null;
        $department = !empty($data['department_id']) ? ItemDepartment::findOrFail($data['department_id']) : null;
        $sizes = ItemSize::whereIn('id', $data['size_ids'])->get();

        $genderLabels = ['L' => 'Laki - Laki', 'P' => 'Perempuan', 'U' => 'Unisex'];

        $newCode = $category->code . '-' . $data['gender'] . '-' . ($type?->code ?? 'XX') . '-' . ($department?->code ?? '00');
        $newName = trim($category->label . ' ' . ($genderLabels[$data['gender']] ?? '') . ' ' . ($type?->label ?? '') . ' ' . ($department?->label ?? ''));

        if ($newCode !== $item->code && Item::where('code', $newCode)->exists()) {
            throw ValidationException::withMessages([
                'category_id' => "Item dengan kode {$newCode} sudah ada.",
            ]);
        }

        $updateData = $data;
        $updateData['code'] = $newCode;
        $updateData['base_code'] = $newCode;
        $updateData['name'] = $newName;
        unset($updateData['size_ids']);

        $item->update($updateData);

        if ($newCode !== $old['code']) {
            foreach ($item->variants as $var) {
                $var->update([
                    'sku' => $newCode . '-' . $var->size,
                ]);
            }
        }

        foreach ($sizes as $size) {
            $item->variants()->firstOrCreate(
                ['size_id' => $size->id],
                [
                    'size' => $size->code,
                    'size_label' => $size->label,
                    'sku' => $newCode . '-' . $size->code,
                ]
            );
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
