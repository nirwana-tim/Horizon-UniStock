<?php

namespace App\Exports\Reports;

use App\Exports\BaseExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SizeRecapReport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private int $row = 0;

    public function __construct(
        private ?int $programLevelId = null,
        private ?int $studyProgramId = null
    ) {}

    public function collection(): \Illuminate\Support\Collection
    {
        $query = DB::table('student_size_items')
            ->join('student_size_profiles', 'student_size_items.size_profile_id', '=', 'student_size_profiles.id')
            ->join('students', 'student_size_profiles.student_id', '=', 'students.id')
            ->join('items', 'student_size_items.item_id', '=', 'items.id')
            ->leftJoin('item_variants', function ($join) {
                $join->on('student_size_items.item_id', '=', 'item_variants.item_id')
                     ->on('student_size_items.size', '=', 'item_variants.size');
            })
            ->select(
                'items.name as item_name',
                'items.code as item_code',
                DB::raw('COALESCE(item_variants.size_label, student_size_items.size) as size_label'),
                DB::raw('COUNT(*) as total_qty')
            )
            ->groupBy('student_size_items.item_id', 'student_size_items.size', 'items.name', 'items.code', 'item_variants.size_label')
            ->orderBy('items.name')
            ->orderBy('student_size_items.size');

        if ($this->programLevelId) {
            $query->where('students.program_level_id', $this->programLevelId);
        }
        if ($this->studyProgramId) {
            $query->where('students.study_program_id', $this->studyProgramId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Ukuran',
            'Total Kebutuhan (Pcs)',
        ];
    }

    public function map($recap): array
    {
        $this->row++;

        return [
            $this->row,
            $recap->item_code,
            $recap->item_name,
            $recap->size_label ?? '-',
            $recap->total_qty,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 5;
        $headerRow = $this->headerRow();
        $dataStart = $this->dataStartRow();
        $lastRow = $dataStart + $this->row - 1;

        $this->setTitle($sheet, 'LAPORAN REKAP KEBUTUHAN UKURAN MAHASISWA', $colCount);
        
        $filterText = 'Semua Angkatan & Prodi';
        if ($this->programLevelId || $this->studyProgramId) {
            $parts = [];
            if ($this->programLevelId) {
                $level = DB::table('program_levels')->where('id', $this->programLevelId)->first();
                if ($level) $parts[] = 'Angkatan: ' . $level->name;
            }
            if ($this->studyProgramId) {
                $prodi = DB::table('study_programs')->where('id', $this->studyProgramId)->first();
                if ($prodi) $parts[] = 'Prodi: ' . $prodi->name;
            }
            $filterText = implode(' | ', $parts);
        }
        $this->setSubtitle($sheet, $filterText, $colCount);

        $this->applyHeaderStyle($sheet, $headerRow, $colCount);
        $this->applyDataStyle($sheet, $dataStart, $lastRow, $colCount);

        // Align right for quantity column
        $sheet->getStyle('E' . $dataStart . ':E' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        $this->setColumnWidths($sheet, [
            'A' => 6, 'B' => 20, 'C' => 35, 'D' => 15, 'E' => 25,
        ]);

        $sheet->freezePane('A' . ($headerRow + 1));
    }
}
