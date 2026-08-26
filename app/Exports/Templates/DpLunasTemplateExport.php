<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use App\Models\StudentLevel;
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
        return [
            ['4112714201240001', 'Wulan Sari', 'S1 Keperawatan', 'Y1S1FTIK', 'Lunas'],
            ['4112714201240002', 'Budi Santoso', 'S1 Teknologi Informasi', 'Y1S1FTIK', 'Lunas'],
            ['4112714201240003', 'Andi Wijaya', 'D3 Kebidanan', 'Y2S1FTIK', 'Belum Lunas'],
            ['4112714201240004', 'Rina Hartati', 'S1 Farmasi', 'Y1S1FKES', 'Lunas'],
            ['4112714201240005', 'Dedi Kurniawan', 'S1 Teknologi Informasi', 'Y3S1FTIK', 'Lunas'],
        ];
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
        $sheet->setCellValue('A3', 'Contoh Format: 4112714201240001 | WULAN SARI NURFIANI | S1 KEPERAWATAN | Y1S1FTIK | Lunas');
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

        $this->setDropdown($sheet, 'D5:D500', StudentLevel::pluck('kode')->toArray());
        $this->setDropdown($sheet, 'E5:E500', ['Lunas', 'Belum Lunas']);

        return null;
    }

    public function title(): string
    {
        return 'Data';
    }
}
