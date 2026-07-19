<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemDepartment;
use App\Models\ItemPrice;
use App\Models\ItemSize;
use App\Models\ItemVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Validation\ValidationException as IlluminateValidationException;

class ItemImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    private int $importedCount = 0;

    private int $totalRows = 0;

    private array $sizeCodes = [];

    public function __construct()
    {
        $this->sizeCodes = ItemSize::pluck('code', 'label')->toArray();
    }

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

        foreach ($records as $record) {
            $category = $this->resolveCategory($record['kategori']);
            $department = $this->resolveDepartment($record['departemen']);

            $item = Item::updateOrCreate(
                ['code' => $record['kode']],
                [
                    'name' => $record['nama'],
                    'base_code' => $this->generateBaseCode($record),
                    'gender' => $record['gender'],
                    'category_id' => $category?->id,
                    'department_id' => $department?->id,
                    'unit' => $record['satuan'],
                    'selling_price' => $record['harga_jual'] ?? 0,
                    'hpp' => $record['hpp'] ?? 0,
                ]
            );

            if (!empty($record['harga_jual']) || !empty($record['hpp'])) {
                ItemPrice::updateOrCreate(
                    [
                        'item_id' => $item->id,
                        'effective_date' => now()->startOfYear()->toDateString(),
                    ],
                    [
                        'selling_price' => $record['harga_jual'] ?? 0,
                        'hpp' => $record['hpp'] ?? 0,
                    ]
                );
            }

            $variantCreated = false;
            foreach ($this->sizeCodes as $sizeLabel => $sizeCode) {
                $qty = $record['sizes'][$sizeLabel] ?? null;
                if ($qty === null) {
                    continue;
                }

                ItemVariant::updateOrCreate(
                    [
                        'item_id' => $item->id,
                        'size' => $sizeCode,
                    ],
                    [
                        'sku' => $item->code . '-' . $sizeCode,
                        'size_label' => $sizeLabel,
                        'price' => $record['harga_jual'] ?? 0,
                    ]
                );
                $variantCreated = true;
            }

            if (!$variantCreated) {
                ItemVariant::firstOrCreate(
                    ['item_id' => $item->id, 'size' => '01'],
                    [
                        'sku' => $item->code . '-01',
                        'size_label' => 'All Size',
                        'price' => $record['harga_jual'] ?? 0,
                    ]
                );
            }

            $this->importedCount++;
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
        $allLabels = array_keys($this->sizeCodes);

        foreach ($rows as $index => $row) {
            $values = $row instanceof Collection ? $row->toArray() : (array) $row;

            if ($this->shouldSkipRow($values)) {
                continue;
            }

            $record = [
                'row' => $index + 1,
                'kategori' => $this->clean($values['kategori'] ?? null),
                'gender' => strtoupper($this->clean($values['gender'] ?? null) ?? ''),
                'nama' => $this->clean($values['nama_item'] ?? $values['nama'] ?? null),
                'kode' => $this->clean($values['kode_barang'] ?? $values['kode'] ?? null),
                'departemen' => $this->clean($values['departemen'] ?? null),
                'satuan' => $this->clean($values['satuan'] ?? null),
                'harga_jual' => $this->parseNumeric($values['harga_jual'] ?? null),
                'hpp' => $this->parseNumeric($values['hpp'] ?? null),
                'sizes' => [],
            ];

            foreach ($allLabels as $label) {
                $labelLower = strtolower($label);
                $found = false;
                foreach ($values as $key => $val) {
                    if (strtolower((string) $key) === $labelLower) {
                        $parsed = $this->parseNumeric($val);
                        if ($parsed !== null) {
                            $record['sizes'][$label] = $parsed;
                        }
                        $found = true;
                        break;
                    }
                }
                if (!$found && isset($values[$label])) {
                    $parsed = $this->parseNumeric($values[$label]);
                    if ($parsed !== null) {
                        $record['sizes'][$label] = $parsed;
                    }
                }
            }

            $records[] = $record;
        }

        return $records;
    }

    private function validateRecords(array &$records): array
    {
        $failures = [];
        $seenCodes = [];

        foreach ($records as &$record) {
            $rules = [
                'nama' => ['required', 'string', 'max:255'],
                'kategori' => ['required', 'string', 'max:255'],
                'gender' => ['required', 'string', 'in:L,P,U'],
                'satuan' => ['required', 'string', 'max:50'],
            ];

            $validator = Validator::make($record, $rules);

            foreach ($validator->errors()->messages() as $attribute => $messages) {
                $failures[] = new Failure($record['row'], $attribute, $messages, $record);
            }

            if ($record['kode'] && in_array($record['kode'], $seenCodes, true)) {
                $failures[] = new Failure($record['row'], 'kode', ['Duplicate item code in this file.'], $record);
            }
            if ($record['kode']) {
                $seenCodes[] = $record['kode'];
            }

            $record['kategori'] = strtoupper($record['kategori']);
        }

        return $failures;
    }

    private function generateBaseCode(array $record): string
    {
        return implode('-', array_filter([$record['kategori'], $record['gender'], substr(preg_replace('/[^A-Z]/', '', strtoupper($record['nama'])), 0, 3)]));
    }

    private function resolveCategory(string $name): ?ItemCategory
    {
        $code = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 3));

        $category = ItemCategory::where('label', $name)->orWhere('code', $code)->first();
        if (!$category) {
            $baseCode = $code;
            $suffix = 1;
            while (ItemCategory::where('code', $baseCode)->exists()) {
                $baseCode = $code . $suffix++;
            }
            $category = ItemCategory::create(['label' => $name, 'code' => $baseCode]);
        }
        return $category;
    }

    private function resolveDepartment(string $name): ?ItemDepartment
    {
        if (!$name) {
            return null;
        }
        return ItemDepartment::where('label', $name)->orWhere('code', $name)->first();
    }

    private function shouldSkipRow(array $values): bool
    {
        $firstKey = array_key_first($values);
        $firstVal = $this->clean($values[$firstKey] ?? null);

        if ($firstVal === null) {
            return collect($values)->filter(fn ($v) => $this->clean($v) !== null)->isEmpty();
        }

        return Str::startsWith(Str::upper($firstVal), [
            'TEMPLATE IMPORT', 'ISI DATA', 'URUTAN KOLOM',
            'KODE BARANG', 'CONTOH FORMAT', 'CONTOH',
        ]);
    }

    private function clean(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (is_numeric($value) && (str_contains(strtolower((string)$value), 'e+') || is_float($value))) {
            $value = number_format((float)$value, 0, '', '');
        }
        $value = ltrim(trim((string) $value), "'");
        return $value === '' ? null : $value;
    }

    private function parseNumeric(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }
        $cleaned = preg_replace('/[^0-9]/', '', (string) $value);
        return $cleaned !== '' ? (int) $cleaned : null;
    }
}
