<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use App\Models\Item;
use App\Models\ItemVariant;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockOpnameTemplateExport extends BaseExport implements FromArray, WithCustomStartCell, WithHeadings, WithStyles, WithTitle
{
    public function startCell(): string
    {
        return 'A4';
    }

    public function array(): array
    {
        return [
            ['UNF-L-SCB-02', 'M', 50],
            ['UNF-P-SCB-02', 'L', 45],
            ['SHO-L-CLG-02', '42', 30],
            ['KTM-U-KTM-01', 'All Size', 200],
            ['KIT-U-NUR-06', 'All Size', 80],
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Barang *',
            'Varian Ukuran *',
            'Quantity Fisik *',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        $colCount = 3;

        $this->setTitle($sheet, 'TEMPLATE IMPORT STOCK OPNAME', $colCount);
        $this->setSubtitle($sheet, 'Kode Barang: UNF-L-SCB-02. Varian Ukuran: S/M/L/XL (atau All Size). Quantity Fisik: jumlah stok nyata di lapangan.', $colCount);

        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A3', 'Contoh: UNF-L-SCB-02 | M | 50');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '888888'], 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
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

        $lastCol = Coordinate::stringFromColumnIndex($colCount);
        $sheet->freezePane('A'.($headerRow + 1));
        $sheet->setAutoFilter('A'.$headerRow.':'.$lastCol.$headerRow);

        $this->setDropdown($sheet, 'A5:A500', Item::pluck('code')->toArray());
        $this->setDropdown($sheet, 'B5:B500', ItemVariant::pluck('size_label')->toArray());

        return null;
    }

    public function title(): string
    {
        return 'Data';
    }
}
