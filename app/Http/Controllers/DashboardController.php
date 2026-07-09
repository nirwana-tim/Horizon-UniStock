<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\Faculty;
use App\Models\Item;
use App\Models\StockBalance;
use App\Models\StockOpname;
use App\Models\StockReceive;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        if ($user->hasRole(Role::SuperAdmin->value)) {
            return view('dashboards.super-admin');
        }

        if ($user->hasRole(Role::Admin->value)) {
            return view('dashboards.finance');
        }

        if ($user->hasRole(Role::Staff->value)) {
            return $this->staffDashboard();
        }

        return $this->studentDashboard();
    }

    public function stats(): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(Role::SuperAdmin->value)) {
            return response()->json([
                'totalUsers' => User::count(),
                'totalStudents' => Student::count(),
                'totalItems' => Item::count(),
                'totalStockReceives' => StockReceive::count(),
                'outOfStockItems' => StockBalance::where('quantity', '<=', 0)->count(),
            ]);
        }

        return response()->json([
            'totalFaculties' => Faculty::count(),
            'totalStudyPrograms' => StudyProgram::count(),
            'totalItems' => Item::count(),
            'monthlyReceives' => StockReceive::whereMonth('receive_date', now()->month)->count(),
            'draftOpnames' => StockOpname::where('status', 'draft')->count(),
            'outOfStockItems' => StockBalance::where('quantity', '<=', 0)->count(),
        ]);
    }

    public function salesChart(): JsonResponse
    {
        $reportService = app(ReportService::class);
        $trend = $reportService->getMonthlySalesTrend(6);

        $months = [];
        $revenue = [];
        $units = [];

        foreach ($trend as $row) {
            $months[] = \Carbon\Carbon::create()->month($row->month)->format('M') . '-' . substr($row->year, -2);
            $revenue[] = (int) $row->revenue;
            $units[] = (int) $row->unit_sold;
        }

        // Chart 1: Unit Sold by Item (kolom)
        $unitSold = DB::table('distribution_items as di')
            ->select('i.name', DB::raw('SUM(di.quantity) as total'))
            ->join('items as i', 'i.id', '=', 'di.item_id')
            ->join('distribution_transactions as dt', 'dt.id', '=', 'di.transaction_id')
            ->where('dt.pickup_time', '>=', now()->subMonths(6))
            ->groupBy('i.id', 'i.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Chart 2: Revenue by Category — stacked by item (barang bertumpuk)
        $revByCatItem = DB::table('distribution_items as di')
            ->select('ic.label as category', 'i.name as item', DB::raw('SUM(di.quantity * i.selling_price) as total'))
            ->join('items as i', 'i.id', '=', 'di.item_id')
            ->join('item_categories as ic', 'ic.id', '=', 'i.category_id')
            ->join('distribution_transactions as dt', 'dt.id', '=', 'di.transaction_id')
            ->where('dt.pickup_time', '>=', now()->subMonths(6))
            ->groupBy('ic.id', 'ic.label', 'i.id', 'i.name')
            ->orderBy('ic.label')
            ->orderByDesc('total')
            ->get();

        // Chart 4: Available Stock by Item (kolom)
        $availStock = DB::table('stock_balances as sb')
            ->select('i.name', DB::raw('SUM(sb.quantity) as total'))
            ->join('items as i', 'i.id', '=', 'sb.item_id')
            ->where('sb.quantity', '>', 0)
            ->groupBy('i.id', 'i.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Chart 5: Value Stock by Category — stacked by item (batang bertumpuk)
        $valByCatItem = DB::table('stock_balances as sb')
            ->select('ic.label as category', 'i.name as item', DB::raw('SUM(sb.quantity * i.selling_price) as total'))
            ->join('items as i', 'i.id', '=', 'sb.item_id')
            ->join('item_categories as ic', 'ic.id', '=', 'i.category_id')
            ->where('sb.quantity', '>', 0)
            ->groupBy('ic.id', 'ic.label', 'i.id', 'i.name')
            ->orderBy('ic.label')
            ->orderByDesc('total')
            ->get();

        // Chart 6: % Unit Sold by Item (lingkaran)
        $totalSoldAll = DB::table('distribution_items as di')
            ->join('distribution_transactions as dt', 'dt.id', '=', 'di.transaction_id')
            ->where('dt.pickup_time', '>=', now()->subMonths(6))
            ->sum('di.quantity');

        $pctSold = DB::table('distribution_items as di')
            ->select('i.name', DB::raw('SUM(di.quantity) as total'))
            ->join('items as i', 'i.id', '=', 'di.item_id')
            ->join('distribution_transactions as dt', 'dt.id', '=', 'di.transaction_id')
            ->where('dt.pickup_time', '>=', now()->subMonths(6))
            ->groupBy('i.id', 'i.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(fn($row) => [
                'name' => $row->name,
                'pct' => $totalSoldAll > 0 ? round(($row->total / $totalSoldAll) * 100, 1) : 0,
            ]);

        return response()->json([
            // Chart 1 — Unit Sold by Item
            'c1Labels' => $unitSold->pluck('name'),
            'c1Data'   => $unitSold->pluck('total')->map(fn($v) => (int) $v),
            // Chart 2 — Revenue by Category (stacked)
            'c2Categories' => $revByCatItem->pluck('category')->unique()->values(),
            'c2Datasets'   => $this->buildStacked($revByCatItem, 'category', 'item', 'total'),
            // Chart 3 — Revenue + Unit Sold by Month (combo)
            'months'  => $months,
            'revenue' => $revenue,
            'units'   => $units,
            // Chart 4 — Available Stock by Item
            'c4Labels' => $availStock->pluck('name'),
            'c4Data'   => $availStock->pluck('total')->map(fn($v) => (int) $v),
            // Chart 5 — Value Stock by Category (stacked)
            'c5Categories' => $valByCatItem->pluck('category')->unique()->values(),
            'c5Datasets'   => $this->buildStacked($valByCatItem, 'category', 'item', 'total'),
            // Chart 6 — % Unit Sold by Item
            'c6Labels' => $pctSold->pluck('name'),
            'c6Data'   => $pctSold->pluck('pct'),
        ]);
    }

    private function buildStacked($rows, string $catField, string $itemField, string $valField): array
    {
        $categories = $rows->pluck($catField)->unique()->values()->toArray();
        $catIndex = array_flip($categories);

        $itemMap = [];
        foreach ($rows as $row) {
            $name = $row->$itemField;
            if (!isset($itemMap[$name])) {
                $itemMap[$name] = array_fill(0, count($categories), 0);
            }
            $idx = $catIndex[$row->$catField] ?? null;
            if ($idx !== null) {
                $itemMap[$name][$idx] = (int) $row->$valField;
            }
        }

        $datasets = [];
        foreach ($itemMap as $label => $data) {
            $datasets[] = ['label' => $label, 'data' => $data];
        }

        return $datasets;
    }

    public function lowStock(): View|JsonResponse
    {
        $lowStockItems = StockBalance::with('item')
            ->where('quantity', '>', 0)
            ->where('quantity', '<=', 5)
            ->orderBy('quantity')
            ->take(10)
            ->get();

        if (request()->wantsJson()) {
            return response()->json([
                'html' => view('dashboards._low-stock', compact('lowStockItems'))->render(),
            ]);
        }

        return view('dashboards._low-stock', compact('lowStockItems'));
    }

    private function staffDashboard(): View
    {
        $activeSchedule = DistributionSchedule::with('programLevel', 'faculty')
            ->where('is_active', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->first();

        $todayCount = DistributionTransaction::whereDate('created_at', today())->count();

        $recentTransactions = DistributionTransaction::with('student.user', 'schedule')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboards.staff', compact('activeSchedule', 'todayCount', 'recentTransactions'));
    }

    private function studentDashboard(): View
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $student->load(['activeSizeProfile', 'studyProgram', 'programLevel']);

        $data = [
            'student' => $student,
            'hasFilledSize' => !is_null($student->activeSizeProfile),
            'hasQr' => true,
            'activeSchedules' => DistributionSchedule::query()
                ->where('is_active', true)
                ->where('date', '>=', now()->format('Y-m-d'))
                ->where(fn ($q) => $q->whereNull('program_level_id')->orWhere('program_level_id', $student->program_level_id))
                ->where(fn ($q) => $q->whereNull('faculty_id')->orWhere('faculty_id', $student->studyProgram?->faculty_id))
                ->where(fn ($q) => $q->whereNull('study_program_id')->orWhere('study_program_id', $student->study_program_id))
                ->orderBy('date')
                ->take(5)
                ->get(),
            'recentTransactions' => DistributionTransaction::with('schedule')
                ->where('student_id', $student->id)
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('dashboards.student', $data);
    }
}
