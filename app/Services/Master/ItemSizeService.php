<?php

namespace App\Services\Master;

use App\Models\ItemSize;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

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
                if (! ItemSize::where('code', $candidate)->exists()) {
                    $code = $candidate;
                    break;
                }
            }
            if (! $code) {
                $code = substr(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $label)), 0, 3);
            }
        }

        if (ItemSize::where('code', $code)->exists()) {
            throw ValidationException::withMessages([
                'label' => "Kode ukuran '{$code}' untuk label '{$label}' sudah terpakai. Harap gunakan label lain.",
            ]);
        }

        $data['code'] = $code;

        try {
            $size = ItemSize::create($data);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                throw ValidationException::withMessages([
                    'label' => "Kode ukuran '{$code}' sudah terpakai. Silakan coba lagi.",
                ]);
            }
            throw $e;
        }
        $size->categories()->sync($categoryIds);

        return $size;
    }

    public function update(ItemSize $itemSize, array $data): ItemSize
    {
        $categoryIds = $data['categories'] ?? [];
        unset($data['categories']);
        unset($data['code']); // Protect code from modification

        $itemSize->update($data);
        $itemSize->categories()->sync($categoryIds);

        return $itemSize;
    }

    public function destroy(ItemSize $itemSize): void
    {
        $itemSize->delete();
    }

    public function toggleTag(ItemSize $itemSize, string $field, bool $value): ItemSize
    {
        $itemSize->update([$field => $value]);

        return $itemSize;
    }
}
