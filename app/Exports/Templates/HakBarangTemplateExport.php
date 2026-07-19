<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use App\Models\Item;
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
        $items = Item::whereHas('entitlementItems')->orWhere('is_active', true)->orderBy('name')->pluck('name');

        return array_merge(
            ['Prodi Level *', 'Tipe *'],
            $items->toArray()
        );
    }

    public function styles(Worksheet $sheet): void
    {
        $items = Item::whereHas('entitlementItems')->orWhere('is_active', true)->orderBy('name')->pluck('name');
        $colCount = 2 + $items->count();

        $this->setTitle($sheet, 'TEMPLATE IMPORT HAK BARANG (ENTITLEMENT)', $colCount);
        $this->setSubtitle($sheet, 'Isi 1 jika prodi berhak mendapat barang, 0 jika tidak. Tipe: Freshman / Continuing.', $colCount);

        $headerRow = $this->headerRow();
        $this->applyHeaderStyle($sheet, $headerRow, $colCount);

        $widths = ['A' => 24, 'B' => 16];
        $colLetter = 'C';
        foreach ($items as $i => $name) {
            $widths[$colLetter] = max(12, min(20, strlen($name) + 4));
            $colLetter++;
        }
        $this->setColumnWidths($sheet, $widths);

        $sheet->freezePane('A' . ($headerRow + 1));
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
        $sheet->setAutoFilter('A' . $headerRow . ':' . $lastCol . $headerRow);
    }

    public function title(): string
    {
        return 'Data';
    }
}
