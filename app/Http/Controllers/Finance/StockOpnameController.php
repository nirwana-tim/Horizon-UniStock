<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\StockBalance;
use App\Models\StockOpname;
use App\Services\StockOpnameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return view('stock-opname.index', compact('batches'));
    }

    public function create(): View
    {
        return view('stock-opname.create');
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

        return view('stock-opname.show', ['batch' => $stockOpname]);
    }

    public function upload(Request $request, StockOpname $stockOpname)
    {
        $request->validate([
            'opname_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('opname_file');
        $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);

        $items = [];
        if (isset($rows[0])) {
            foreach ($rows[0] as $index => $row) {
                if ($index === 0) continue;

                $items[] = [
                    'item_id' => $row[0] ?? null,
                    'variant_id' => $row[1] ?? null,
                    'physical_quantity' => $row[2] ?? 0,
                    'notes' => $row[3] ?? null,
                ];
            }
        }

        $this->service->processUpload($stockOpname, $items);

        return redirect()->route('finance.stock-opname.show', $stockOpname)
            ->with('success', 'Data stock opname berhasil diupload.');
    }

    public function approve(Request $request, StockOpname $stockOpname)
    {
        $stockOpname->update(['status' => 'approved']);

        $this->service->createAdjustments($stockOpname, Auth::user());

        return redirect()->route('finance.stock-opname.show', $stockOpname)
            ->with('success', 'Stock opname berhasil disetujui dan adjustment telah dibuat.');
    }
}
