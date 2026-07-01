<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DpLunasTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return [
            ['4112714201240001', 'WULAN SARI NURFIANI', 'S1 KEPERAWATAN', 'Year 2 Sem 2', 'Lunas', '01/07/2025', '5000000'],
            ['4112714201240002', 'SITI DELA AYU PITA', 'S1 KEPERAWATAN', 'Year 2 Sem 2', 'Belum Lunas', '', ''],
        ];
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

        $headerRow = $this->headerRow();
        $this->applyHeaderStyle($sheet, $headerRow, $colCount);

        $dataStart = $this->dataStartRow();
        $dataEnd = $dataStart + 1;
        $this->applyDataStyle($sheet, $dataStart, $dataEnd, $colCount);

        $sheet->getStyle('A' . $dataStart . ':G' . $dataStart)->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '999999']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5'],
            ],
        ]);

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
