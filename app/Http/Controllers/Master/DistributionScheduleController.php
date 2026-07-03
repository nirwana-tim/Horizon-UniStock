<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistributionScheduleRequest;
use App\Models\DistributionSchedule;
use App\Models\DistributionStage;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DistributionScheduleController extends Controller
{
    public function index(): View
    {
        $schedules = DistributionSchedule::with('stage.period')->latest()->paginate(15);

        return view('master.distribution-schedule.index', compact('schedules'));
    }

    public function create(): View
    {
        $stages = DistributionStage::with('period')->whereHas('period', fn ($q) => $q->where('is_active', true))->orderBy('stage_order')->get();
        $items = Item::orderBy('name')->get();

        return view('master.distribution-schedule.create', compact('stages', 'items'));
    }

    public function store(DistributionScheduleRequest $request): RedirectResponse
    {
        $schedule = DistributionSchedule::create($request->validated());

        if ($request->has('item_ids')) {
            foreach ($request->item_ids as $itemId) {
                $schedule->items()->create(['item_id' => $itemId]);
            }
        }

        return redirect()->route('master.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil ditambahkan.');
    }

    public function show(DistributionSchedule $distributionSchedule): View
    {
        $distributionSchedule->load(['stage.period', 'items.item', 'transactions.student']);

        return view('master.distribution-schedule.show', compact('distributionSchedule'));
    }

    public function edit(DistributionSchedule $distributionSchedule): View
    {
        $distributionSchedule->load('items');
        $stages = DistributionStage::with('period')->orderBy('stage_order')->get();
        $items = Item::orderBy('name')->get();

        return view('master.distribution-schedule.edit', compact('distributionSchedule', 'stages', 'items'));
    }

    public function update(DistributionScheduleRequest $request, DistributionSchedule $distributionSchedule): RedirectResponse
    {
        $distributionSchedule->update($request->validated());

        $distributionSchedule->items()->delete();

        if ($request->has('item_ids')) {
            foreach ($request->item_ids as $itemId) {
                $distributionSchedule->items()->create(['item_id' => $itemId]);
            }
        }

        return redirect()->route('master.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil diperbarui.');
    }

    public function destroy(DistributionSchedule $distributionSchedule): RedirectResponse
    {
        $distributionSchedule->items()->delete();
        $distributionSchedule->delete();

        return redirect()->route('master.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil dihapus.');
    }
}
