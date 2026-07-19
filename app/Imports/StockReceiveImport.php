<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemVariant;
use App\Models\Vendor;
use App\Services\StockService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Validation\ValidationException as IlluminateValidationException;

class StockReceiveImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    private int $importedCount = 0;
    private int $totalRows = 0;

    public function collection(Collection $rows): void
    {
        $records = $this->recordsFromRows($rows);
        $this->totalRows = count($records);

        $failures = $this->validateRecords($records);
        if ($failures !== []) {
            throw new ValidationException(
                IlluminateValidationException::withMessages([]),
                $failures
            );
        }

        $groups = $this->groupRecords($records);
        $stockService = app(StockService::class);

        foreach ($groups as $key => $group) {
            $vendor = Vendor::firstOrCreate(
                ['name' => $group['vendor_name']],
                ['code' => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $group['vendor_name']), 0, 3))]
            );

            $data = [
                'vendor_id' => $vendor->id,
                'receive_date' => $group['receive_date'],
                'reference_number' => $group['reference_number'],
                'notes' => $group['notes'],
                'items' => [],
            ];

            foreach ($group['items'] as $itemData) {
                $item = Item::where('code', $itemData['kode_barang'])
                    ->orWhere('base_code', $itemData['kode_barang'])
                    ->first();

                if (!$item) continue;

                $variant = $itemData['sku']
                    ? $item->variants()->where('sku', $itemData['sku'])->first()
                    : $item->variants()->first();

                if (!$variant) continue;

                $data['items'][] = [
                    'item_id' => $item->id,
                    'variant_id' => $variant->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'hpp' => $itemData['hpp'],
                ];
            }

            if (!empty($data['items'])) {
                $stockService->receiveStock($data);
                $this->importedCount += count($group['items']);
            }
        }
    }

    public function sheets(): array
    {
        return ['Data' => $this];
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function countRows(Collection $rows): int
    {
        return count($this->recordsFromRows($rows));
    }

    private function recordsFromRows(Collection $rows): array
    {
        $records = [];

        foreach ($rows as $index => $row) {
            $values = $row instanceof Collection ? $row->toArray() : (array) $row;

            if ($this->shouldSkipRow($values)) continue;

            $records[] = [
                'row' => $index + 1,
                'kode_barang' => $this->clean($values['kode_barang'] ?? null),
                'sku' => $this->clean($values['sku'] ?? $values['varian_sku'] ?? null),
                'quantity' => $this->parseNumeric($values['quantity'] ?? $values['qty'] ?? null),
                'unit_price' => $this->parseDecimal($values['unit_price'] ?? $values['harga_satuan'] ?? null),
                'hpp' => $this->parseDecimal($values['hpp'] ?? null),
                'vendor' => $this->clean($values['vendor'] ?? $values['vendor_name'] ?? $values['nama_vendor'] ?? null),
                'tanggal' => $this->clean($values['tanggal'] ?? $values['receive_date'] ?? $values['tgl_terima'] ?? date('Y-m-d')),
                'nomor_ref' => $this->clean($values['nomor_ref'] ?? $values['reference_number'] ?? null),
                'notes' => $this->clean($values['notes'] ?? $values['keterangan'] ?? null),
            ];
        }

        return $records;
    }

    private function validateRecords(array &$records): array
    {
        $failures = [];

        foreach ($records as &$record) {
            $rules = [
                'kode_barang' => ['required', 'string', 'max:100'],
                'quantity' => ['required', 'integer', 'min:1'],
                'vendor' => ['required', 'string', 'max:255'],
                'tanggal' => ['required', 'string', 'date_format:Y-m-d'],
            ];

            $validator = Validator::make($record, $rules);

            foreach ($validator->errors()->messages() as $attribute => $messages) {
                $failures[] = new Failure($record['row'], $attribute, $messages, $record);
            }
        }

        return $failures;
    }

    private function groupRecords(array $records): array
    {
        $groups = [];

        foreach ($records as $record) {
            $key = $record['nomor_ref'] ?? md5($record['vendor'] . '|' . $record['tanggal'] . '|' . ($record['notes'] ?? ''));

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'vendor_name' => $record['vendor'],
                    'receive_date' => $record['tanggal'],
                    'reference_number' => $record['nomor_ref'],
                    'notes' => $record['notes'],
                    'items' => [],
                ];
            }

            $groups[$key]['items'][] = $record;
        }

        return $groups;
    }

    private function shouldSkipRow(array $values): bool
    {
        $firstVal = $this->clean(reset($values));
        if ($firstVal === null) {
            return collect($values)->filter(fn ($v) => $this->clean($v) !== null)->isEmpty();
        }
        return false;
    }

    private function clean(mixed $value): ?string
    {
        if ($value === null) return null;
        if (is_numeric($value) && (str_contains(strtolower((string)$value), 'e+') || is_float($value))) {
            $value = number_format((float)$value, 0, '', '');
        }
        $value = ltrim(trim((string) $value), "'");
        return $value === '' ? null : $value;
    }

    private function parseNumeric(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === '-') return null;
        if (is_numeric($value)) return (int) $value;
        $cleaned = preg_replace('/[^0-9]/', '', (string) $value);
        return $cleaned !== '' ? (int) $cleaned : null;
    }

    private function parseDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '' || $value === '-') return null;
        $cleaned = str_replace(',', '.', (string) $value);
        $cleaned = preg_replace('/[^0-9.]/', '', $cleaned);
        return $cleaned !== '' ? (float) $cleaned : null;
    }
}
