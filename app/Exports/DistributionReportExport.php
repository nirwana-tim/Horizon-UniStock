<?php

namespace App\Exports;

use App\Models\DistributionItem;
use App\Models\DistributionPeriod;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DistributionReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

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
                'distribution_schedules.period',
                'distribution_transactions.student_id',
                'distribution_transactions.status as transaction_status',
                'distribution_transactions.pickup_time'
            );

        if ($this->period) {
            $query->where('distribution_schedules.period', $this->period);
        }

        return $query->orderBy('distribution_schedules.period')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Periode',
            'Mahasiswa',
            'Item',
            'Ukuran',
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
            $item->period,
            $item->transaction->student->name ?? '-',
            $item->item->name ?? '-',
            $item->actual_size ?? $item->expected_size ?? '-',
            $item->quantity,
            $item->transaction_status,
            $item->pickup_time ? $item->pickup_time->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
