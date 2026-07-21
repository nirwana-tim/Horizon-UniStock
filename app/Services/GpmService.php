<?php

namespace App\Services;

use App\Models\DistributionItem;
use App\Models\DistributionPeriod;
use App\Models\DistributionTransaction;
use App\Models\Item;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GpmService
{
    public function calculateGpm(?string $period = null): Collection
    {
        if (!$period) {
            $latestPeriod = DistributionPeriod::latest('period')->value('period');
            $period = $latestPeriod ?? now()->format('Y-m');
        }

        $query = DistributionItem::select(
            'distribution_items.item_id',
            'items.code as item_code',
            'items.name as item_name_raw',
            'items.selling_price',
            DB::raw('SUM(distribution_items.quantity) as qty_sold'),
            DB::raw('SUM(distribution_items.quantity * distribution_items.hpp) as total_hpp'),
            DB::raw('SUM(distribution_items.quantity * distribution_items.selling_price_at_distribution) as total_selling_price')
        )
            ->join('items', 'distribution_items.item_id', '=', 'items.id')
            ->groupBy('distribution_items.item_id', 'items.code', 'items.name', 'items.selling_price');

        $query->whereHas('transaction', function ($q) use ($period) {
            $q->whereHas('schedule', function ($q2) use ($period) {
                $q2->where('period', $period);
            });
        });

        $results = $query->get();
        $itemIds = $results->pluck('item_id');
        $categories = Item::with('category')->whereIn('id', $itemIds)->get()->keyBy('id');

        return $results->map(function ($item) use ($categories) {
            $itemModel = $categories->get($item->item_id);
            $labaRugi = $item->total_selling_price - $item->total_hpp;

            $avgHpp = $item->qty_sold > 0 ? round($item->total_hpp / $item->qty_sold, 2) : 0;

            return [
                'item_id' => $item->item_id,
                'item_code' => $item->item_code ?? '',
                'item_name' => $item->item_name_raw ?? '-',
                'category_name' => $itemModel?->category?->name ?? '-',
                'qty_sold' => $item->qty_sold,
                'hpp' => $avgHpp,
                'selling_price' => $item->selling_price ?? 0,
                'total_hpp' => $item->total_hpp,
                'total_selling_price' => $item->total_selling_price,
                'laba_rugi' => $labaRugi,
            ];
        });
    }

    public function getGpmByCategory(?string $period = null): Collection
    {
        $gpmData = $this->calculateGpm($period);

        return $gpmData->groupBy('category_name')->map(function ($items, $category) {
            $totalQty = $items->sum('qty_sold');
            $total_hpp = $items->sum('total_hpp');
            $total_selling = $items->sum('total_selling_price');

            return [
                'category' => $category,
                'total_qty' => $totalQty,
                'total_hpp' => $total_hpp,
                'total_selling_price' => $total_selling,
                'laba_rugi' => $total_selling - $total_hpp,
            ];
        })->values();
    }

    public function getGpmByPeriod(): Collection
    {
        $results = DB::table('distribution_items')
            ->join('distribution_transactions', 'distribution_items.transaction_id', '=', 'distribution_transactions.id')
            ->join('distribution_schedules', 'distribution_transactions.schedule_id', '=', 'distribution_schedules.id')
            ->join('items', 'distribution_items.item_id', '=', 'items.id')
            ->select(
                'distribution_schedules.period',
                DB::raw('SUM(distribution_items.quantity) as qty_sold'),
                DB::raw('SUM(distribution_items.quantity * distribution_items.hpp) as total_hpp'),
                DB::raw('SUM(distribution_items.quantity * items.selling_price) as total_selling_price')
            )
            ->groupBy('distribution_schedules.period')
            ->orderBy('distribution_schedules.period')
            ->get();

        return $results->map(function ($row) {
            return [
                'period' => $row->period,
                'qty_sold' => $row->qty_sold,
                'total_hpp' => $row->total_hpp,
                'total_selling_price' => $row->total_selling_price,
                'laba_rugi' => $row->total_selling_price - $row->total_hpp,
            ];
        });
    }
}
