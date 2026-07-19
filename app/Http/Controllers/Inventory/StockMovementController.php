<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = StockMovement::with(['item.category', 'variant']);

        if ($search = $request->input('q')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $movements = $query->latest()->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('inventory.stock-movement._table', compact('movements'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $movements])->render(),
            ]);
        }

        return view('inventory.stock-movement.index', compact('movements'));
    }
}
