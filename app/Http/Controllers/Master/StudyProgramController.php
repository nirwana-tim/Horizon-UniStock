<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudyProgramRequest;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Services\Master\StudyProgramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyProgramController extends Controller
{
    public function __construct(
        protected StudyProgramService $studyProgramService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = StudyProgram::with('faculty')->withCount('students');

        if ($search = $request->input('q')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $programs = $query->orderBy('code')->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('master.study-program._table', compact('programs'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $programs])->render(),
            ]);
        }

        return view('master.study-program.index', compact('programs'));
    }

    public function create(): View
    {
        $faculties = Faculty::orderBy('name')->get();

        return view('master.study-program.create', compact('faculties'));
    }

    public function store(StudyProgramRequest $request): RedirectResponse
    {
        $this->studyProgramService->store($request->validated());

        return redirect()->route('master-data.study-program.index')->with('success', 'Program studi berhasil ditambahkan.');
    }

    public function show(StudyProgram $program): View
    {
        $program->load(['faculty']);
        $program->loadCount('students');

        return view('master.study-program.show', compact('program'));
    }

    public function edit(StudyProgram $program): View
    {
        $faculties = Faculty::orderBy('name')->get();

        return view('master.study-program.edit', compact('program', 'faculties'));
    }

    public function update(StudyProgramRequest $request, StudyProgram $program): RedirectResponse
    {
        $this->studyProgramService->update($program, $request->validated());

        return redirect()->route('master-data.study-program.index')->with('success', 'Program studi berhasil diperbarui.');
    }

    public function destroy(StudyProgram $program): RedirectResponse
    {
        $this->studyProgramService->destroy($program);

        return redirect()->route('master-data.study-program.index')->with('success', 'Program studi berhasil dihapus.');
    }
}
