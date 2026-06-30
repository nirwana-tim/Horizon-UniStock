<?php

namespace App\Exports;

use App\Models\StockBalance;
use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    private int $row = 0;

    public function collection()
    {
        return StockBalance::with('item', 'variant')
            ->join('items', 'stock_balances.item_id', '=', 'items.id')
            ->leftJoin('item_variants', 'stock_balances.variant_id', '=', 'item_variants.id')
            ->select(
                'stock_balances.*',
                'items.name as item_name',
                'items.code as item_code',
                'items.unit',
                'item_variants.size as variant_size'
            )
            ->orderBy('items.name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Item',
            'Nama Item',
            'Varian',
            'Satuan',
            'Stok Saat Ini',
            'Terakhir Update',
        ];
    }

    public function map($balance): array
    {
        $this->row++;

        return [
            $this->row,
            $balance->item_code,
            $balance->item_name,
            $balance->variant_size ?? '-',
            $balance->unit,
            $balance->quantity,
            $balance->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
