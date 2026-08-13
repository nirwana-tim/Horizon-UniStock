<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\StudentGeneration;
use App\Models\StudentLevel;
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
        $studentLevels = StudentLevel::orderBy('kode')->get();

        return view('finance.size-events.create', compact('faculties', 'studyPrograms', 'generations', 'studentLevels'));
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
            'allow_reedit' => ['boolean'],
            'baju_size_options_text' => ['nullable', 'string'],
            'sepatu_size_options_text' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['allow_reedit'] = $request->boolean('allow_reedit', false);
        $validated['created_by'] = auth()->id();

        // Parse comma-separated size options into JSON arrays
        if (! empty($validated['baju_size_options_text'])) {
            $validated['baju_size_options'] = array_map('trim', explode(',', $validated['baju_size_options_text']));
        }
        if (! empty($validated['sepatu_size_options_text'])) {
            $validated['sepatu_size_options'] = array_map('trim', explode(',', $validated['sepatu_size_options_text']));
        }

        unset($validated['baju_size_options_text'], $validated['sepatu_size_options_text']);

        SizeChangeEvent::create($validated);

        return redirect()->route('distribution.size-events.index')
            ->with('success', 'Event Pengisian / Perubahan Ukuran berhasil dibuat.');
    }

    public function edit(SizeChangeEvent $sizeEvent): View
    {
        $faculties = Faculty::orderBy('name')->get();
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();
        $generations = StudentGeneration::orderBy('name')->get();
        $studentLevels = StudentLevel::orderBy('kode')->get();

        $bajuOptionsText = $sizeEvent->baju_size_options ? implode(', ', $sizeEvent->baju_size_options) : 'XS, S, M, L, XL, XXL, XXXL, XXXXL, XXXXXL, XXXXXXL';
        $sepatuOptionsText = $sizeEvent->sepatu_size_options ? implode(', ', $sizeEvent->sepatu_size_options) : '38, 39, 40, 41, 42, 43, 44, 45';

        return view('finance.size-events.edit', compact('sizeEvent', 'faculties', 'studyPrograms', 'generations', 'studentLevels', 'bajuOptionsText', 'sepatuOptionsText'));
    }

    public function update(Request $request, SizeChangeEvent $sizeEvent): RedirectResponse
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
            'allow_reedit' => ['boolean'],
            'baju_size_options_text' => ['nullable', 'string'],
            'sepatu_size_options_text' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['allow_reedit'] = $request->boolean('allow_reedit', false);

        // Parse comma-separated size options into JSON arrays
        if (! empty($validated['baju_size_options_text'])) {
            $validated['baju_size_options'] = array_map('trim', explode(',', $validated['baju_size_options_text']));
        }
        if (! empty($validated['sepatu_size_options_text'])) {
            $validated['sepatu_size_options'] = array_map('trim', explode(',', $validated['sepatu_size_options_text']));
        }

        unset($validated['baju_size_options_text'], $validated['sepatu_size_options_text']);

        $sizeEvent->update($validated);

        return redirect()->route('distribution.size-events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(SizeChangeEvent $sizeEvent): RedirectResponse
    {
        $sizeEvent->delete();

        return redirect()->route('distribution.size-events.index')
            ->with('success', 'Event Pengisian / Perubahan Ukuran berhasil dihapus.');
    }
}
