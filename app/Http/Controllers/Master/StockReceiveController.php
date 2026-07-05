<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockReceiveRequest;
use App\Models\Item;
use App\Models\StockReceive;
use App\Models\Vendor;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockReceiveController extends Controller
{
    public function __construct(
        private readonly StockService $stockService
    ) {}

    public function index(): View
    {
        $receives = StockReceive::with('vendor', 'items')
            ->latest()
            ->paginate(15);

        return view('inventory.stock-receive.index', compact('receives'));
    }

    public function create(): View
    {
        $vendors = Vendor::orderBy('name')->get();
        $items = Item::with('category', 'variants')->orderBy('name')->get();

        return view('inventory.stock-receive.create', compact('vendors', 'items'));
    }

    public function store(StockReceiveRequest $request): RedirectResponse
    {
        $this->stockService->receiveStock($request->validated());

        return redirect()
            ->route('inventory.stock-receive.index')
            ->with('success', 'Penerimaan barang berhasil dicatat.');
    }

    public function show(StockReceive $stockReceive): View
    {
        $stockReceive->load(['vendor', 'items.item', 'items.variant']);

        return view('inventory.stock-receive.show', compact('stockReceive'));
    }

    public function destroy(StockReceive $stockReceive): RedirectResponse
    {
        if ($stockReceive->status !== 'received') {
            return back()->with('error', 'Hanya penerimaan dengan status "received" yang bisa dihapus.');
        }

        $stockReceive->items()->delete();
        $stockReceive->delete();

        return redirect()
            ->route('inventory.stock-receive.index')
            ->with('success', 'Penerimaan barang berhasil dihapus.');
    }
}
