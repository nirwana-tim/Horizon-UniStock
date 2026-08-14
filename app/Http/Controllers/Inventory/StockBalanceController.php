<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use App\Models\StockBalance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockBalanceController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = StockBalance::with(['item.category', 'variant']);

        if ($search = $request->input('q')) {
            $search = $this->escapeLike($search);
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('base_code', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->whereHas('item', fn ($q) => $q->where('category_id', $category));
        }

        if ($gender = $request->input('gender')) {
            $query->whereHas('item', fn ($q) => $q->where('gender', $gender));
        }

        $balances = $query->orderBy('item_id')->orderBy('variant_id')->paginate(20);

        $demands = DB::table('student_size_items')
            ->join('student_size_profiles', 'student_size_items.size_profile_id', '=', 'student_size_profiles.id')
            ->select('student_size_items.item_id', 'student_size_items.size')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('student_size_items.item_id', 'student_size_items.size')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->item_id.'|'.$row->size => (int) $row->total]);

        $balances->setCollection(
            $balances->getCollection()->map(function (StockBalance $balance) use ($demands) {
                $balance->demand = $demands[$balance->item_id.'|'.$balance->variant?->size] ?? 0;

                return $balance;
            })
        );

        if ($request->ajax()) {
            return response()->json([
                'html' => view('inventory.stock-balance._table', compact('balances'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $balances])->render(),
            ]);
        }

        $categories = ItemCategory::orderBy('label')->get();

        return view('inventory.stock-balance.index', compact('balances', 'categories'));
    }
}
