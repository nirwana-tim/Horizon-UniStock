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
        $totalRows = 0;
        $importer = null;

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

            $collection = Excel::toCollection(null, $filePath)->first() ?? collect();
            $totalRows = method_exists($importer, 'countRows')
                ? $importer->countRows($collection)
                : $collection->count();

            $batch->update(['total_rows' => $totalRows]);

            Excel::import($importer, $filePath);

            $successRows = method_exists($importer, 'getImportedCount')
                ? $importer->getImportedCount()
                : $totalRows;

            $batch->update([
                'status' => 'completed',
                'total_rows' => method_exists($importer, 'getTotalRows') ? $importer->getTotalRows() : $totalRows,
                'success_rows' => $successRows,
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
                'total_rows' => method_exists($importer, 'getTotalRows') && $importer->getTotalRows() > 0
                    ? $importer->getTotalRows()
                    : $totalRows,
                'failed_rows' => count($failures),
                'success_rows' => 0,
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
