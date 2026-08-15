<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReceiveTemplateExport extends BaseExport implements FromArray, WithCustomStartCell, WithHeadings, WithStyles, WithTitle
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
            'SKU Varian',
            'QTY *',
            'Harga Satuan (Rp)',
            'HPP (Rp)',
            'Nama Vendor *',
            'Tanggal Terima *',
            'Nomor Ref',
            'Keterangan',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        $colCount = 9;

        $this->setTitle($sheet, 'TEMPLATE IMPORT PENERIMAAN BARANG (STOCK RECEIVE)', $colCount);
        $this->setSubtitle($sheet, 'Kode Barang: UNF-L-SCB-02. SKU Varian: UNF-L-SCB-02-03 (kosongkan jika all size). Tanggal: YYYY-MM-DD.', $colCount);

        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A3', 'Contoh Format: UNF-L-SCB-02 | UNF-L-SCB-02-03 | 100 | 190000 | 150000 | CV Seragam Makmur | 2026-07-01 | PO-001 | (kosong)');
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

        $dataStart = $this->dataStartRow();
        $dataEnd = 1000;

        $this->setColumnWidths($sheet, [
            'A' => 22, 'B' => 24, 'C' => 10, 'D' => 20, 'E' => 20,
            'F' => 24, 'G' => 16, 'H' => 16, 'I' => 30,
        ]);

        $this->setFormatRupiah($sheet, 'D', $dataStart, $dataEnd);
        $this->setFormatRupiah($sheet, 'E', $dataStart, $dataEnd);
        $this->setFormatNumber($sheet, 'C', $dataStart, $dataEnd);

        $lastCol = Coordinate::stringFromColumnIndex($colCount);
        $sheet->freezePane('A'.($headerRow + 1));
        $sheet->setAutoFilter('A'.$headerRow.':'.$lastCol.$headerRow);

        return null;
    }

    public function title(): string
    {
        return 'Data';
    }
}
