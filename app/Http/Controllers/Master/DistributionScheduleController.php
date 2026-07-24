<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistributionScheduleRequest;
use App\Models\DistributionSchedule;
use App\Models\Entitlement;
use App\Models\Faculty;
use App\Models\Item;
use App\Models\StudyProgram;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistributionScheduleController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $schedules = DistributionSchedule::with('faculty', 'studyProgram', 'studentLevel')
            ->when($request->input('q'), function ($query, $search) {
                $search = str_replace(['%', '_'], ['\%', '\_'], $search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($request->input('period'), fn ($query, $p) => $query->where('period', $p))
            ->when($request->input('faculty_id'), fn ($query, $f) => $query->where('faculty_id', $f))
            ->when($request->input('study_program_id'), fn ($query, $s) => $query->where('study_program_id', $s))
            ->latest()
            ->paginate(20);

        if ($request->ajax()) {
            $html = view('distribution.distribution-schedule._table', compact('schedules'))->render();
            $pagination = view('components.alpine-pagination', ['paginator' => $schedules])->render();

            return response()->json(compact('html', 'pagination'));
        }

        $periods = cache()->remember('schedule-periods', 3600, fn () =>
            DistributionSchedule::whereNotNull('period')
                ->distinct()
                ->orderBy('period', 'desc')
                ->pluck('period')
                ->map(fn ($p) => (string) $p)
                ->values()
                ->toArray()
        );
        $faculties = cache()->remember('faculties-all', 3600, fn () =>
            Faculty::orderBy('name')->get()
        );
        $studyPrograms = cache()->remember('study-programs-faculty', 3600, fn () =>
            StudyProgram::with('faculty')->orderBy('name')->get()
        );

        return view('distribution.distribution-schedule.index', compact('schedules', 'periods', 'faculties', 'studyPrograms'));
    }

    public function create(): View
    {
        $faculties = Faculty::orderBy('name', 'asc')->get();
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name', 'asc')->get();

        return view('distribution.distribution-schedule.create', compact(
            'faculties', 'studyPrograms'
        ));
    }

    public function fetchItems(Request $request): JsonResponse
    {
        $studyProgramId = $request->study_program_id;
        $studentLevel = $request->input('student_level');
 
        if ($studyProgramId === 'all') {
            $items = Item::orderBy('name')->get();
        } elseif ($studyProgramId && $request->faculty_id) {
            $facultyCode = Faculty::find($request->faculty_id)?->code ?? '';
            $prodiCode = StudyProgram::find($studyProgramId)?->code ?? '';
            $entitlement = Entitlement::with('items')
                ->where('code', $facultyCode.$prodiCode)
                ->when($studentLevel, fn ($query) => $query->where('student_level', $studentLevel))
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
        $itemIds = $data['item_ids'] ?? [];
        unset($data['item_ids']);

        if (($data['study_program_id'] ?? null) === 'all') {
            $data['study_program_id'] = null;
        }

        $schedule = DistributionSchedule::create($data);

        if ($itemIds) {
            foreach ($itemIds as $itemId) {
                $schedule->items()->create(['item_id' => $itemId]);
            }
        }

        AuditService::log(
            'create',
            DistributionSchedule::class,
            $schedule->id,
            null,
            array_merge($schedule->fresh()->toArray(), ['item_ids' => $itemIds])
        );

        return redirect()->route('distribution.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil ditambahkan.');
    }

    public function show(DistributionSchedule $distributionSchedule): View
    {
        $distributionSchedule->load(['faculty', 'studyProgram', 'items.item', 'studentLevel']);

        return view('distribution.distribution-schedule.show', compact('distributionSchedule'));
    }

    public function transactions(DistributionSchedule $distributionSchedule, Request $request): View|JsonResponse
    {
        $query = $distributionSchedule->transactions()->with('student');

        if ($search = $request->input('q')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->whereHas('student', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"));
        }

        $transactions = $query->latest('pickup_time')->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('distribution.distribution-schedule._transactions', compact('transactions'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $transactions])->render(),
            ]);
        }

        return view('distribution.distribution-schedule._transactions', compact('transactions'));
    }

    public function edit(DistributionSchedule $distributionSchedule): View
    {
        $distributionSchedule->load('items');
        $faculties = Faculty::orderBy('name', 'asc')->get();
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name', 'asc')->get();

        return view('distribution.distribution-schedule.edit', compact(
            'distributionSchedule', 'faculties', 'studyPrograms'
        ));
    }

    public function update(DistributionScheduleRequest $request, DistributionSchedule $distributionSchedule): RedirectResponse
    {
        $data = $request->validated();
        $itemIds = $data['item_ids'] ?? [];
        unset($data['item_ids']);

        if (($data['study_program_id'] ?? null) === 'all') {
            $data['study_program_id'] = null;
        }

        $oldValues = array_merge($distributionSchedule->toArray(), [
            'item_ids' => $distributionSchedule->items()->pluck('item_id')->toArray(),
        ]);

        $distributionSchedule->update($data);

        $distributionSchedule->items()->delete();

        if ($itemIds) {
            foreach ($itemIds as $itemId) {
                $distributionSchedule->items()->create(['item_id' => $itemId]);
            }
        }

        AuditService::log(
            'update',
            DistributionSchedule::class,
            $distributionSchedule->id,
            $oldValues,
            array_merge($distributionSchedule->fresh()->toArray(), ['item_ids' => $itemIds])
        );

        return redirect()->route('distribution.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil diperbarui.');
    }

    public function destroy(DistributionSchedule $distributionSchedule): RedirectResponse
    {
        $oldValues = array_merge($distributionSchedule->toArray(), [
            'item_ids' => $distributionSchedule->items()->pluck('item_id')->toArray(),
        ]);

        $distributionSchedule->items()->delete();
        $distributionSchedule->delete();

        AuditService::log('delete', DistributionSchedule::class, $distributionSchedule->id, $oldValues, null);

        return redirect()->route('distribution.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil dihapus.');
    }
}
