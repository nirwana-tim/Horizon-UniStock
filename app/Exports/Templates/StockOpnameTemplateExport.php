<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockOpnameTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithCustomStartCell
{
    public function startCell(): string
    {
        return 'A4';
    }

    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'Kode Barang *',
            'Varian Ukuran *',
            'Quantity Fisik *',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 3;

        $this->setTitle($sheet, 'TEMPLATE IMPORT STOCK OPNAME', $colCount);
        $this->setSubtitle($sheet, 'Kode Barang: UNF-L-SCB-02-03. Varian Ukuran: S/M/L/XL (atau All Size). Quantity Fisik: jumlah stok nyata di lapangan.', $colCount);

        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A3', 'Contoh: UNF-L-SCB-02-03 | M | 50');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '888888'], 'size' => 10],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        $headerRow = $this->headerRow();
        $this->applyHeaderStyle($sheet, $headerRow, $colCount);

        $this->setColumnWidths($sheet, [
            'A' => 22,
            'B' => 20,
            'C' => 18,
        ]);

        $dataStart = $this->dataStartRow();
        $dataEnd = 1000;
        $this->setFormatNumber($sheet, 'C', $dataStart, $dataEnd);

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
        $sheet->freezePane('A' . ($headerRow + 1));
        $sheet->setAutoFilter('A' . $headerRow . ':' . $lastCol . $headerRow);
    }

    public function title(): string
    {
        return 'Data';
    }
}
