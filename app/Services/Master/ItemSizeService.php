<?php

namespace App\Services\Master;

use App\Models\ItemSize;
use App\Services\AuditService;

class ItemSizeService
{
    public function store(array $data): ItemSize
    {
        $categoryIds = $data['categories'] ?? [];
        unset($data['categories']);

        $label = trim($data['label']);
        if (is_numeric($label)) {
            $code = str_pad($label, 2, '0', STR_PAD_LEFT);
        } else {
            $code = null;
            for ($i = 1; $i <= 99; $i++) {
                $candidate = str_pad($i, 2, '0', STR_PAD_LEFT);
                if (!ItemSize::where('code', '=', $candidate, 'and')->exists()) {
                    $code = $candidate;
                    break;
                }
            }
            if (!$code) {
                $code = substr(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $label)), 0, 3);
            }
        }

        if (ItemSize::where('code', '=', $code, 'and')->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'label' => "Kode ukuran '{$code}' untuk label '{$label}' sudah terpakai. Harap gunakan label lain.",
            ]);
        }

        $data['code'] = $code;

        $size = ItemSize::create($data);
        $size->categories()->sync($categoryIds);

        AuditService::log('create', 'item_size', $size->id, null, $data);
        return $size;
    }

    public function update(ItemSize $itemSize, array $data): ItemSize
    {
        $old = $itemSize->toArray();
        $categoryIds = $data['categories'] ?? [];
        unset($data['categories']);
        unset($data['code']); // Protect code from modification

        $itemSize->update($data);
        $itemSize->categories()->sync($categoryIds);

        AuditService::log('update', 'item_size', $itemSize->id, $old, $data);
        return $itemSize;
    }

    public function destroy(ItemSize $itemSize): void
    {
        AuditService::log('delete', 'item_size', $itemSize->id, $itemSize->toArray(), null);
        $itemSize->delete([]);
    }
}
