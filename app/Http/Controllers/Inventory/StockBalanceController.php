<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use App\Models\StockBalance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockBalanceController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = StockBalance::with(['item.category', 'variant']);

        if ($search = $request->input('q')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
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
