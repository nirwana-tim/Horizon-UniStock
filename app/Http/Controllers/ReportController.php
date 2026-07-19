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
use App\Exports\Reports\SizeRecapReport;
use App\Models\ProgramLevel;
use App\Models\StudyProgram;
use App\Models\ItemCategory;
use App\Models\StockOpname;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $periods = DistributionSchedule::select('period')->whereNotNull('period')->groupBy('period')->orderBy('period', 'desc')->pluck('period', 'period');
        $stockOpnames = StockOpname::orderBy('created_at', 'desc')->pluck('period', 'id');
        $items = Item::orderBy('name', 'asc')->pluck('code', 'code');
        $categories = ItemCategory::orderBy('code', 'asc')->get(['code', 'label']);
        
        $programLevels = ProgramLevel::orderBy('name', 'asc')->get();
        $studyPrograms = StudyProgram::orderBy('name', 'asc')->get();

        return view('report.index', compact('periods', 'stockOpnames', 'items', 'categories', 'programLevels', 'studyPrograms'));
    }

    public function sizeRecap(Request $request)
    {
        $request->validate([
            'program_level_id' => 'nullable|integer|exists:program_levels,id',
            'study_program_id' => 'nullable|integer|exists:study_programs,id',
        ]);

        $levelId = $request->input('program_level_id');
        $prodiId = $request->input('study_program_id');
        $filename = 'Laporan_Rekap_Kebutuhan_Ukuran.xlsx';

        return Excel::download(new SizeRecapReport($levelId, $prodiId), $filename);
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

    public function salesDashboard(Request $request): View|JsonResponse
    {
        if ($request->has('get_items') && $request->has('category_id')) {
            $items = Item::where('category_id', $request->input('category_id'))
                ->orderBy('name')
                ->get(['id', 'name']);
            return response()->json($items);
        }

        if ($request->wantsJson() || $request->has('ajax')) {
            $processed = app(ReportService::class)->getSalesDashboardProcessedData(
                $request->input('start_date'),
                $request->input('end_date'),
                $request->input('category_id') ? (int) $request->input('category_id') : null,
                $request->input('item_id') ? (int) $request->input('item_id') : null
            );
            return response()->json($processed);
        }

        $minDate = \Illuminate\Support\Facades\DB::table('distribution_transactions')->whereNotNull('pickup_time')->min('pickup_time');
        $maxDate = \Illuminate\Support\Facades\DB::table('distribution_transactions')->whereNotNull('pickup_time')->max('pickup_time');

        $defaultStart = $minDate ? \Carbon\Carbon::parse($minDate)->toDateString() : now()->startOfMonth()->toDateString();
        $defaultEnd = $maxDate ? \Carbon\Carbon::parse($maxDate)->toDateString() : now()->toDateString();

        $categories = ItemCategory::orderBy('code', 'asc')->get(['id', 'code', 'label']);

        return view('report.sales-dashboard', compact('defaultStart', 'defaultEnd', 'categories'));
    }

    public function distributionRecap(Request $request): View|JsonResponse
    {
        $period = $request->input('period');
        $studyProgramId = $request->input('study_program_id');

        $data = app(ReportService::class)->getDistributionRecap($period, $studyProgramId);

        if ($request->ajax()) {
            $html = view('report.distribution-recap', compact('data'))->render();
            return response()->json(compact('html'));
        }

        $periods = DistributionSchedule::select('period')->whereNotNull('period')->groupBy('period')->orderBy('period', 'desc')->pluck('period', 'period');
        $studyPrograms = StudyProgram::orderBy('name', 'asc')->get();

        return view('report.distribution-recap', compact('data', 'periods', 'studyPrograms', 'period', 'studyProgramId'));
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
