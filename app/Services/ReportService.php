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
            DB::raw('SUM(distribution_items.quantity * distribution_items.hpp) as total_hpp'),
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

    public function getDistributionRecap(?string $period = null, ?int $studyProgramId = null): Collection
    {
        $receivedSub = DB::table('distribution_transactions')
            ->select('students.study_program_id',
                DB::raw('COUNT(DISTINCT distribution_transactions.student_id) as total_received'))
            ->join('students', 'students.id', '=', 'distribution_transactions.student_id')
            ->whereIn('distribution_transactions.status', ['completed', 'partial']);

        if ($period) {
            $receivedSub->whereExists(function ($q) use ($period) {
                $q->select(DB::raw(1))
                    ->from('distribution_schedules')
                    ->whereColumn('distribution_schedules.id', '=', 'distribution_transactions.schedule_id')
                    ->where('distribution_schedules.period', '=', $period);
            });
        }

        $receivedSub->groupBy('students.study_program_id');

        $query = DB::table('study_programs')
            ->select(
                'study_programs.id',
                'study_programs.name as study_program_name',
                DB::raw('COALESCE(eligible.total_eligible, 0) as total_eligible'),
                DB::raw('COALESCE(received.total_received, 0) as total_received'),
                DB::raw('COALESCE(eligible.total_eligible, 0) - COALESCE(received.total_received, 0) as not_received')
            )
            ->leftJoin(DB::raw('(
                SELECT students.study_program_id, COUNT(DISTINCT students.id) as total_eligible
                FROM eligibility_records
                JOIN students ON students.id = eligibility_records.student_id
                WHERE eligibility_records.is_eligible = 1
                GROUP BY students.study_program_id
            ) as eligible'), 'eligible.study_program_id', '=', 'study_programs.id')
            ->leftJoinSub($receivedSub, 'received', 'received.study_program_id', '=', 'study_programs.id')
            ->where(function ($q) {
                $q->whereNotNull('eligible.total_eligible')->orWhereNotNull('received.total_received');
            });

        if ($studyProgramId) {
            $query->where('study_programs.id', $studyProgramId);
        }

        return $query->orderBy('study_programs.name')->get();
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

    public function getSalesDashboardProcessedData(?string $startDate = null, ?string $endDate = null, ?int $categoryId = null, ?int $itemId = null): array
    {
        $start = $startDate ? \Carbon\Carbon::parse($startDate)->startOfDay() : null;
        $end = $endDate ? \Carbon\Carbon::parse($endDate)->endOfDay() : null;

        // 1. KPI Sold Data
        $soldQuery = DB::table('distribution_items as di')
            ->join('distribution_transactions as dt', 'dt.id', '=', 'di.transaction_id')
            ->join('items as i', 'i.id', '=', 'di.item_id')
            ->select('i.category_id', DB::raw('SUM(di.quantity) as total_sold'))
            ->whereIn('dt.status', ['completed', 'partial']);

        if ($start) {
            $soldQuery->where('dt.pickup_time', '>=', $start);
        }
        if ($end) {
            $soldQuery->where('dt.pickup_time', '<=', $end);
        }
        if ($categoryId) {
            $soldQuery->where('i.category_id', $categoryId);
        }
        if ($itemId) {
            $soldQuery->where('di.item_id', $itemId);
        }

        $soldData = $soldQuery->groupBy('i.category_id')->pluck('total_sold', 'i.category_id');

        // 2. KPI Stock Data
        $stockQuery = DB::table('stock_balances as sb')
            ->join('items as i', 'i.id', '=', 'sb.item_id')
            ->select('i.category_id', DB::raw('SUM(sb.quantity) as total_stock'));

        if ($categoryId) {
            $stockQuery->where('i.category_id', $categoryId);
        }
        if ($itemId) {
            $stockQuery->where('sb.item_id', $itemId);
        }

        $stockData = $stockQuery->groupBy('i.category_id')->pluck('total_stock', 'i.category_id');

        // 3. Build KPIs
        $categoriesList = DB::table('item_categories')->orderBy('code')->get();
        $kpis = [];
        $grandTotalSold = 0;
        $grandTotalStock = 0;

        foreach ($categoriesList as $cat) {
            $catId = $cat->id;
            $code = strtolower($cat->code);
            $key = match($code) {
                'unf' => 'uniform',
                'shs' => 'shoes',
                'tmb' => 'tumbler',
                default => $code,
            };
            
            $sold = (int) ($soldData[$catId] ?? 0);
            $stock = (int) ($stockData[$catId] ?? 0);
            
            $kpis[$key] = [
                'sold' => $sold,
                'stock' => $stock
            ];
            
            $grandTotalSold += $sold;
            $grandTotalStock += $stock;
        }

        $kpis['grand_total'] = [
            'sold' => $grandTotalSold,
            'stock' => $grandTotalStock
        ];

        // 4. Chart 1: Unit Sold by Items
        $c1Query = DB::table('distribution_items as di')
            ->join('distribution_transactions as dt', 'dt.id', '=', 'di.transaction_id')
            ->join('items as i', 'i.id', '=', 'di.item_id')
            ->select('i.name', DB::raw('SUM(di.quantity) as total_sold'))
            ->whereIn('dt.status', ['completed', 'partial']);

        if ($start) {
            $c1Query->where('dt.pickup_time', '>=', $start);
        }
        if ($end) {
            $c1Query->where('dt.pickup_time', '<=', $end);
        }
        if ($categoryId) {
            $c1Query->where('i.category_id', $categoryId);
        }
        if ($itemId) {
            $c1Query->where('di.item_id', $itemId);
        }

        $c1Data = $c1Query->groupBy('i.id', 'i.name')
            ->orderByDesc('total_sold')
            ->limit(15)
            ->get();

        $chart1 = [
            'labels' => $c1Data->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Unit Sold',
                    'data' => $c1Data->pluck('total_sold')->map(fn($v) => (int) $v)->toArray(),
                    'backgroundColor' => '#980416',
                    'borderRadius' => 4
                ]
            ]
        ];

        // 5. Chart 2: Revenue by Items
        $c2Query = DB::table('distribution_items as di')
            ->join('distribution_transactions as dt', 'dt.id', '=', 'di.transaction_id')
            ->join('items as i', 'i.id', '=', 'di.item_id')
            ->select('i.name', DB::raw('SUM(di.quantity * i.selling_price) as total_revenue'))
            ->whereIn('dt.status', ['completed', 'partial']);

        if ($start) {
            $c2Query->where('dt.pickup_time', '>=', $start);
        }
        if ($end) {
            $c2Query->where('dt.pickup_time', '<=', $end);
        }
        if ($categoryId) {
            $c2Query->where('i.category_id', $categoryId);
        }
        if ($itemId) {
            $c2Query->where('di.item_id', $itemId);
        }

        $c2Data = $c2Query->groupBy('i.id', 'i.name')
            ->orderByDesc('total_revenue')
            ->limit(15)
            ->get();

        $chart2 = [
            'labels' => $c2Data->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $c2Data->pluck('total_revenue')->map(fn($v) => (int) $v)->toArray(),
                    'backgroundColor' => '#10B981',
                    'borderRadius' => 4
                ]
            ]
        ];

        // 6. Chart 3: Combo Chart (Revenue & Unit Sold by Month)
        $c3Query = DB::table('distribution_items as di')
            ->join('distribution_transactions as dt', 'dt.id', '=', 'di.transaction_id')
            ->join('items as i', 'i.id', '=', 'di.item_id')
            ->select(
                DB::raw("DATE_FORMAT(dt.pickup_time, '%Y-%m') as month_val"),
                DB::raw("DATE_FORMAT(dt.pickup_time, '%b-%y') as month_label"),
                DB::raw('SUM(di.quantity) as total_sold'),
                DB::raw('SUM(di.quantity * i.selling_price) as total_revenue')
            )
            ->whereIn('dt.status', ['completed', 'partial']);

        if ($start) {
            $c3Query->where('dt.pickup_time', '>=', $start);
        }
        if ($end) {
            $c3Query->where('dt.pickup_time', '<=', $end);
        }
        if ($categoryId) {
            $c3Query->where('i.category_id', $categoryId);
        }
        if ($itemId) {
            $c3Query->where('di.item_id', $itemId);
        }

        $c3Data = $c3Query->groupBy('month_val', 'month_label')
            ->orderBy('month_val')
            ->get();

        $chart3 = [
            'labels' => $c3Data->pluck('month_label')->toArray(),
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Revenue',
                    'data' => $c3Data->pluck('total_revenue')->map(fn($v) => (int) $v)->toArray(),
                    'backgroundColor' => '#980416',
                    'yAxisID' => 'y',
                    'borderRadius' => 4
                ],
                [
                    'type' => 'line',
                    'label' => 'Unit Sold',
                    'data' => $c3Data->pluck('total_sold')->map(fn($v) => (int) $v)->toArray(),
                    'borderColor' => '#2563EB',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                    'yAxisID' => 'y1',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];

        // 7. Chart 4: Available Stock
        $c4Query = DB::table('stock_balances as sb')
            ->join('items as i', 'i.id', '=', 'sb.item_id')
            ->select('i.name', DB::raw('SUM(sb.quantity) as total_stock'));

        if ($categoryId) {
            $c4Query->where('i.category_id', $categoryId);
        }
        if ($itemId) {
            $c4Query->where('sb.item_id', $itemId);
        }

        $c4Data = $c4Query->groupBy('i.id', 'i.name')
            ->orderByDesc('total_stock')
            ->limit(15)
            ->get();

        $chart4 = [
            'labels' => $c4Data->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Available Stock',
                    'data' => $c4Data->pluck('total_stock')->map(fn($v) => (int) $v)->toArray(),
                    'backgroundColor' => '#F59E0B',
                    'borderRadius' => 4
                ]
            ]
        ];

        // 8. Chart 5: Value Stock
        $c5Query = DB::table('stock_balances as sb')
            ->join('items as i', 'i.id', '=', 'sb.item_id')
            ->select('i.name', DB::raw('SUM(sb.quantity * CASE WHEN i.hpp > 0 THEN i.hpp ELSE i.selling_price * 0.7 END) as total_value'));

        if ($categoryId) {
            $c5Query->where('i.category_id', $categoryId);
        }
        if ($itemId) {
            $c5Query->where('sb.item_id', $itemId);
        }

        $c5Data = $c5Query->groupBy('i.id', 'i.name')
            ->orderByDesc('total_value')
            ->limit(15)
            ->get();

        $chart5 = [
            'labels' => $c5Data->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Value Stock (Rp)',
                    'data' => $c5Data->pluck('total_value')->map(fn($v) => (int) $v)->toArray(),
                    'backgroundColor' => '#3B82F6',
                    'borderRadius' => 4
                ]
            ]
        ];

        // 9. Chart 6: % Unit Sold
        $c6Query = DB::table('distribution_items as di')
            ->join('distribution_transactions as dt', 'dt.id', '=', 'di.transaction_id')
            ->join('items as i', 'i.id', '=', 'di.item_id')
            ->select('i.name', DB::raw('SUM(di.quantity) as total_sold'))
            ->whereIn('dt.status', ['completed', 'partial']);

        if ($start) {
            $c6Query->where('dt.pickup_time', '>=', $start);
        }
        if ($end) {
            $c6Query->where('dt.pickup_time', '<=', $end);
        }
        if ($categoryId) {
            $c6Query->where('i.category_id', $categoryId);
        }
        if ($itemId) {
            $c6Query->where('di.item_id', $itemId);
        }

        $c6Data = $c6Query->groupBy('i.id', 'i.name')
            ->orderByDesc('total_sold')
            ->limit(8)
            ->get();

        $colors = [
            '#980416', // Maroon
            '#3B82F6', // Blue
            '#10B981', // Green
            '#F59E0B', // Amber
            '#EC4899', // Pink
            '#8B5CF6', // Purple
            '#06B6D4', // Cyan
            '#6B7280', // Gray
        ];

        $chart6 = [
            'labels' => $c6Data->pluck('name')->toArray(),
            'datasets' => [
                [
                    'data' => $c6Data->pluck('total_sold')->map(fn($v) => (int) $v)->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $c6Data->count()),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff'
                ]
            ]
        ];

        return [
            'kpis' => $kpis,
            'chart1' => $chart1,
            'chart2' => $chart2,
            'chart3' => $chart3,
            'chart4' => $chart4,
            'chart5' => $chart5,
            'chart6' => $chart6,
        ];
    }
}
