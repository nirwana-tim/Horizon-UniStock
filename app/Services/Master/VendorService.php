<?php

namespace App\Services\Master;

use App\Models\Vendor;

class VendorService
{
    public function store(array $data): Vendor
    {
        $vendor = Vendor::create($data);

        return $vendor;
    }

    public function update(Vendor $vendor, array $data): Vendor
    {
        $vendor->update($data);

        return $vendor;
    }

    public function destroy(Vendor $vendor): void
    {
        $vendor->delete();
    }
}
