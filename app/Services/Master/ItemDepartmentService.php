<?php

namespace App\Services\Master;

use App\Models\ItemDepartment;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ItemDepartmentService
{
    public function store(array $data): ItemDepartment
    {
        // Auto generate sequential numeric code: 01, 02, etc.
        $code = null;
        for ($i = 1; $i <= 99; $i++) {
            $candidate = str_pad($i, 2, '0', STR_PAD_LEFT);
            if (! ItemDepartment::where('code', $candidate)->exists()) {
                $code = $candidate;
                break;
            }
        }

        if (! $code) {
            throw ValidationException::withMessages([
                'label' => 'Semua kode departemen (01-99) sudah terpakai. Harap hubungi admin.',
            ]);
        }

        if (ItemDepartment::where('code', $code)->exists()) {
            throw ValidationException::withMessages([
                'label' => "Kode departemen '{$code}' sudah terpakai. Silakan coba lagi.",
            ]);
        }

        $data['code'] = $code;

        try {
            $department = ItemDepartment::create($data);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                throw ValidationException::withMessages([
                    'label' => "Kode departemen '{$code}' sudah terpakai. Silakan coba lagi.",
                ]);
            }
            throw $e;
        }

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
