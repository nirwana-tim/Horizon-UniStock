<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use App\Models\ItemSize;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
        $sizes = ItemSize::orderBy('code')->pluck('label');

        return array_merge(
            ['Kategori *', 'Gender *', 'Nama Item *', 'Type *', 'Departemen', 'Satuan *', 'Harga Jual (Rp)', 'HPP (Rp)'],
            $sizes->toArray()
        );
    }

    public function styles(Worksheet $sheet): void
    {
        $sizes = ItemSize::orderBy('code')->pluck('label');
        $fixedCols = 8;
        $colCount = $fixedCols + $sizes->count();

        $this->setTitle($sheet, 'TEMPLATE IMPORT KATALOG BARANG', $colCount);
        $this->setSubtitle($sheet, 'Kategori: UNF / SHO / KTM / KIT / MRC. Gender: L / P / U. Type: SCB / CLG / COM / LAB / CLN / ALM. Isi qty per ukuran.', $colCount);

        $headerRow = $this->headerRow();
        $this->applyHeaderStyle($sheet, $headerRow, $colCount);

        $widths = ['A' => 12, 'B' => 10, 'C' => 40, 'D' => 10, 'E' => 20, 'F' => 10, 'G' => 18, 'H' => 18];
        $colLetter = 'I';
        foreach ($sizes as $label) {
            $widths[$colLetter] = max(8, min(14, strlen($label) + 4));
            $colLetter++;
        }
        $this->setColumnWidths($sheet, $widths);

        $this->setFormatRupiah($sheet, 'G', $this->dataStartRow(), 1000);
        $this->setFormatRupiah($sheet, 'H', $this->dataStartRow(), 1000);

        $lastCol = Coordinate::stringFromColumnIndex($colCount);
        $sheet->freezePane('A' . ($headerRow + 1));
        $sheet->setAutoFilter('A' . $headerRow . ':' . $lastCol . $headerRow);
    }

    public function title(): string
    {
        return 'Data';
    }
}
