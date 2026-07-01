<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use App\Models\ItemCategory;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HakBarangTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return [
            ['D3 KEPERAWATAN 1', 'Freshman', 1, 1, 1, 0, 0, 1, 1, 0, 1, 1, 0, 0],
            ['D3 KEBIDANAN 2', 'Continuing', 0, 1, 1, 0, 0, 0, 1, 0, 1, 1, 0, 0],
        ];
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

        $headerRow = $this->headerRow();
        $this->applyHeaderStyle($sheet, $headerRow, $colCount);

        $dataStart = $this->dataStartRow();
        $dataEnd = $dataStart + 1;
        $this->applyDataStyle($sheet, $dataStart, $dataEnd, $colCount);

        $sheet->getStyle('A' . $dataStart . ':N' . $dataStart)->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '999999']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5'],
            ],
        ]);

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
