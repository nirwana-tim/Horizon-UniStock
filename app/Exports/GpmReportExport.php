<?php

namespace App\Exports;

use App\Services\GpmService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GpmReportExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private int $row = 0;

    public function __construct(
        private ?string $period = null,
        private ?string $category = null
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
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Harga Jual (Rp)',
            'HPP (Rp)',
            'Qty Terjual',
            'Revenue (Rp)',
            'Cost (Rp)',
            'Laba Kotor (Rp)',
            'Margin (%)',
        ];
    }

    public function map($item): array
    {
        $this->row++;
        $revenue = ($item['selling_price'] ?? 0) * ($item['qty_sold'] ?? 0);
        $cost = ($item['hpp'] ?? 0) * ($item['qty_sold'] ?? 0);
        $profit = $revenue - $cost;
        $margin = $revenue > 0 ? $profit / $revenue : 0;

        return [
            $this->row,
            $item['item_code'] ?? '',
            $item['item_name'],
            $item['category_name'] ?? '-',
            $item['selling_price'] ?? 0,
            $item['hpp'] ?? 0,
            $item['qty_sold'] ?? 0,
            $revenue,
            $cost,
            $profit,
            $margin,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $colCount = 11;
        $headerRow = $this->headerRow();
        $dataStart = $this->dataStartRow();
        $lastRow = $dataStart + $this->row - 1;

        $this->setTitle($sheet, 'LAPORAN GPM / LABA KOTOR', $colCount);
        $filterText = $this->period ? 'Periode: ' . $this->period : 'Semua Periode';
        $this->setSubtitle($sheet, $filterText, $colCount);

        $this->applyHeaderStyle($sheet, $headerRow, $colCount);
        $this->applyDataStyle($sheet, $dataStart, $lastRow, $colCount);

        for ($i = $dataStart; $i <= $lastRow; $i++) {
            $margin = $sheet->getCell('K' . $i)->getValue();

            if (is_numeric($margin)) {
                if ($margin < 0.10) {
                    $sheet->getStyle('K' . $i)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFE0E0');
                } elseif ($margin < 0.20) {
                    $sheet->getStyle('K' . $i)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFF8E0');
                } else {
                    $sheet->getStyle('K' . $i)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('E0FFE0');
                }
            }
        }

        if ($lastRow >= $dataStart) {
            $totalRow = $lastRow + 1;
            $this->applyTotalStyle($sheet, $totalRow, $colCount);

            $sheet->setCellValue('A' . $totalRow, 'TOTAL');
            $sheet->setCellValue('G' . $totalRow, '=SUM(G' . $dataStart . ':G' . $lastRow . ')');
            $sheet->setCellValue('H' . $totalRow, '=SUM(H' . $dataStart . ':H' . $lastRow . ')');
            $sheet->setCellValue('I' . $totalRow, '=SUM(I' . $dataStart . ':I' . $lastRow . ')');
            $sheet->setCellValue('J' . $totalRow, '=SUM(J' . $dataStart . ':J' . $lastRow . ')');

            $this->setFormatRupiah($sheet, 'E', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'F', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'H', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'I', $dataStart, $totalRow);
            $this->setFormatRupiah($sheet, 'J', $dataStart, $totalRow);
            $this->setFormatNumber($sheet, 'G', $dataStart, $totalRow);
            $this->setFormatPercentage($sheet, 'K', $dataStart, $totalRow);
        }

        $this->setColumnWidths($sheet, [
            'A' => 5, 'B' => 20, 'C' => 30, 'D' => 14,
            'E' => 16, 'F' => 16, 'G' => 14, 'H' => 18,
            'I' => 18, 'J' => 18, 'K' => 14,
        ]);

        $sheet->freezePane('A' . ($headerRow + 1));
    }
}
