<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HakBarangTemplateExport extends BaseExport implements FromArray, WithCustomStartCell, WithHeadings, WithStyles, WithTitle
{
    public function startCell(): string
    {
        return 'A4';
    }

    public function array(): array
    {
        $items = Item::whereHas('entitlementItems')->orWhere('is_active', true)->orderBy('name')->pluck('name');
        $itemCols = array_fill(0, $items->count(), 0);

        return [
            array_merge(['Y1S1FTIK', 'Freshman'], $itemCols),
            array_merge(['Y1S1FTIK', 'Freshman'], array_map(fn () => 1, $itemCols)),
            array_merge(['Y2S1FTIK', 'Continuing'], $itemCols),
            array_merge(['Y1S1FKES', 'Freshman'], array_map(fn () => 1, $itemCols)),
            array_merge(['Y3S1FTIK', 'Continuing'], $itemCols),
        ];
    }

    public function headings(): array
    {
        $items = Item::whereHas('entitlementItems')->orWhere('is_active', true)->orderBy('name')->pluck('name');

        return array_merge(
            ['Prodi Level *', 'Tipe *'],
            $items->toArray()
        );
    }

    public function styles(Worksheet $sheet): ?array
    {
        $items = Item::whereHas('entitlementItems')->orWhere('is_active', true)->orderBy('name')->pluck('name');
        $colCount = 2 + $items->count();

        $this->setTitle($sheet, 'TEMPLATE IMPORT HAK BARANG (ENTITLEMENT)', $colCount);
        $this->setSubtitle($sheet, 'Isi jumlah barang yang berhak didapat per item (0 jika tidak berhak). Tipe: Freshman / Continuing.', $colCount);

        $headerRow = $this->headerRow();
        $this->applyHeaderStyle($sheet, $headerRow, $colCount);

        $widths = ['A' => 24, 'B' => 16];
        $colLetter = 'C';
        foreach ($items as $i => $name) {
            $widths[$colLetter] = max(12, min(20, strlen($name) + 4));
            $colLetter++;
        }
        $this->setColumnWidths($sheet, $widths);

        $sheet->freezePane('A'.($headerRow + 1));
        $lastCol = Coordinate::stringFromColumnIndex($colCount);
        $sheet->setAutoFilter('A'.$headerRow.':'.$lastCol.$headerRow);

        $this->setDropdown($sheet, 'B5:B500', ['Freshman', 'Continuing']);

        return null;
    }

    public function title(): string
    {
        return 'Data';
    }
}
