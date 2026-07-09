<?php

namespace App\Services;

use App\Models\DistributionItem;
use App\Models\DistributionTransaction;
use App\Models\Item;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDistributionReportData(?string $period = null, ?int $studyProgramId = null): Collection
    {
        $query = DistributionTransaction::with(['student.studyProgram', 'student.programLevel', 'items.item', 'schedule']);

        if ($period) {
            $query->whereHas('schedule', function ($q) use ($period) {
                $q->where('period', $period);
            });
        }

        if ($studyProgramId) {
            $query->whereHas('student', function ($q) use ($studyProgramId) {
                $q->where('study_program_id', $studyProgramId);
            });
        }

        return $query->latest()->get();
    }

    public function getDistributionSummary(?string $period = null): Collection
    {
        $query = DistributionTransaction::select(
            'students.study_program_id',
            'study_programs.name as study_program_name',
            DB::raw('COUNT(DISTINCT distribution_transactions.student_id) as total_students'),
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(CASE WHEN distribution_transactions.status = "completed" THEN 1 ELSE 0 END) as completed'),
            DB::raw('SUM(CASE WHEN distribution_transactions.status = "partial" THEN 1 ELSE 0 END) as partial')
        )
            ->join('students', 'distribution_transactions.student_id', '=', 'students.id')
            ->join('study_programs', 'students.study_program_id', '=', 'study_programs.id')
            ->groupBy('students.study_program_id', 'study_programs.name');

        if ($period) {
            $query->whereHas('schedule', function ($q) use ($period) {
                $q->where('period', $period);
            });
        }

        return $query->orderBy('study_programs.name')->get();
    }

    public function getStockReportData(?string $category = null, ?string $gender = null): Collection
    {
        return app(StockService::class)->getAllBalances($category, $gender);
    }

    public function getStockOpnameReportData(StockOpname $stockOpname): Collection
    {
        return $stockOpname->items()
            ->with(['item.category', 'variant'])
            ->join('items', 'stock_opname_items.item_id', '=', 'items.id')
            ->leftJoin('item_variants', 'stock_opname_items.variant_id', '=', 'item_variants.id')
            ->select(
                'stock_opname_items.*',
                'items.name as item_name',
                'items.code as item_code',
                'items.hpp',
                'item_variants.size as variant_size'
            )
            ->orderBy('items.name')
            ->get();
    }

    public function getGpmReportData(?string $period = null, ?int $categoryId = null): Collection
    {
        $query = DistributionItem::select(
            'distribution_items.item_id',
            DB::raw('SUM(distribution_items.quantity) as qty_sold'),
            DB::raw('SUM(distribution_items.quantity * items.hpp) as total_hpp'),
            DB::raw('SUM(distribution_items.quantity * items.selling_price) as total_revenue')
        )
            ->join('items', 'distribution_items.item_id', '=', 'items.id')
            ->groupBy('distribution_items.item_id');

        if ($categoryId) {
            $query->where('items.category_id', $categoryId);
        }

        if ($period) {
            $query->whereHas('transaction.schedule', function ($q) use ($period) {
                $q->where('period', $period);
            });
        }

        return $query->get();
    }

    public function getSalesDashboardData(int $month, int $year): array
    {
        $soldSub = DB::table('distribution_items as di')
            ->select('di.item_id', DB::raw('SUM(di.quantity) as total_sold'))
            ->join('distribution_transactions as dt', 'dt.id', '=', 'di.transaction_id')
            ->whereYear('dt.pickup_time', $year)
            ->whereMonth('dt.pickup_time', $month)
            ->groupBy('di.item_id');

        $receiveSub = DB::table('stock_receive_items')
            ->select('item_id',
                DB::raw('SUM(quantity) as total_received'),
                DB::raw('COALESCE(AVG(unit_price), 0) as avg_unit_price')
            )
            ->groupBy('item_id');

        $balanceSub = DB::table('stock_balances')
            ->select('item_id', DB::raw('SUM(quantity) as stock_sum'))
            ->groupBy('item_id');

        $categories = DB::table('item_categories as ic')
            ->select(
                'ic.id', 'ic.label', 'ic.code',
                DB::raw('COALESCE(SUM(di.quantity), 0) as unit_sold'),
                DB::raw('COALESCE(SUM(sb.stock_sum), 0) as stock_avail')
            )
            ->leftJoin('items as i', 'i.category_id', '=', 'ic.id')
            ->leftJoin('distribution_items as di', 'di.item_id', '=', 'i.id')
            ->leftJoin('distribution_transactions as dt', function ($join) use ($month, $year) {
                $join->on('dt.id', '=', 'di.transaction_id')
                    ->whereYear('dt.pickup_time', '=', $year)
                    ->whereMonth('dt.pickup_time', '=', $month);
            })
            ->leftJoinSub($balanceSub, 'sb', 'sb.item_id', '=', 'i.id')
            ->groupBy('ic.id', 'ic.label', 'ic.code')
            ->orderBy('ic.code')
            ->get();

        $revenueItems = DB::table('distribution_items as di')
            ->select(
                'i.id', 'i.name', 'i.code',
                DB::raw('SUM(di.quantity) as unit_sold'),
                DB::raw('SUM(di.quantity * i.selling_price) as revenue')
            )
            ->join('items as i', 'i.id', '=', 'di.item_id')
            ->join('distribution_transactions as dt', function ($join) use ($month, $year) {
                $join->on('dt.id', '=', 'di.transaction_id')
                    ->whereYear('dt.pickup_time', '=', $year)
                    ->whereMonth('dt.pickup_time', '=', $month);
            })
            ->groupBy('i.id', 'i.name', 'i.code')
            ->orderBy('i.name')
            ->get();

        $monthlyRecap = DB::table('distribution_items as di')
            ->select(
                DB::raw('SUM(di.quantity) as unit_sold'),
                DB::raw('SUM(di.quantity * i.selling_price) as total_revenue')
            )
            ->join('items as i', 'i.id', '=', 'di.item_id')
            ->join('distribution_transactions as dt', function ($join) use ($month, $year) {
                $join->on('dt.id', '=', 'di.transaction_id')
                    ->whereYear('dt.pickup_time', '=', $year)
                    ->whereMonth('dt.pickup_time', '=', $month);
            })
            ->first();

        $stockDetails = DB::table('items as i')
            ->select(
                'i.id', 'i.name', 'i.code',
                DB::raw('COALESCE(sb.stock_sum, 0) as available_stock'),
                DB::raw('COALESCE(sb.stock_sum * i.hpp, 0) as stock_value'),
                DB::raw('COALESCE(sri.total_received, 0) as stock_receive'),
                DB::raw('COALESCE(di.total_sold, 0) as sum_sold'),
                DB::raw('COALESCE(di.total_sold * sri.avg_unit_price, 0) as expense'),
                DB::raw('CASE WHEN COALESCE(sri.total_received, 0) > 0 THEN ROUND((COALESCE(di.total_sold, 0) / sri.total_received) * 100, 2) ELSE 0 END as pct_sold')
            )
            ->leftJoinSub($balanceSub, 'sb', 'sb.item_id', '=', 'i.id')
            ->leftJoinSub($receiveSub, 'sri', 'sri.item_id', '=', 'i.id')
            ->leftJoinSub($soldSub, 'di', 'di.item_id', '=', 'i.id')
            ->orderBy('i.name')
            ->get();

        return compact('categories', 'revenueItems', 'monthlyRecap', 'stockDetails');
    }

    public function getMonthlySalesTrend(int $months = 6): Collection
    {
        return DB::table('distribution_items as di')
            ->select(
                DB::raw('YEAR(dt.pickup_time) as year'),
                DB::raw('MONTH(dt.pickup_time) as month'),
                DB::raw('SUM(di.quantity) as unit_sold'),
                DB::raw('SUM(di.quantity * i.selling_price) as revenue')
            )
            ->join('items as i', 'i.id', '=', 'di.item_id')
            ->join('distribution_transactions as dt', 'dt.id', '=', 'di.transaction_id')
            ->whereRaw('dt.pickup_time >= DATE_SUB(NOW(), INTERVAL ? MONTH)', [$months])
            ->groupBy(DB::raw('YEAR(dt.pickup_time)'), DB::raw('MONTH(dt.pickup_time)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    public function getSizeDistributionData(): Collection
    {
        return DB::table('student_size_items')
            ->join('items', 'student_size_items.item_id', '=', 'items.id')
            ->select(
                'items.name as item_name',
                'student_size_items.size',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('items.name', 'student_size_items.size')
            ->orderBy('items.name')
            ->orderBy('student_size_items.size')
            ->get();
    }

    public function getStockCardData(string $itemCode, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $items = Item::where('code', $itemCode)->get();

        if ($items->isEmpty()) {
            return collect();
        }

        $movements = StockMovement::with(['item', 'variant'])
            ->whereIn('item_id', $items->pluck('id'))
            ->orderBy('created_at')
            ->orderBy('id');

        if ($startDate) {
            $movements->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $movements->whereDate('created_at', '<=', $endDate);
        }

        return $movements->get();
    }

    public function getLossReportData(?string $period = null, ?string $category = null): Collection
    {
        $query = StockOpnameItem::select(
            'items.name as item_name',
            'item_categories.label as category_name',
            'item_categories.code as category_code',
            'items.hpp',
            DB::raw('SUM(CASE WHEN stock_opname_items.variance < 0 THEN ABS(stock_opname_items.variance) ELSE 0 END) as qty_loss'),
            DB::raw('SUM(CASE WHEN stock_opname_items.variance > 0 THEN stock_opname_items.variance ELSE 0 END) as qty_surplus')
        )
            ->join('items', 'stock_opname_items.item_id', '=', 'items.id')
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->groupBy('items.id', 'items.name', 'item_categories.label', 'item_categories.code', 'items.hpp')
            ->orderBy('items.name');

        if ($period) {
            $query->whereHas('stockOpname', function ($q) use ($period) {
                $q->where('period', $period);
            });
        }

        if ($category) {
            $query->where('item_categories.code', $category);
        }

        return $query->get();
    }
}
