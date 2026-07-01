<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KatalogTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return [
            ['UNF-L-SCB-02-03', 'UNF', 'L', 'Uniform Scrub Laki-Laki STIKES', '02 (STIKES)', 'S', 'Pcs'],
            ['SHO-P-CLC-02-37', 'SHO', 'P', 'Shoes Clinical Perempuan STIKES', '02 (STIKES)', '37', 'Pasang'],
            ['KTM-U-YDH-01-01', 'KTM', 'U', 'KTM Lanyard & Holder Unisex', '01 (UMUM)', 'All Size', 'Pcs'],
            ['', '', '', '', '', '', ''],
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Kategori *',
            'Gender *',
            'Nama Item *',
            'Departemen *',
            'Ukuran *',
            'Satuan *',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 7;

        $this->setTitle($sheet, 'TEMPLATE IMPORT KATALOG BARANG', $colCount);
        $this->setSubtitle($sheet, 'Kode Barang dikosongkan untuk generate otomatis. Kategori: UNF / SHO / KTM / KIT / MRC. Gender: L / P / U.', $colCount);

        $headerRow = $this->headerRow();
        $this->applyHeaderStyle($sheet, $headerRow, $colCount);

        $dataStart = $this->dataStartRow();
        $dataEnd = $dataStart + 3;
        $this->applyDataStyle($sheet, $dataStart, $dataEnd, $colCount);

        $sheet->getStyle('A' . $dataStart . ':G' . $dataStart)->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '999999']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5'],
            ],
        ]);

        $this->setColumnWidths($sheet, [
            'A' => 22, 'B' => 14, 'C' => 10, 'D' => 40, 'E' => 20, 'F' => 14, 'G' => 12,
        ]);

        $sheet->freezePane('A' . ($headerRow + 1));
        $sheet->setAutoFilter('A' . $headerRow . ':G' . $headerRow);
    }

    public function title(): string
    {
        return 'Data';
    }
}
