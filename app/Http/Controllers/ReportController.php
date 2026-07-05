<?php

namespace App\Http\Controllers;

use App\Exports\DistributionReportExport;
use App\Exports\GpmReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\Reports\LossReport;
use App\Exports\Reports\StockCardReport;
use App\Exports\Reports\StockOpnameReport;
use App\Exports\Reports\StockReport;
use App\Models\DistributionSchedule;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $periods = DistributionSchedule::whereNotNull('period')->distinct()->orderBy('period', 'desc')->pluck('period', 'period');
        $stockOpnames = StockOpname::orderBy('created_at', 'desc')->pluck('period', 'id');
        $items = Item::orderBy('name')->pluck('code', 'code');
        $categories = ItemCategory::orderBy('code')->get(['code', 'name']);

        return view('report.index', compact('periods', 'stockOpnames', 'items', 'categories'));
    }

    public function distribution(Request $request)
    {
        $request->validate([
            'period' => 'nullable|string',
        ]);

        $period = $request->input('period');
        $filename = 'Laporan_Distribusi' . ($period ? "_{$period}" : '') . '.xlsx';

        return Excel::download(new DistributionReportExport($period), $filename);
    }

    public function inventory(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string',
        ]);

        $category = $request->input('category');
        $filename = 'Laporan_Inventory' . ($category ? "_{$category}" : '') . '.xlsx';

        return Excel::download(new InventoryReportExport($category), $filename);
    }

    public function gpm(Request $request)
    {
        $request->validate([
            'period' => 'nullable|string',
        ]);

        $period = $request->input('period');
        $filename = 'Laporan_GPM' . ($period ? "_{$period}" : '') . '.xlsx';

        return Excel::download(new GpmReportExport($period), $filename);
    }

    public function stock(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string',
            'gender' => 'nullable|string|in:L,P,U',
        ]);

        $filename = 'Laporan_Stok_Inventaris.xlsx';

        return Excel::download(
            new StockReport($request->input('category'), $request->input('gender')),
            $filename
        );
    }

    public function stockOpname(Request $request)
    {
        $request->validate([
            'stock_opname_id' => ['required', 'integer', 'exists:stock_opnames,id'],
        ]);

        $stockOpname = StockOpname::findOrFail($request->input('stock_opname_id'));
        $filename = 'Laporan_Stok_Opname_' . $stockOpname->reference_number . '.xlsx';

        return Excel::download(new StockOpnameReport($stockOpname), $filename);
    }

    public function stockCard(Request $request)
    {
        $request->validate([
            'item_code' => ['required', 'string', 'exists:items,code'],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $filename = 'Kartu_Stok_' . $request->input('item_code') . '.xlsx';

        return Excel::download(
            new StockCardReport(
                $request->input('item_code'),
                $request->input('start_date'),
                $request->input('end_date')
            ),
            $filename
        );
    }

    public function loss(Request $request)
    {
        $request->validate([
            'period' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        $filename = 'Laporan_Susut_Stok.xlsx';

        return Excel::download(
            new LossReport($request->input('period'), $request->input('category')),
            $filename
        );
    }
}
