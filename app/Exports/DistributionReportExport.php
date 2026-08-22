<?php

namespace App\Exports;

use App\Models\DistributionItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DistributionReportExport extends BaseExport implements FromQuery, WithChunkReading, WithCustomStartCell, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    private int $row = 0;

    public function __construct(
        private ?string $period = null
    ) {}

    public function startCell(): string
    {
        return 'A4';
    }

    public function query(): Builder
    {
        $query = DistributionItem::with('item', 'variant', 'transaction.student.studyProgram', 'transaction.schedule')
            ->join('distribution_transactions', 'distribution_items.transaction_id', '=', 'distribution_transactions.id')
            ->join('distribution_schedules', 'distribution_transactions.schedule_id', '=', 'distribution_schedules.id')
            ->select(
                'distribution_items.*',
                'distribution_transactions.status as transaction_status',
                'distribution_transactions.pickup_time'
            );

        if ($this->period) {
            $query->where('distribution_schedules.period', $this->period);
        }

        return $query->orderBy('distribution_transactions.created_at')->orderBy('distribution_items.id');
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function headings(): array
    {
        return [
            'No', 'NIM', 'Nama Mahasiswa', 'Prodi', 'Item',
            'Ukuran Diharapkan', 'Ukuran Diberikan', 'Jumlah', 'Status', 'Waktu Ambil',
        ];
    }

    public function map($item): array
    {
        $this->row++;

        return [
            $this->row,
            $item->transaction?->student?->nim ?? '-',
            $item->transaction?->student?->name ?? '-',
            $item->transaction?->student?->studyProgram?->name ?? '-',
            $item->item->name ?? '-',
            $item->variant?->size_label ?? $item->expected_size ?? '-',
            $item->variant?->size_label ?? $item->actual_size ?? '-',
            $item->quantity,
            $item->transaction_status,
            $item->pickup_time ? Carbon::parse($item->pickup_time)->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        $colCount = 10;
        $headerRow = $this->headerRow();
        $dataStart = $this->dataStartRow();
        $lastRow = $dataStart + $this->row - 1;

        $this->setTitle($sheet, 'LAPORAN REKAP PEMBAGIAN', $colCount);
        $filterText = $this->period ? 'Periode: '.$this->period : 'Semua Periode';
        $this->setSubtitle($sheet, $filterText, $colCount);

        $this->applyHeaderStyle($sheet, $headerRow, $colCount);
        $this->applyDataStyle($sheet, $dataStart, $lastRow, $colCount);

        $this->setColumnWidths($sheet, [
            'A' => 5, 'B' => 15, 'C' => 30, 'D' => 22, 'E' => 35,
            'F' => 18, 'G' => 18, 'H' => 10, 'I' => 14, 'J' => 18,
        ]);

        $sheet->freezePane('A'.($headerRow + 1));

        return null;
    }
}
