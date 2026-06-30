<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): Item
    {
        $category = ItemCategory::firstOrCreate([
            'name' => $row['kategori'],
        ]);

        return Item::updateOrCreate(
            ['code' => $row['kode']],
            [
                'name' => $row['nama'],
                'category_id' => $category->id,
                'unit' => $row['satuan'],
                'selling_price' => $row['harga_jual'],
                'hpp' => $row['hpp'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:50', 'unique:items,code'],
            'kategori' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:50'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'hpp' => ['required', 'numeric', 'min:0'],
        ];
    }
}
