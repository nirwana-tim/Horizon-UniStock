<?php

namespace App\Services\Master;

use App\Models\ItemDepartment;

class ItemDepartmentService
{
    public function store(array $data): ItemDepartment
    {
        // Auto generate sequential numeric code: 01, 02, etc.
        $code = null;
        for ($i = 1; $i <= 99; $i++) {
            $candidate = str_pad($i, 2, '0', STR_PAD_LEFT);
            if (! ItemDepartment::where('code', '=', $candidate, 'and')->exists()) {
                $code = $candidate;
                break;
            }
        }
        $data['code'] = $code;

        $department = ItemDepartment::create($data);

        return $department;
    }

    public function update(ItemDepartment $itemDepartment, array $data): ItemDepartment
    {
        unset($data['code']); // Protect code from modification

        $itemDepartment->update($data);

        return $itemDepartment;
    }

    public function destroy(ItemDepartment $itemDepartment): void
    {
        $itemDepartment->delete();
    }
}
