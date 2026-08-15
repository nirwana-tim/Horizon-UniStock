<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DpLunasTemplateExport extends BaseExport implements FromArray, WithCustomStartCell, WithHeadings, WithStyles, WithTitle
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
            'NIM *',
            'Nama Mahasiswa *',
            'Prodi *',
            'Level *',
            'Status Bayar *',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        $colCount = 5;

        $this->setTitle($sheet, 'TEMPLATE IMPORT DP LUNAS', $colCount);
        $this->setSubtitle($sheet, 'Data mahasiswa yang sudah membayar DP. Status Bayar: Lunas / Belum Lunas.', $colCount);

        $sheet->mergeCells('A3:E3');
        $sheet->setCellValue('A3', 'Contoh Format: 4112714201240001 | WULAN SARI NURFIANI | S1 KEPERAWATAN | Y1S1 | Lunas');
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
            'A' => 22, 'B' => 30, 'C' => 22, 'D' => 18, 'E' => 16,
        ]);

        $sheet->freezePane('A'.($headerRow + 1));
        $sheet->setAutoFilter('A'.$headerRow.':E'.$headerRow);

        return null;
    }

    public function title(): string
    {
        return 'Data';
    }
}
