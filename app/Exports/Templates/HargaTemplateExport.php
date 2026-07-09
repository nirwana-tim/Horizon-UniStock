<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HargaTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithCustomStartCell
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
            'Nama Barang',
            'Tahun Akademik *',
            'Harga Jual (Rp) *',
            'HPP (Rp) *',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 5;

        $this->setTitle($sheet, 'TEMPLATE IMPORT HARGA BARANG', $colCount);
        $this->setSubtitle($sheet, 'Tahun Akademik: 22/23 / 23/24 / 24/25 / 25/26. Nama Barang akan terisi otomatis jika kode valid.', $colCount);

        // Write Contoh Format in Row 3
        $sheet->mergeCells('A3:E3');
        $sheet->setCellValue('A3', 'Contoh Format: UNF-L-SCB-02-03 | (Nama Kosong) | 24/25 | 190000 | 150000');
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

        $dataStart = $this->dataStartRow();
        $dataEnd = 1000;

        $this->setColumnWidths($sheet, [
            'A' => 22, 'B' => 40, 'C' => 18, 'D' => 20, 'E' => 20,
        ]);

        $this->setFormatRupiah($sheet, 'D', $dataStart, $dataEnd);
        $this->setFormatRupiah($sheet, 'E', $dataStart, $dataEnd);

        $sheet->freezePane('A' . ($headerRow + 1));
        $sheet->setAutoFilter('A' . $headerRow . ':E' . $headerRow);
    }

    public function title(): string
    {
        return 'Data';
    }
}
