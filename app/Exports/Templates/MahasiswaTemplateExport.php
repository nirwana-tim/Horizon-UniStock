<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return [];
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

        return null;
    }

    public function title(): string
    {
        return 'Data';
    }
}
