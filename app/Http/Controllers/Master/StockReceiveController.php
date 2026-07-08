<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockReceiveRequest;
use App\Models\Item;
use App\Models\StockReceive;
use App\Models\Vendor;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockReceiveController extends Controller
{
    public function __construct(
        private readonly StockService $stockService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = StockReceive::with('vendor', 'items');

        if ($search = $request->input('q')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereRelation('vendor', 'name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $receives = $query->latest()->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('inventory.stock-receive._table', compact('receives'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $receives])->render(),
            ]);
        }

        return view('inventory.stock-receive.index', compact('receives'));
    }

    public function create(): View
    {
        $vendors = Vendor::orderBy('name')->get();

        return view('inventory.stock-receive.create', compact('vendors'));
    }

    public function searchItems(Request $request): JsonResponse
    {
        $query = Item::whereNotNull('base_code')
            ->select('base_code')
            ->distinct()
            ->orderBy('base_code');

        if ($q = $request->input('q')) {
            $q = str_replace(['%', '_'], ['\%', '\_'], $q);
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('base_code', 'like', "%{$q}%");
            });
        }

        $items = $query->limit(20)->get()->map(function ($row) {
            $rep = Item::where('base_code', $row->base_code)->first();
            return [
                'id' => $row->base_code,
                'label' => ($rep->name ?? '?') . ' (' . $row->base_code . ')',
            ];
        });

        return response()->json($items);
    }

    public function variantsByBaseCode(string $baseCode): JsonResponse
    {
        $items = Item::with('variants')->where('base_code', $baseCode)->get();

        $variants = collect();
        foreach ($items as $item) {
            foreach ($item->variants as $variant) {
                $variants->push([
                    'id' => $variant->id,
                    'item_id' => $item->id,
                    'label' => $variant->size_label . ' (' . $variant->sku . ')',
                ]);
            }
        }

        return response()->json($variants->sortBy('label')->values());
    }

    public function variantsByItem(Item $item): JsonResponse
    {
        $variants = $item->variants()->orderBy('size')->get()->map(fn ($v) => [
            'id' => $v->id,
            'label' => $v->size_label . ' (' . $v->sku . ')',
        ]);

        return response()->json($variants);
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
