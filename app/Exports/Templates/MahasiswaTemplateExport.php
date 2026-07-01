<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use App\Models\StudyProgram;
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
            ['4112714401250002', 'NABILA LUTHFIYYAH SETIAWAN', 'D3 KEPERAWATAN 1', 'Perempuan', 'M', '38', 'nabila@krw.horizon.ac.id', 'nabila@gmail.com', 'Freshman'],
            ['4112715401240002', 'BUNGA CITRA ANDINI', 'D3 KEBIDANAN 2', 'Perempuan', 'M', '36', 'bunga@krw.horizon.ac.id', 'bunga@gmail.com', 'Continuing'],
            ['', '', '', '', '', '', '', '', ''],
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
            'Tipe *',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 9;

        $this->setTitle($sheet, 'TEMPLATE IMPORT MAHASISWA', $colCount);
        $this->setSubtitle($sheet, 'Isi data sesuai format. Kolom dengan * wajib diisi. Baris pertama adalah contoh.', $colCount);

        $headerRow = $this->headerRow();
        $this->applyHeaderStyle($sheet, $headerRow, $colCount);

        $dataStart = $this->dataStartRow();
        $dataEnd = $dataStart + 2;
        $this->applyDataStyle($sheet, $dataStart, $dataEnd, $colCount);

        $sheet->getStyle('A' . $dataStart . ':I' . $dataStart)->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '999999']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5'],
            ],
        ]);

        $this->setColumnWidths($sheet, [
            'A' => 20, 'B' => 35, 'C' => 22, 'D' => 16,
            'E' => 16, 'F' => 16, 'G' => 30, 'H' => 30, 'I' => 16,
        ]);

        $sheet->freezePane('A' . ($headerRow + 1));
        $sheet->setAutoFilter('A' . $headerRow . ':I' . $headerRow);
    }

    public function title(): string
    {
        return 'Data';
    }
}
