<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Imports\StockOpnameImport;
use App\Models\StockOpname;
use App\Services\StockOpnameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    public function __construct(
        private StockOpnameService $service
    ) {}

    public function index(): View
    {
        $batches = StockOpname::with('creator')
            ->latest()
            ->paginate(15);

        return view('inventory.stock-opname.index', compact('batches'));
    }

    public function create(): View
    {
        return view('inventory.stock-opname.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'opname_date' => 'required|date',
            'period' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $batch = $this->service->createBatch($validated);

        return redirect()->route('finance.stock-opname.show', $batch)
            ->with('success', 'Batch stock opname berhasil dibuat.');
    }

    public function show(StockOpname $stockOpname): View
    {
        $stockOpname->load(['items.item', 'items.variant', 'creator', 'adjustments.approver']);

        $this->service->calculateVariance($stockOpname);

        return view('inventory.stock-opname.show', ['batch' => $stockOpname]);
    }

    public function upload(Request $request, StockOpname $stockOpname)
    {
        $request->validate([
            'opname_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('opname_file');
        $import = new StockOpnameImport($stockOpname->id);
        Excel::import($import, $file);

        $stockOpname->update(['status' => 'counted']);

        return redirect()->route('finance.stock-opname.show', $stockOpname)
            ->with('success', 'Data stock opname berhasil diupload.')
            ->with('total_imported', $import->getImportedRows());
    }

    public function approve(Request $request, StockOpname $stockOpname)
    {
        $this->service->createAdjustments($stockOpname, Auth::user());

        return redirect()->route('finance.stock-opname.show', $stockOpname)
            ->with('success', 'Stock opname berhasil disetujui dan adjustment telah dibuat.');
    }
}
