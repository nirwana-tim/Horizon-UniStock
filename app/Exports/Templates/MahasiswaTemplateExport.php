<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use App\Models\ItemSize;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return [
            ['4112714201240001', 'Budi Santoso', 'S1 Teknologi Informasi', 'L', 'L', '42', '4112714201240001@krw.horizon.ac.id', 'budi@gmail.com', 'Freshman'],
            ['4112714201240002', 'Siti Aminah', 'S1 Keperawatan', 'P', 'M', '38', '4112714201240002@krw.horizon.ac.id', 'siti@gmail.com', 'Freshman'],
            ['4112714201240003', 'Andi Wijaya', 'D3 Kebidanan', 'L', 'XL', '43', '4112714201240003@krw.horizon.ac.id', 'andi@gmail.com', 'Continuing'],
            ['4112714201240004', 'Rina Hartati', 'S1 Farmasi', 'P', 'S', '37', '4112714201240004@krw.horizon.ac.id', 'rina@gmail.com', 'Freshman'],
            ['4112714201240005', 'Dedi Kurniawan', 'S1 Teknologi Informasi', 'L', 'L', '41', '', 'dedi@gmail.com', 'Continuing'],
        ];
    }

    public function headings(): array
    {
        return [
            'NIM *',
            'Nama Lengkap *',
            'Prodi *',
            'Jenis Kelamin *',
            'Ukuran Baju *',
            'Ukuran Sepatu *',
            'Email Kampus',
            'Email Pribadi',
            'Tipe',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        $colCount = 9;
        $this->applyHeaderStyle($sheet, 1, $colCount);

        $this->setColumnWidths($sheet, [
            'A' => 18, 'B' => 30, 'C' => 25, 'D' => 16,
            'E' => 16, 'F' => 16, 'G' => 30, 'H' => 25, 'I' => 18,
        ]);

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:I1');

        $this->setDropdown($sheet, 'D2:D500', ['L', 'P']);
        $this->setDropdown($sheet, 'E2:E500', ItemSize::where('is_baju', true)->pluck('label')->toArray());
        $this->setDropdown($sheet, 'F2:F500', ItemSize::where('is_sepatu', true)->pluck('label')->toArray());
        $this->setDropdown($sheet, 'I2:I500', ['Freshman', 'Continuing']);

        return null;
    }

    public function title(): string
    {
        return 'Data';
    }
}
