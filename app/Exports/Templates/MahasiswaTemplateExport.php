<?php

namespace App\Exports\Templates;

use App\Exports\BaseExport;
use App\Models\StudyProgram;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaTemplateExport extends BaseExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithCustomStartCell
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
        $this->setSubtitle($sheet, 'Isi data sesuai format. Kolom dengan * wajib diisi.', $colCount);

        // Write Contoh Format in Row 3
        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A3', 'Contoh Format: 4112714401250002 | NABILA LUTHFIYYAH SETIAWAN | D3 KEPERAWATAN 1 | Perempuan | M | 38 | nabila@krw.horizon.ac.id | nabila@gmail.com | Freshman');
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
