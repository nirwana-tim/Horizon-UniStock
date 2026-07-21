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
        return [
            ['123459', 'Budi Santoso', 'S1 Sistem Informasi', 'L', 'L', '34', 'budi@krw.horizon.ac.id', '', 'Year 1 Sem 1'],
            ['123460', 'Siti Aminah', 'S1 Keperawatan', 'P', 'M', '29', 'siti@krw.horizon.ac.id', '', 'Year 1 Sem 2'],
            ['123461', 'Rizky Pratama', 'S1 Informatika', 'L', 'XL', '36', 'rizky@krw.horizon.ac.id', '', 'Year 2 Sem 1'],
            ['123462', 'Dewi Lestari', 'S1 Akuntansi', 'P', 'S', '28', 'dewi@krw.horizon.ac.id', '', 'Year 3 Sem 1'],
            ['123463', 'Andi Wijaya', 'S1 Manajemen', 'L', 'M', '32', 'andi@krw.horizon.ac.id', '', 'Year 1 Sem 1'],
            ['123464', 'Rina Novita', 'S1 Sistem Informasi', 'P', 'L', '30', 'rina@krw.horizon.ac.id', '', 'Year 2 Sem 2'],
            ['123465', 'Fajar Hidayat', 'S1 Informatika', 'L', 'XXL', '40', 'fajar@krw.horizon.ac.id', '', 'Year 4 Sem 1'],
            ['123466', 'Ayu Wandira', 'S1 Keperawatan', 'P', 'M', '29', 'ayu@krw.horizon.ac.id', '', 'Year 1 Sem 1'],
            ['123467', 'Hendra Gunawan', 'S1 Akuntansi', 'L', 'L', '34', 'hendra@krw.horizon.ac.id', '', 'Year 3 Sem 2'],
            ['123468', 'Maya Sari', 'S1 Manajemen', 'P', 'S', '27', 'maya@krw.horizon.ac.id', '', 'Year 2 Sem 1'],
            ['123469', 'Teguh Prakoso', 'S1 Informatika', 'L', 'XL', '38', 'teguh@krw.horizon.ac.id', '', 'Year 1 Sem 1'],
            ['123470', 'Indah Permata', 'S1 Sistem Informasi', 'P', 'L', '31', 'indah@krw.horizon.ac.id', '', 'Year 4 Sem 2'],
            ['123471', 'Dimas Anggara', 'S1 Manajemen', 'L', 'M', '33', 'dimas@krw.horizon.ac.id', '', 'Year 2 Sem 2'],
            ['123472', 'Putri Kinanti', 'S1 Keperawatan', 'P', 'S', '28', 'putri@krw.horizon.ac.id', '', 'Year 3 Sem 1'],
            ['123473', 'Gilang Ramadhan', 'S1 Informatika', 'L', 'XXL', '42', 'gilang@krw.horizon.ac.id', '', 'Year 1 Sem 1'],
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

    public function styles(Worksheet $sheet): void
    {
        $colCount = 9;
        $this->applyHeaderStyle($sheet, 1, $colCount);

        $this->setColumnWidths($sheet, [
            'A' => 18, 'B' => 30, 'C' => 25, 'D' => 16,
            'E' => 16, 'F' => 16, 'G' => 30, 'H' => 25, 'I' => 18,
        ]);

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:I1');
    }

    public function title(): string
    {
        return 'Data';
    }
}
