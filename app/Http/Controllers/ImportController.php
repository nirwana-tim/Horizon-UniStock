<?php

namespace App\Http\Controllers;

use App\Models\ImportBatch;
use App\Services\ImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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
        $validated = $request->validate([
            'import_type' => ['required', 'string', 'in:student,eligibility,item,stock_opname,item_price,entitlement'],
            'stock_opname_id' => ['required_if:import_type,stock_opname', 'nullable', 'integer', 'exists:stock_opnames,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $filePath = $file->storeAs('imports', $fileName, 'local');

        $batch = $this->importService->processImport(
            $validated['import_type'],
            Storage::disk('local')->path($filePath),
            $request->user()->id
        );

        if ($batch->status === 'completed') {
            return redirect()
                ->route('import.result', $batch)
                ->with('success', "Import berhasil. {$batch->success_rows} dari {$batch->total_rows} baris diproses.");
        }

        return redirect()
            ->route('import.result', $batch)
            ->with('error', "Import gagal. Lihat log untuk detail.");
    }

    public function preview(Request $request): View
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'import_type' => ['required', 'string', 'in:student,eligibility,item,stock_opname,item_price,entitlement'],
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $filePath = $file->storeAs('imports', $fileName, 'local');

        $data = \Maatwebsite\Excel\Facades\Excel::toCollection(
            null,
            Storage::disk('local')->path($filePath)
        )->first();

        return view('import.preview', [
            'data' => $data,
            'importType' => $validated['import_type'],
            'filePath' => $filePath,
        ]);
    }
}
