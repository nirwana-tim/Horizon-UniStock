<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use App\Models\ItemCategory;
use App\Models\ItemDepartment;
use App\Models\ItemSize;
use App\Models\ItemType;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KatalogTemplateExport extends BaseExport implements FromArray, WithCustomStartCell, WithHeadings, WithStyles, WithTitle
{
    public function startCell(): string
    {
        return 'A4';
    }

    public function array(): array
    {
        $sizes = ItemSize::orderBy('code')->pluck('label');
        $sizeCols = array_fill(0, $sizes->count(), 0);

        return [
            array_merge(['UNF', 'L', 'Uniform Scrub STIKES', 'SCG', '-', 'Pcs', 190000, 150000], $sizeCols),
            array_merge(['UNF', 'P', 'Uniform Scrub STIKES', 'SCG', '-', 'Pcs', 190000, 150000], $sizeCols),
            array_merge(['SHO', 'L', 'Sepatu Clinical STIKES', 'CLG', '-', 'Pcs', 350000, 280000], $sizeCols),
            array_merge(['KTM', 'U', 'Kartu Mahasiswa', 'KTM', '-', 'Pcs', 0, 0], $sizeCols),
            array_merge(['KIT', 'U', 'Nursing Kit D3', 'NUR', '-', 'Set', 500000, 400000], $sizeCols),
        ];
    }

    public function headings(): array
    {
        $sizes = ItemSize::orderBy('code')->pluck('label');

        return array_merge(
            ['Kategori *', 'Gender *', 'Nama Item *', 'Type *', 'Departemen', 'Satuan *', 'Harga Jual (Rp)', 'HPP (Rp)'],
            $sizes->toArray()
        );
    }

    public function styles(Worksheet $sheet): ?array
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
        $sheet->freezePane('A'.($headerRow + 1));
        $sheet->setAutoFilter('A'.$headerRow.':'.$lastCol.$headerRow);

        $this->setDropdown($sheet, 'A5:A500', ItemCategory::pluck('label')->toArray());
        $this->setDropdown($sheet, 'B5:B500', ['L', 'P', 'U']);
        $this->setDropdown($sheet, 'D5:D500', ItemType::pluck('label')->toArray());
        $this->setDropdown($sheet, 'E5:E500', ItemDepartment::pluck('label')->toArray());

        return null;
    }

    public function title(): string
    {
        return 'Data';
    }
}
