<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistributionScheduleRequest;
use App\Models\DistributionSchedule;
use App\Models\Entitlement;
use App\Models\Faculty;
use App\Models\Item;
use App\Models\ProgramLevel;
use App\Models\StudyProgram;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistributionScheduleController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $schedules = DistributionSchedule::with('programLevel', 'faculty', 'studyProgram')
            ->when($request->input('q'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20);

        if ($request->ajax()) {
            $html = view('distribution.distribution-schedule._table', compact('schedules'))->render();
            return response()->json(compact('html'));
        }

        return view('distribution.distribution-schedule.index', compact('schedules'));
    }

    public function create(): View
    {
        $programLevels = ProgramLevel::orderBy('name', 'asc')->get();
        $faculties = Faculty::orderBy('name', 'asc')->get();
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name', 'asc')->get();

        return view('distribution.distribution-schedule.create', compact(
            'programLevels', 'faculties', 'studyPrograms'
        ));
    }

    public function fetchItems(Request $request): JsonResponse
    {
        $studyProgramId = $request->study_program_id;

        if ($studyProgramId === 'all') {
            $items = Item::orderBy('name')->get();
        } elseif ($studyProgramId && $request->program_level_id && $request->faculty_id) {
            $levelCode = ProgramLevel::find($request->program_level_id)?->code ?? '';
            $facultyCode = Faculty::find($request->faculty_id)?->code ?? '';
            $prodiCode = StudyProgram::find($studyProgramId)?->code ?? '';
            $entitlement = Entitlement::with('items')
                ->where('code', $levelCode . $facultyCode . $prodiCode)
                ->first();
            $allowedIds = $entitlement?->items->pluck('item_id')->toArray() ?? [];
            $items = $allowedIds ? Item::whereIn('id', $allowedIds)->orderBy('name')->get() : collect();
        } else {
            $items = collect();
        }

        $checkedIds = $request->has('checked_ids') ? explode(',', $request->checked_ids) : [];

        $html = view('distribution.distribution-schedule._items', compact('items', 'checkedIds'))->render();

        return response()->json(compact('html'));
    }

    public function store(DistributionScheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (($data['study_program_id'] ?? null) === 'all') {
            $data['study_program_id'] = null;
        }
        $schedule = DistributionSchedule::create($data);

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

        return view('distribution.distribution-schedule.edit', compact(
            'distributionSchedule', 'programLevels', 'faculties', 'studyPrograms'
        ));
    }

    public function update(DistributionScheduleRequest $request, DistributionSchedule $distributionSchedule): RedirectResponse
    {
        $data = $request->validated();
        if (($data['study_program_id'] ?? null) === 'all') {
            $data['study_program_id'] = null;
        }
        $distributionSchedule->update($data);

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
