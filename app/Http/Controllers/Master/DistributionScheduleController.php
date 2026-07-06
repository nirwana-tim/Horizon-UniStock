<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistributionScheduleRequest;
use App\Models\DistributionSchedule;
use App\Models\Faculty;
use App\Models\Item;
use App\Models\ProgramLevel;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DistributionScheduleController extends Controller
{
    public function index(): View
    {
        $schedules = DistributionSchedule::with('programLevel', 'faculty', 'studyProgram')->latest()->paginate(15);

        return view('distribution.distribution-schedule.index', compact('schedules'));
    }

    public function create(): View
    {
        $programLevels = ProgramLevel::orderBy('name', 'asc')->get();
        $faculties = Faculty::orderBy('name', 'asc')->get();
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name', 'asc')->get();
        $items = Item::orderBy('name', 'asc')->get();

        $entitlements = \App\Models\Entitlement::with('items')->get();
        $entitlementMap = [];
        foreach ($entitlements as $ent) {
            $entitlementMap[$ent->code] = $ent->items->pluck('item_id')->toArray();
        }
        $levelCodes = $programLevels->pluck('code', 'id')->toArray();
        $facultyCodes = $faculties->pluck('code', 'id')->toArray();
        $prodiCodes = $studyPrograms->pluck('code', 'id')->toArray();

        return view('distribution.distribution-schedule.create', compact(
            'programLevels', 'faculties', 'studyPrograms', 'items',
            'entitlementMap', 'levelCodes', 'facultyCodes', 'prodiCodes'
        ));
    }

    public function store(DistributionScheduleRequest $request): RedirectResponse
    {
        $schedule = DistributionSchedule::create($request->validated());

        if ($request->has('item_ids')) {
            foreach ($request->item_ids as $itemId) {
                $schedule->items()->create(['item_id' => $itemId]);
            }
        }

        return redirect()->route('distribution.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil ditambahkan.');
    }

    public function show(DistributionSchedule $distributionSchedule): View
    {
        $distributionSchedule->load(['programLevel', 'faculty', 'studyProgram', 'items.item', 'transactions.student']);

        return view('distribution.distribution-schedule.show', compact('distributionSchedule'));
    }

    public function edit(DistributionSchedule $distributionSchedule): View
    {
        $distributionSchedule->load('items');
        $programLevels = ProgramLevel::orderBy('name', 'asc')->get();
        $faculties = Faculty::orderBy('name', 'asc')->get();
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name', 'asc')->get();
        $items = Item::orderBy('name', 'asc')->get();

        $entitlements = \App\Models\Entitlement::with('items')->get();
        $entitlementMap = [];
        foreach ($entitlements as $ent) {
            $entitlementMap[$ent->code] = $ent->items->pluck('item_id')->toArray();
        }
        $levelCodes = $programLevels->pluck('code', 'id')->toArray();
        $facultyCodes = $faculties->pluck('code', 'id')->toArray();
        $prodiCodes = $studyPrograms->pluck('code', 'id')->toArray();

        return view('distribution.distribution-schedule.edit', compact(
            'distributionSchedule', 'programLevels', 'faculties', 'studyPrograms', 'items',
            'entitlementMap', 'levelCodes', 'facultyCodes', 'prodiCodes'
        ));
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

        return redirect()->route('distribution.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil diperbarui.');
    }

    public function destroy(DistributionSchedule $distributionSchedule): RedirectResponse
    {
        $distributionSchedule->items()->delete();
        $distributionSchedule->delete();

        return redirect()->route('distribution.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil dihapus.');
    }
}
