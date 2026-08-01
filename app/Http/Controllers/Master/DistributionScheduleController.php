<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistributionScheduleRequest;
use App\Models\DistributionSchedule;
use App\Services\DistributionScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistributionScheduleController extends Controller
{
    public function __construct(
        protected DistributionScheduleService $scheduleService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $schedules = $this->scheduleService->getFilteredSchedules(
            $request->input('q'),
            $request->input('period'),
            $request->integer('faculty_id') ?: null,
            $request->integer('study_program_id') ?: null,
        );

        if ($request->ajax()) {
            $html = view('distribution.distribution-schedule._table', compact('schedules'))->render();
            $pagination = view('components.alpine-pagination', ['paginator' => $schedules])->render();

            return response()->json(compact('html', 'pagination'));
        }

        $options = $this->scheduleService->getFormOptions();

        return view('distribution.distribution-schedule.index', array_merge(
            compact('schedules'),
            $options
        ));
    }

    public function create(): View
    {
        $options = $this->scheduleService->getFormOptions();

        return view('distribution.distribution-schedule.create', [
            'faculties' => $options['faculties'],
            'studyPrograms' => $options['studyPrograms'],
        ]);
    }

    public function fetchItems(Request $request): JsonResponse
    {
        $studyProgramId = $request->input('study_program_id') === 'all' ? -1 : $request->integer('study_program_id');
        $checkedIds = $request->has('checked_ids') ? explode(',', $request->checked_ids) : [];

        [$items, $checkedIds] = $this->scheduleService->fetchItems(
            $studyProgramId,
            $request->integer('faculty_id') ?: null,
            $request->input('student_level'),
            $checkedIds
        );

        $html = view('distribution.distribution-schedule._items', compact('items', 'checkedIds'))->render();

        return response()->json(compact('html'));
    }

    public function store(DistributionScheduleRequest $request): RedirectResponse
    {
        $this->scheduleService->store($request->validated());

        return redirect()->route('distribution.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil ditambahkan.');
    }

    public function show(DistributionSchedule $distributionSchedule): View
    {
        $distributionSchedule->load(['faculty', 'studyProgram', 'items.item', 'studentLevel']);

        return view('distribution.distribution-schedule.show', compact('distributionSchedule'));
    }

    public function transactions(DistributionSchedule $distributionSchedule, Request $request): View|JsonResponse
    {
        $query = $distributionSchedule->transactions()->with('student', 'items.item');

        if ($search = $request->input('q')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->whereHas('student', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"));
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
        $faculties = $this->scheduleService->getFormOptions()['faculties'];
        $studyPrograms = $this->scheduleService->getFormOptions()['studyPrograms'];

        return view('distribution.distribution-schedule.edit', compact(
            'distributionSchedule', 'faculties', 'studyPrograms'
        ));
    }

    public function update(DistributionScheduleRequest $request, DistributionSchedule $distributionSchedule): RedirectResponse
    {
        $this->scheduleService->update($distributionSchedule, $request->validated());

        return redirect()->route('distribution.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil diperbarui.');
    }

    public function destroy(DistributionSchedule $distributionSchedule): RedirectResponse
    {
        $this->scheduleService->destroy($distributionSchedule);

        return redirect()->route('distribution.distribution-schedule.index')->with('success', 'Jadwal distribusi berhasil dihapus.');
    }
}
