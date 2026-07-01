<?php

namespace App\Exports\Reports;

use App\Exports\BaseExport;
use App\Models\StockOpname;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockOpnameReport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private int $row = 0;

    public function __construct(
        private StockOpname $stockOpname
    ) {}

    public function collection()
    {
        return $this->stockOpname->items()
            ->with('item.category', 'variant')
            ->join('items', 'stock_opname_items.item_id', '=', 'items.id')
            ->leftJoin('item_variants', 'stock_opname_items.variant_id', '=', 'item_variants.id')
            ->select(
                'stock_opname_items.*',
                'items.name as item_name',
                'items.code as item_code',
                'item_variants.size as variant_size'
            )
            ->orderBy('items.name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Ukuran',
            'Stok Sistem',
            'Stok Fisik',
            'Selisih',
            'Keterangan',
        ];
    }

    public function map($item): array
    {
        $this->row++;

        return [
            $this->row,
            $item->item_code,
            $item->item_name,
            $item->variant_size ?? '-',
            $item->system_quantity,
            $item->physical_quantity,
            $item->variance,
            $item->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 8;
        $headerRow = $this->headerRow();
        $dataStart = $this->dataStartRow();
        $lastRow = $dataStart + $this->row - 1;

        $this->setTitle($sheet, 'LAPORAN STOK OPNAME', $colCount);
        $this->setSubtitle($sheet, 'Periode: ' . ($this->stockOpname->period ?? $this->stockOpname->opname_date->format('d/m/Y'))
            . ' | ' . $this->stockOpname->reference_number, $colCount);

        $this->applyHeaderStyle($sheet, $headerRow, $colCount);
        $this->applyDataStyle($sheet, $dataStart, $lastRow, $colCount);

        for ($i = $dataStart; $i <= $lastRow; $i++) {
            $variance = $sheet->getCell('G' . $i)->getValue();
            if (is_numeric($variance)) {
                if ($variance > 0) {
                    $sheet->getStyle('G' . $i)->getFont()->getColor()->setRGB('006600');
                } elseif ($variance < 0) {
                    $sheet->getStyle('G' . $i)->getFont()->getColor()->setRGB('CC0000');
                }
            }
        }

        if ($lastRow >= $dataStart) {
            $totalRow = $lastRow + 1;
            $this->applyTotalStyle($sheet, $totalRow, $colCount);

            $sheet->setCellValue('A' . $totalRow, 'TOTAL');
            $sheet->setCellValue('E' . $totalRow, '=SUM(E' . $dataStart . ':E' . $lastRow . ')');
            $sheet->setCellValue('F' . $totalRow, '=SUM(F' . $dataStart . ':F' . $lastRow . ')');
            $sheet->setCellValue('G' . $totalRow, '=SUM(G' . $dataStart . ':G' . $lastRow . ')');

            $this->setFormatNumber($sheet, 'E', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'F', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'G', $dataStart, $totalRow);
        }

        $this->setColumnWidths($sheet, [
            'A' => 5, 'B' => 22, 'C' => 35, 'D' => 10,
            'E' => 14, 'F' => 14, 'G' => 12, 'H' => 25,
        ]);

        $sheet->freezePane('A' . ($headerRow + 1));
    }
}
