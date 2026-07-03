<?php

namespace App\Exports;

use App\Models\StockBalance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReportExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private int $row = 0;
    private string $lastCategory = '';

    public function __construct(
        private ?string $category = null,
        private ?string $gender = null
    ) {}

    public function collection()
    {
        $query = StockBalance::with('item.category', 'variant')
            ->join('items', 'stock_balances.item_id', '=', 'items.id')
            ->leftJoin('item_variants', 'stock_balances.variant_id', '=', 'item_variants.id')
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->select(
                'stock_balances.*',
                'items.name as item_name',
                'items.code as item_code',
                'items.unit',
                'item_categories.name as category_name',
                'item_categories.code as category_code',
                'item_variants.size as variant_size'
            )
            ->orderBy('item_categories.code')
            ->orderBy('items.name');

        if ($this->category) {
            $query->where('item_categories.code', $this->category);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Item',
            'Nama Item',
            'Kategori',
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
            $balance->category_name ?? '-',
            $balance->variant_size ?? '-',
            $balance->unit,
            $balance->quantity,
            $balance->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 8;
        $headerRow = $this->headerRow();
        $dataStart = $this->dataStartRow();
        $lastRow = $dataStart + $this->row - 1;

        $this->setTitle($sheet, 'LAPORAN INVENTARIS', $colCount);
        $this->setSubtitle($sheet, 'Periode: ' . now()->format('d/m/Y'), $colCount);

        $this->applyHeaderStyle($sheet, $headerRow, $colCount);
        $this->applyDataStyle($sheet, $dataStart, $lastRow, $colCount);

        $this->setColumnWidths($sheet, [
            'A' => 5, 'B' => 20, 'C' => 35, 'D' => 14,
            'E' => 10, 'F' => 10, 'G' => 14, 'H' => 18,
        ]);

        $sheet->freezePane('A' . ($headerRow + 1));
    }
}
