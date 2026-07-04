<?php

namespace App\Exports\Reports;

use App\Exports\BaseExport;
use App\Models\StockBalance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        $query = StockBalance::with('item.category', 'item.variants', 'variant')
            ->join('items', 'stock_balances.item_id', '=', 'items.id')
            ->leftJoin('item_variants', 'stock_balances.variant_id', '=', 'item_variants.id')
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->select(
                'stock_balances.*',
                'items.name as item_name',
                'items.code as item_code',
                'items.selling_price',
                'items.hpp',
                'items.unit',
                'item_categories.label as category_name',
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
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Gender',
            'Ukuran',
            'Stok Awal',
            'Stok Masuk',
            'Stok Keluar',
            'Stok Akhir',
            'Nilai Stok (Rp)',
        ];
    }

    public function map($balance): array
    {
        $this->row++;
        $totalIn = $balance->item->stockMovements()
            ->where('variant_id', $balance->variant_id)
            ->where('type', 'IN')->sum('quantity');
        $totalOut = $balance->item->stockMovements()
            ->where('variant_id', $balance->variant_id)
            ->where('type', 'OUT')->sum('quantity');

        return [
            $this->row,
            $balance->item_code,
            $balance->item_name,
            $balance->category_name ?? '-',
            $balance->variant?->item?->category?->name ?? '-',
            $balance->variant_size ?? '-',
            $balance->quantity,
            $totalIn,
            $totalOut,
            $balance->quantity,
            $balance->quantity * ($balance->item->hpp ?? 0),
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 11;
        $headerRow = $this->headerRow();
        $dataStart = $this->dataStartRow();
        $lastRow = $dataStart + $this->row - 1;

        $this->setTitle($sheet, 'LAPORAN STOK INVENTARIS', $colCount);
        $filterText = 'Semua Kategori';
        if ($this->category) {
            $filterText = 'Kategori: ' . $this->category;
        }
        $this->setSubtitle($sheet, 'Periode: ' . now()->format('d/m/Y') . ' | ' . $filterText, $colCount);

        $this->applyHeaderStyle($sheet, $headerRow, $colCount);
        $this->applyDataStyle($sheet, $dataStart, $lastRow, $colCount);

        if ($lastRow >= $dataStart) {
            $totalRow = $lastRow + 1;
            $this->applyTotalStyle($sheet, $totalRow, $colCount);

            $sheet->setCellValue('A' . $totalRow, 'TOTAL');
            $sheet->setCellValue('G' . $totalRow, '=SUM(G' . $dataStart . ':G' . $lastRow . ')');
            $sheet->setCellValue('H' . $totalRow, '=SUM(H' . $dataStart . ':H' . $lastRow . ')');
            $sheet->setCellValue('I' . $totalRow, '=SUM(I' . $dataStart . ':I' . $lastRow . ')');
            $sheet->setCellValue('J' . $totalRow, '=SUM(J' . $dataStart . ':J' . $lastRow . ')');
            $sheet->setCellValue('K' . $totalRow, '=SUM(K' . $dataStart . ':K' . $lastRow . ')');

            $this->setFormatRupiah($sheet, 'K', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'G', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'H', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'I', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'J', $dataStart, $totalRow);
        }

        $this->setColumnWidths($sheet, [
            'A' => 5, 'B' => 22, 'C' => 35, 'D' => 14, 'E' => 10,
            'F' => 10, 'G' => 14, 'H' => 14, 'I' => 14, 'J' => 14, 'K' => 18,
        ]);

        $sheet->freezePane('A' . ($headerRow + 1));
    }
}
