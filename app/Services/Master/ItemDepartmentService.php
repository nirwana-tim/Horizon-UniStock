<?php

namespace App\Services\Master;

use App\Models\ItemDepartment;
use App\Services\AuditService;

class ItemDepartmentService
{
    public function store(array $data): ItemDepartment
    {
        $department = ItemDepartment::create($data);
        AuditService::log('create', 'item_department', $department->id, null, $data);
        return $department;
    }

    public function update(ItemDepartment $itemDepartment, array $data): ItemDepartment
    {
        $old = $itemDepartment->toArray();
        $itemDepartment->update($data);
        AuditService::log('update', 'item_department', $itemDepartment->id, $old, $data);
        return $itemDepartment;
    }

    public function destroy(ItemDepartment $itemDepartment): void
    {
        $itemDepartment->delete();
        AuditService::log('delete', 'item_department', $itemDepartment->id);
    }
}
