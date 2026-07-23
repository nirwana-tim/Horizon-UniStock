<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\StudentGeneration;
use App\Models\SizeChangeEvent;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SizeChangeEventController extends Controller
{
    public function index(): View
    {
        $events = SizeChangeEvent::with(['faculty', 'studyProgram', 'generation', 'creator'])
            ->latest()
            ->paginate(15);

        return view('finance.size-events.index', compact('events'));
    }

    public function create(): View
    {
        $faculties = Faculty::orderBy('name')->get();
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();
        $generations = StudentGeneration::orderBy('name')->get();

        return view('finance.size-events.create', compact('faculties', 'studyPrograms', 'generations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'faculty_id' => ['nullable', 'integer', 'exists:faculties,id'],
            'study_program_id' => ['nullable', 'integer', 'exists:study_programs,id'],
            'generation_id' => ['nullable', 'integer', 'exists:student_generations,id'],
            'student_level' => ['nullable', 'string', 'exists:student_levels,kode'],
            'max_changes' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_by'] = auth()->id();

        SizeChangeEvent::create($validated);

        return redirect()->route('distribution.size-events.index')
            ->with('success', 'Event Pengisian / Perubahan Ukuran berhasil dibuat.');
    }

    public function destroy(SizeChangeEvent $sizeEvent): RedirectResponse
    {
        $sizeEvent->delete();

        return redirect()->route('distribution.size-events.index')
            ->with('success', 'Event Pengisian / Perubahan Ukuran berhasil dihapus.');
    }
}
