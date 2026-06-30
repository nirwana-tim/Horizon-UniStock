<?php

namespace App\Exports;

use App\Services\GpmService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GpmReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    private int $row = 0;

    public function __construct(
        private ?string $period = null
    ) {}

    public function collection()
    {
        $service = new GpmService();

        return $service->calculateGpm($this->period);
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Item',
            'Kategori',
            'Qty Terjual',
            'HPP',
            'Harga Jual',
            'Total HPP',
            'Total Penjualan',
            'Laba / Rugi',
        ];
    }

    public function map($item): array
    {
        $this->row++;

        return [
            $this->row,
            $item['item_name'],
            $item['category_name'],
            $item['qty_sold'],
            $item['hpp'],
            $item['selling_price'],
            $item['total_hpp'],
            $item['total_selling_price'],
            $item['laba_rugi'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
