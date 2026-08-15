<?php

namespace App\Exports\Reports;

use App\Exports\BaseExport;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockCardReport extends BaseExport implements FromQuery, WithChunkReading, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    private int $row = 0;

    private int $runningBalance = 0;

    public function __construct(
        private string $itemCode,
        private ?string $startDate = null,
        private ?string $endDate = null
    ) {}

    public function query(): Builder
    {
        $movements = StockMovement::with('item', 'variant')
            ->whereIn('item_id', Item::where('code', $this->itemCode)->pluck('id'))
            ->orderBy('created_at')
            ->orderBy('id');

        if ($this->startDate) {
            $movements->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $movements->whereDate('created_at', '<=', $this->endDate);
        }

        return $movements;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Referensi',
            'Deskripsi',
            'Masuk (IN)',
            'Keluar (OUT)',
            'HPP Satuan (Rp)',
            'Total HPP (Rp)',
            'Saldo Akhir',
        ];
    }

    public function map($movement): array
    {
        $this->row++;

        if ($movement->type === 'IN') {
            $in = $movement->quantity;
            $out = 0;
            $this->runningBalance += $in;
        } else {
            $in = 0;
            $out = $movement->quantity;
            $this->runningBalance -= $out;
        }

        $hpp = $movement->item?->hpp ?? 0;

        return [
            $this->row,
            $movement->created_at->format('d/m/Y'),
            $movement->reference_type.' #'.$movement->reference_id,
            $movement->notes ?? ($movement->type === 'IN' ? 'Penerimaan Stok' : 'Pengeluaran Stok'),
            $in,
            $out,
            $hpp,
            $hpp * max($in, $out),
            max(0, $this->runningBalance),
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        $colCount = 9;
        $headerRow = $this->headerRow();
        $dataStart = $this->dataStartRow();
        $lastRow = $dataStart + $this->row - 1;

        $this->setTitle($sheet, 'KARTU STOK', $colCount);
        $this->setSubtitle($sheet, 'Barang: '.$this->itemCode
            .' | '.($this->startDate ?? 'Awal').' - '.($this->endDate ?? now()->format('d/m/Y')), $colCount);

        $this->applyHeaderStyle($sheet, $headerRow, $colCount);
        $this->applyDataStyle($sheet, $dataStart, $lastRow, $colCount);

        if ($lastRow >= $dataStart) {
            $totalRow = $lastRow + 1;
            $this->applyTotalStyle($sheet, $totalRow, $colCount);

            $sheet->setCellValue('A'.$totalRow, 'TOTAL');
            $sheet->setCellValue('E'.$totalRow, '=SUM(E'.$dataStart.':E'.$lastRow.')');
            $sheet->setCellValue('F'.$totalRow, '=SUM(F'.$dataStart.':F'.$lastRow.')');
            $sheet->setCellValue('H'.$totalRow, '=SUM(H'.$dataStart.':H'.$lastRow.')');

            $this->setFormatRupiah($sheet, 'G', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'H', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'E', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'F', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'I', $dataStart, $totalRow);
        }

        $this->setColumnWidths($sheet, [
            'A' => 5, 'B' => 14, 'C' => 18, 'D' => 35,
            'E' => 14, 'F' => 14, 'G' => 16, 'H' => 16, 'I' => 14,
        ]);

        $sheet->freezePane('A'.($headerRow + 1));

        return null;
    }
}
