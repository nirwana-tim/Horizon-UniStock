<?php

namespace App\Exports;

use App\Models\DistributionItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DistributionReportExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private int $row = 0;

    public function __construct(
        private ?string $period = null
    ) {}

    public function collection()
    {
        $query = DistributionItem::with('item', 'transaction.student', 'transaction.schedule')
            ->join('distribution_transactions', 'distribution_items.transaction_id', '=', 'distribution_transactions.id')
            ->join('distribution_schedules', 'distribution_transactions.schedule_id', '=', 'distribution_schedules.id')
            ->select(
                'distribution_items.*',
                'distribution_transactions.student_id',
                'distribution_transactions.status as transaction_status',
                'distribution_transactions.pickup_time'
            );

        if ($this->period) {
            $query->whereHas('transaction.schedule', function ($q) {
                $q->where('period', $this->period);
            });
        }

        return $query->orderBy('distribution_transactions.created_at')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Mahasiswa',
            'Prodi',
            'Item',
            'Ukuran Diharapkan',
            'Ukuran Diberikan',
            'Jumlah',
            'Status',
            'Waktu Ambil',
        ];
    }

    public function map($item): array
    {
        $this->row++;

        return [
            $this->row,
            $item->transaction->student->name ?? '-',
            $item->transaction->student->studyProgram->name ?? '-',
            $item->item->name ?? '-',
            $item->expected_size ?? '-',
            $item->actual_size ?? '-',
            $item->quantity,
            $item->transaction_status,
            $item->pickup_time ? $item->pickup_time->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 9;
        $headerRow = $this->headerRow();
        $dataStart = $this->dataStartRow();
        $lastRow = $dataStart + $this->row - 1;

        $this->setTitle($sheet, 'LAPORAN REKAP PEMBAGIAN', $colCount);
        $filterText = $this->period ? 'Periode: ' . $this->period : 'Semua Periode';
        $this->setSubtitle($sheet, $filterText, $colCount);

        $this->applyHeaderStyle($sheet, $headerRow, $colCount);
        $this->applyDataStyle($sheet, $dataStart, $lastRow, $colCount);

        $this->setColumnWidths($sheet, [
            'A' => 5, 'B' => 30, 'C' => 22, 'D' => 35,
            'E' => 18, 'F' => 18, 'G' => 10, 'H' => 14, 'I' => 18,
        ]);

        $sheet->freezePane('A' . ($headerRow + 1));
    }
}
