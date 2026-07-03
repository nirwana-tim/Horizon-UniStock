<?php

namespace App\Exports\Reports;

use App\Exports\BaseExport;
use App\Models\StockOpnameAdjustment;
use App\Models\StockOpnameItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LossReport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private int $row = 0;

    public function __construct(
        private ?string $period = null,
        private ?string $category = null
    ) {}

    public function collection()
    {
        $query = StockOpnameItem::select(
                'items.name as item_name',
                'item_categories.name as category_name',
                'item_categories.code as category_code',
                DB::raw('SUM(CASE WHEN stock_opname_items.variance < 0 THEN ABS(stock_opname_items.variance) ELSE 0 END) as qty_loss'),
                DB::raw('SUM(CASE WHEN stock_opname_items.variance > 0 THEN stock_opname_items.variance ELSE 0 END) as qty_surplus'),
                DB::raw('COUNT(DISTINCT stock_opname_items.stock_opname_id) as opname_count')
            )
            ->join('items', 'stock_opname_items.item_id', '=', 'items.id')
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->groupBy('items.id', 'items.name', 'item_categories.name', 'item_categories.code')
            ->orderBy('item_categories.code')
            ->orderBy('items.name');

        if ($this->period) {
            $query->whereHas('stockOpname', function ($q) {
                $q->where('period', $this->period);
            });
        }

        if ($this->category) {
            $query->where('item_categories.code', $this->category);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Item / Kategori',
            'QTY Loss',
            'Harga Satuan (Rp)',
            'Total Loss (Rp)',
            'QTY Surplus',
            'Total Surplus (Rp)',
            'Net Loss (Rp)',
        ];
    }

    public function map($item): array
    {
        $this->row++;
        $itemModel = \App\Models\Item::where('name', $item->item_name)->first();
        $hpp = $itemModel?->hpp ?? 0;

        $totalLoss = $item->qty_loss * $hpp;
        $totalSurplus = $item->qty_surplus * $hpp;
        $netLoss = $totalSurplus - $totalLoss;

        return [
            $this->row,
            $item->item_name . ' (' . ($item->category_name ?? '-') . ')',
            $item->qty_loss,
            $hpp,
            $totalLoss,
            $item->qty_surplus,
            $totalSurplus,
            $netLoss,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 8;
        $headerRow = $this->headerRow();
        $dataStart = $this->dataStartRow();
        $lastRow = $dataStart + $this->row - 1;

        $this->setTitle($sheet, 'LAPORAN SUSUT STOK (LOSS/GAIN)', $colCount);
        $this->setSubtitle($sheet, 'Periode: ' . ($this->period ?? 'Semua Periode'), $colCount);

        $this->applyHeaderStyle($sheet, $headerRow, $colCount);
        $this->applyDataStyle($sheet, $dataStart, $lastRow, $colCount);

        for ($i = $dataStart; $i <= $lastRow; $i++) {
            $netLoss = $sheet->getCell('H' . $i)->getValue();
            if (is_numeric($netLoss)) {
                if ($netLoss > 0) {
                    $sheet->getStyle('H' . $i)->getFont()->getColor()->setRGB('006600');
                } elseif ($netLoss < 0) {
                    $sheet->getStyle('H' . $i)->getFont()->getColor()->setRGB('CC0000');
                }
            }

            $totalLoss = $sheet->getCell('E' . $i)->getValue();
            if (is_numeric($totalLoss) && $totalLoss > 0) {
                $sheet->getStyle('E' . $i)->getFont()->getColor()->setRGB('CC0000');
            }

            $totalSurplus = $sheet->getCell('G' . $i)->getValue();
            if (is_numeric($totalSurplus) && $totalSurplus > 0) {
                $sheet->getStyle('G' . $i)->getFont()->getColor()->setRGB('006600');
            }
        }

        if ($lastRow >= $dataStart) {
            $totalRow = $lastRow + 1;
            $this->applyTotalStyle($sheet, $totalRow, $colCount);

            $sheet->setCellValue('A' . $totalRow, 'GRAND TOTAL');
            $sheet->setCellValue('C' . $totalRow, '=SUM(C' . $dataStart . ':C' . $lastRow . ')');
            $sheet->setCellValue('E' . $totalRow, '=SUM(E' . $dataStart . ':E' . $lastRow . ')');
            $sheet->setCellValue('F' . $totalRow, '=SUM(F' . $dataStart . ':F' . $lastRow . ')');
            $sheet->setCellValue('G' . $totalRow, '=SUM(G' . $dataStart . ':G' . $lastRow . ')');
            $sheet->setCellValue('H' . $totalRow, '=SUM(H' . $dataStart . ':H' . $lastRow . ')');

            $this->setFormatRupiah($sheet, 'D', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'E', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'G', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'H', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'C', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'F', $dataStart, $totalRow);
        }

        $this->setColumnWidths($sheet, [
            'A' => 5, 'B' => 35, 'C' => 12, 'D' => 18,
            'E' => 18, 'F' => 12, 'G' => 18, 'H' => 18,
        ]);

        $sheet->freezePane('A' . ($headerRow + 1));
    }
}
