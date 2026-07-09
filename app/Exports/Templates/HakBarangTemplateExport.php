<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use App\Models\ItemCategory;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HakBarangTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithCustomStartCell
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
            'Prodi Level *',
            'Tipe *',
            'Almamater',
            'Seragam Kuliah',
            'Seragam Praktek',
            'Scrub Suit',
            'Jas Lab',
            'Seragam Komunitas',
            'Sepatu Kuliah',
            'Sepatu Praktek',
            'Lanyard & Holder',
            'Name Tag',
            'Nursing Kit',
            'Midwifery Kit',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 14;

        $this->setTitle($sheet, 'TEMPLATE IMPORT HAK BARANG (ENTITLEMENT)', $colCount);
        $this->setSubtitle($sheet, 'Isi 1 jika prodi berhak mendapat barang, 0 jika tidak. Tipe: Freshman / Continuing.', $colCount);

        // Write Contoh Format in Row 3
        $sheet->mergeCells('A3:N3');
        $sheet->setCellValue('A3', 'Contoh Format: D3 KEPERAWATAN 1 | Freshman | 1 | 1 | 1 | 0 | 0 | 1 | 1 | 0 | 1 | 1 | 0 | 0');
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
            'A' => 24, 'B' => 16,
            'C' => 14, 'D' => 16, 'E' => 16, 'F' => 12, 'G' => 10,
            'H' => 18, 'I' => 14, 'J' => 14, 'K' => 18, 'L' => 12,
            'M' => 14, 'N' => 14,
        ]);

        $sheet->freezePane('A' . ($headerRow + 1));
        $sheet->setAutoFilter('A' . $headerRow . ':N' . $headerRow);
    }

    public function title(): string
    {
        return 'Data';
    }
}
