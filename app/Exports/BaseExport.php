<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseExport
{
    protected string $primaryColor = '980416';
    protected string $stripeColor = 'F9F0F0';
    protected string $totalColor = 'E8D5D5';

    protected function applyHeaderStyle(Worksheet $sheet, int $row = 1, int|string|null $colCount = null): void
    {
        $colCount = $this->resolveColCount($colCount, $sheet);
        $range = 'A' . $row . ':' . $colCount . $row;

        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $this->primaryColor],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => $this->primaryColor],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension($row)->setRowHeight(25);
    }

    protected function applyDataStyle(Worksheet $sheet, int $startRow, int $endRow, int|string|null $colCount = null): void
    {
        $colLetter = $this->resolveColLetter($colCount, $sheet);

        for ($i = $startRow; $i <= $endRow; $i++) {
            $range = 'A' . $i . ':' . $colLetter . $i;
            $bgColor = ($i % 2 === 0) ? $this->stripeColor : 'FFFFFF';

            $sheet->getStyle($range)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->getRowDimension($i)->setRowHeight(20);
        }
    }

    protected function applyTotalStyle(Worksheet $sheet, int $row, int|string|null $colCount = null): void
    {
        $colCount = $this->resolveColCount($colCount, $sheet);
        $range = 'A' . $row . ':' . $colCount . $row;

        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $this->totalColor],
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => $this->primaryColor],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => $this->primaryColor],
                ],
            ],
        ]);
    }

    protected function setTitle(Worksheet $sheet, string $title, int $colCount = 10): void
    {
        $colLetter = Coordinate::stringFromColumnIndex($colCount);
        $sheet->mergeCells('A1:' . $colLetter . '1');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => $this->primaryColor],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
    }

    protected function setSubtitle(Worksheet $sheet, string $subtitle, int $colCount = 10): void
    {
        $colLetter = Coordinate::stringFromColumnIndex($colCount);
        $sheet->mergeCells('A2:' . $colLetter . '2');
        $sheet->setCellValue('A2', $subtitle);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '666666'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);
    }

    protected function setColumnWidths(Worksheet $sheet, array $widths): void
    {
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
    }

    protected function setFormatRupiah(Worksheet $sheet, string $column, int $startRow, int $endRow): void
    {
        $sheet->getStyle($column . $startRow . ':' . $column . $endRow)
            ->getNumberFormat()->setFormatCode('#,##0');
    }

    protected function setFormatNumber(Worksheet $sheet, string $column, int $startRow, int $endRow): void
    {
        $sheet->getStyle($column . $startRow . ':' . $column . $endRow)
            ->getNumberFormat()->setFormatCode('#,##0');
    }

    protected function setFormatDate(Worksheet $sheet, string $column, int $startRow, int $endRow): void
    {
        $sheet->getStyle($column . $startRow . ':' . $column . $endRow)
            ->getNumberFormat()->setFormatCode('dd/mm/yyyy');
    }

    protected function setFormatPercentage(Worksheet $sheet, string $column, int $startRow, int $endRow): void
    {
        $sheet->getStyle($column . $startRow . ':' . $column . $endRow)
            ->getNumberFormat()->setFormatCode('0.00%');
    }

    protected function headerRow(): int
    {
        return 4;
    }

    protected function dataStartRow(): int
    {
        return 5;
    }

    private function resolveColCount(int|string|null $colCount, Worksheet $sheet): string
    {
        if ($colCount === null) {
            return $sheet->getHighestColumn();
        }

        if (is_int($colCount)) {
            return Coordinate::stringFromColumnIndex($colCount);
        }

        return $colCount;
    }

    private function resolveColLetter(int|string|null $colCount, Worksheet $sheet): string
    {
        if ($colCount === null) {
            return $sheet->getHighestColumn();
        }

        if (is_int($colCount)) {
            return Coordinate::stringFromColumnIndex($colCount);
        }

        return $colCount;
    }
}
