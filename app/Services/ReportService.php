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
