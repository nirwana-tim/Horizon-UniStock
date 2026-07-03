<?php

namespace App\Services\Master;

use App\Models\Vendor;
use App\Services\AuditService;

class VendorService
{
    public function store(array $data): Vendor
    {
        $vendor = Vendor::create($data);
        AuditService::log('create', 'vendor', $vendor->id, null, $data);
        return $vendor;
    }

    public function update(Vendor $vendor, array $data): Vendor
    {
        $old = $vendor->toArray();
        $vendor->update($data);
        AuditService::log('update', 'vendor', $vendor->id, $old, $data);
        return $vendor;
    }

    public function destroy(Vendor $vendor): void
    {
        $vendor->delete();
        AuditService::log('delete', 'vendor', $vendor->id);
    }
}
