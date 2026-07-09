<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DpLunasTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithCustomStartCell
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
            'Semester *',
            'Status Bayar *',
            'Tanggal Bayar',
            'Nominal (Rp)',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 7;

        $this->setTitle($sheet, 'TEMPLATE IMPORT DP LUNAS', $colCount);
        $this->setSubtitle($sheet, 'Data mahasiswa yang sudah membayar DP. Status Bayar: Lunas / Belum Lunas.', $colCount);

        // Write Contoh Format in Row 3
        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', 'Contoh Format: 4112714201240001 | WULAN SARI NURFIANI | S1 KEPERAWATAN | Year 2 Sem 2 | Lunas | 01/07/2025 | 5000000');
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
            'A' => 22, 'B' => 30, 'C' => 22, 'D' => 18, 'E' => 16, 'F' => 16, 'G' => 18,
        ]);

        $this->setFormatRupiah($sheet, 'G', $dataStart, $dataEnd);

        $sheet->freezePane('A' . ($headerRow + 1));
        $sheet->setAutoFilter('A' . $headerRow . ':G' . $headerRow);
    }

    public function title(): string
    {
        return 'Data';
    }
}
