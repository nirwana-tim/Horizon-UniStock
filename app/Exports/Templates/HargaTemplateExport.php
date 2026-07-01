<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HargaTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return [
            ['UNF-L-SCB-02-03', '', '24/25', '190000', '150000'],
            ['SHO-P-CLC-02-37', '', '24/25', '280000', '200000'],
            ['', '', '', '', ''],
        ];
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

        $headerRow = $this->headerRow();
        $this->applyHeaderStyle($sheet, $headerRow, $colCount);

        $dataStart = $this->dataStartRow();
        $dataEnd = $dataStart + 2;
        $this->applyDataStyle($sheet, $dataStart, $dataEnd, $colCount);

        $sheet->getStyle('A' . $dataStart . ':E' . $dataStart)->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '999999']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5'],
            ],
        ]);

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
