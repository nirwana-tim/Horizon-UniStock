<?php

namespace App\Services;

use App\Models\DistributionItem;
use App\Models\DistributionTransaction;
use App\Models\Item;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GpmService
{
    public function calculateGpm(?string $period = null): Collection
    {
        $query = DistributionItem::select(
            'item_id',
            DB::raw('SUM(quantity) as qty_sold'),
            DB::raw('SUM(quantity * (SELECT hpp FROM items WHERE items.id = distribution_items.item_id)) as total_hpp'),
            DB::raw('SUM(quantity * (SELECT selling_price FROM items WHERE items.id = distribution_items.item_id)) as total_selling_price')
        )
            ->groupBy('item_id');

        if ($period) {
            $query->whereHas('transaction', function ($q) use ($period) {
                $q->whereHas('schedule', function ($q2) use ($period) {
                    $q2->where('period', $period);
                });
            });
        }

        $results = $query->get();

        return $results->map(function ($item) {
            $itemModel = Item::with('category')->find($item->item_id);
            $labaRugi = $item->total_selling_price - $item->total_hpp;

            return [
                'item_id' => $item->item_id,
                'item_name' => $itemModel->name ?? '-',
                'category_name' => $itemModel->category->name ?? '-',
                'qty_sold' => $item->qty_sold,
                'hpp' => $itemModel->hpp ?? 0,
                'selling_price' => $itemModel->selling_price ?? 0,
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
                DB::raw('SUM(distribution_items.quantity * items.hpp) as total_hpp'),
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
