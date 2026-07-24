<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemPrice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemPriceImport implements ToModel, WithHeadingRow, WithValidation
{
    public function headingRow(): int
    {
        return 4;
    }

    public function model(array $row): ?ItemPrice
    {
        $item = Item::where('code', $row['kode_barang'])->first();

        if (!$item) {
            return null;
        }

        $effectiveDate = $this->resolveEffectiveDate($row['tahun_akademik'] ?? null);

        return ItemPrice::updateOrCreate(
            [
                'item_id' => $item->id,
                'effective_date' => $effectiveDate,
            ],
            [
                'selling_price' => $row['harga_jual'],
                'hpp' => $row['hpp'],
            ]
        );
    }

    private function resolveEffectiveDate(?string $tahunAkademik): string
    {
        if (!$tahunAkademik) {
            return now()->startOfYear()->toDateString();
        }

        if (preg_match('/^(\d{2,4})\s*\/\s*(\d{2})$/', $tahunAkademik, $matches)) {
            $year = (int) $matches[1];
            $year = $year < 100 ? 2000 + $year : $year;
            return "{$year}-07-01";
        }

        if (strtotime($tahunAkademik)) {
            return date('Y-m-d', strtotime($tahunAkademik));
        }

        return now()->startOfYear()->toDateString();
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
