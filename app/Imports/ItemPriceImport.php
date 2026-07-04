<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemPrice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemPriceImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?ItemPrice
    {
        $item = Item::where('code', $row['kode_barang'])->first();

        if (!$item) {
            return null;
        }

        return ItemPrice::updateOrCreate(
            [
                'item_id' => $item->id,
            ],
            [
                'selling_price' => $row['harga_jual'],
                'hpp' => $row['hpp'],
                'effective_date' => $row['tahun_akademik'] ?? now()->startOfYear(),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'kode_barang' => ['required', 'string', 'exists:items,code'],
            'tahun_akademik' => ['nullable', 'string'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'hpp' => ['required', 'numeric', 'min:0'],
        ];
    }
}
