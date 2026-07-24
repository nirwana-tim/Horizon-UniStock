<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemVariant;
use App\Models\StockBalance;
use App\Models\StockOpnameItem;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;

class StockOpnameImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected int $stockOpnameId;

    private int $importedCount = 0;

    public function __construct(int $stockOpnameId)
    {
        $this->stockOpnameId = $stockOpnameId;
    }

    public function getImportedRows(): int
    {
        return $this->importedCount;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $item = Item::where('code', $row['kode_barang'])->first();
            if (!$item) continue;

            $variantLabel = trim((string) ($row['varian_ukuran'] ?? ''));
            $variant = $item->variants()
                ->where('size_label', $variantLabel)
                ->orWhere('size', $variantLabel)
                ->first();

            if (!$variant) continue;

            $stockBalance = StockBalance::where('item_id', $item->id)
                ->where('variant_id', $variant->id)
                ->first();

            $systemQuantity = $stockBalance?->quantity ?? 0;
            $physicalQuantity = (int) $row['quantity_fisik'];

            StockOpnameItem::create([
                'stock_opname_id' => $this->stockOpnameId,
                'item_id' => $item->id,
                'variant_id' => $variant->id,
                'system_quantity' => $systemQuantity,
                'physical_quantity' => $physicalQuantity,
            ]);

            $this->importedCount++;
        }
    }

    public function rules(): array
    {
        return [
            'kode_barang' => ['required', 'string', 'exists:items,code'],
            'varian_ukuran' => ['required', 'string'],
            'quantity_fisik' => ['required', 'integer', 'min:0'],
        ];
    }
}
