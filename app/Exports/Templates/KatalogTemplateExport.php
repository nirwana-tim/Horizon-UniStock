<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KatalogTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithCustomStartCell
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

        // Write Contoh Format in Row 3
        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', 'Contoh Format: UNF-L-SCB-02-03 | UNF | L | Uniform Scrub Laki-Laki STIKES | 02 (STIKES) | S | Pcs');
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
