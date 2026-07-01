<?php

namespace App\Services;

use App\Imports\EligibilityImport;
use App\Imports\EntitlementImport;
use App\Imports\ItemImport;
use App\Imports\ItemPriceImport;
use App\Imports\StockOpnameImport;
use App\Imports\StudentImport;
use App\Models\ImportBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ImportService
{
    public function processImport(string $type, string $filePath, int $userId): ImportBatch
    {
        $batch = ImportBatch::create([
            'import_type' => $type,
            'file_name' => basename($filePath),
            'total_rows' => 0,
            'success_rows' => 0,
            'failed_rows' => 0,
            'status' => 'processing',
            'imported_by' => $userId,
        ]);

        try {
            $importer = $this->resolveImporter($type, $filePath);

            $collection = Excel::toCollection($importer, $filePath)->first();
            $totalRows = $collection->count();

            $batch->update(['total_rows' => $totalRows]);

            Excel::import($importer, $filePath);

            $batch->update([
                'status' => 'completed',
                'success_rows' => $totalRows,
            ]);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorLog = collect($failures)->map(fn ($f) => [
                'row' => $f->row(),
                'attribute' => $f->attribute(),
                'errors' => $f->errors(),
            ])->toArray();

            $batch->update([
                'status' => 'failed',
                'failed_rows' => count($failures),
                'success_rows' => $totalRows - count($failures),
                'error_log' => $errorLog,
            ]);

            Log::error("Import {$type} failed", ['batch_id' => $batch->id, 'errors' => $errorLog]);
        } catch (\Exception $e) {
            $batch->update([
                'status' => 'failed',
                'error_log' => ['message' => $e->getMessage()],
            ]);

            Log::error("Import {$type} exception", ['batch_id' => $batch->id, 'exception' => $e->getMessage()]);
        }

        return $batch->fresh();
    }

    protected function resolveImporter(string $type, string $filePath): object
    {
        return match ($type) {
            'student' => new StudentImport(),
            'eligibility' => new EligibilityImport(),
            'item' => new ItemImport(),
            'stock_opname' => $this->resolveStockOpnameImporter($filePath),
            'item_price' => new ItemPriceImport(),
            'entitlement' => new EntitlementImport(),
            default => throw new \InvalidArgumentException("Import type '{$type}' is not supported."),
        };
    }

    protected function resolveStockOpnameImporter(string $filePath): StockOpnameImport
    {
        $stockOpnameId = request()->input('stock_opname_id');

        if (!$stockOpnameId) {
            throw new \InvalidArgumentException('stock_opname_id is required for stock opname import.');
        }

        return new StockOpnameImport($stockOpnameId);
    }
}
