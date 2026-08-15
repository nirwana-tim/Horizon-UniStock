<?php

namespace App\Http\Controllers;

use App\Models\ImportBatch;
use App\Services\ImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function __construct(
        protected ImportService $importService
    ) {}

    public function index(): View
    {
        return view('import.index');
    }

    public function result(ImportBatch $importBatch): View
    {
        return view('import.result', [
            'batch' => $importBatch,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'import_type' => ['required', 'string', 'in:student,eligibility,item,stock_opname,item_price,entitlement,stock_receive'],
            'stock_opname_id' => ['nullable', 'integer', 'exists:stock_opnames,id'],
        ];

        if ($request->has('file_path') && $request->filled('file_path')) {
            $validated = $request->validate($rules);
            $filePath = $request->input('file_path');

            if (! str_starts_with($filePath, 'imports/') || str_contains($filePath, '..') || str_contains($filePath, '\\')) {
                return back()->with('error', 'Invalid file path.');
            }

            if (! Storage::disk('local')->exists($filePath)) {
                return back()->with('error', 'File not found. Please upload again.');
            }
        } else {
            $rules['file'] = ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'];
            $validated = $request->validate($rules);
            $file = $request->file('file');
            $fileName = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs('imports', $fileName, 'local');
        }

        $batch = $this->importService->processImport(
            $request->input('import_type'),
            Storage::disk('local')->path($filePath),
            $request->user()->id,
            $request->integer('stock_opname_id') ?: null
        );

        if ($batch->status === 'completed') {
            return redirect()
                ->route('import.result', $batch)
                ->with('success', "Import berhasil. {$batch->success_rows} dari {$batch->total_rows} baris diproses.");
        }

        return redirect()
            ->route('import.result', $batch)
            ->with('error', 'Import gagal. Lihat log untuk detail.');
    }

    public function preview(Request $request): View
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'import_type' => ['required', 'string', 'in:student,eligibility,item,stock_opname,item_price,entitlement,stock_receive'],
        ]);

        $file = $request->file('file');
        $fileName = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $filePath = $file->storeAs('imports', $fileName, 'local');

        $data = Excel::toCollection(
            null,
            Storage::disk('local')->path($filePath)
        )->first();

        $headingRow = $this->headingRowFor($validated['import_type']);
        $headers = $data->isNotEmpty() && $data->has($headingRow - 1)
            ? $data->get($headingRow - 1)->values()->map(fn ($value) => (string) $value)->all()
            : [];
        $rawRows = $data->isNotEmpty() ? $data->skip($headingRow)->values() : collect();
        $rows = $rawRows->map(function ($row) use ($headers) {
            $values = $row instanceof Collection ? $row->values()->all() : array_values((array) $row);

            $keyed = [];
            foreach ($headers as $index => $header) {
                $keyed[$header] = $values[$index] ?? null;
            }

            return $keyed;
        });
        $totalDataRows = $rows->count();

        return view('import.preview', [
            'headers' => $headers,
            'rows' => $rows,
            'importType' => $validated['import_type'],
            'filePath' => $filePath,
            'totalDataRows' => $totalDataRows,
        ]);
    }

    private function headingRowFor(string $importType): int
    {
        return $importType === 'student' ? 1 : 4;
    }
}
