<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\SizeChangeEventRequest;
use App\Models\Faculty;
use App\Models\StudentGeneration;
use App\Models\StudentLevel;
use App\Models\SizeChangeEvent;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
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

    public function store(SizeChangeEventRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['allow_reedit'] = $request->boolean('allow_reedit', false);
        $validated['created_by'] = auth()->id();

        $validated = $this->parseSizeOptions($validated);

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

    public function update(SizeChangeEventRequest $request, SizeChangeEvent $sizeEvent): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['allow_reedit'] = $request->boolean('allow_reedit', false);

        $validated = $this->parseSizeOptions($validated);

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

    private function parseSizeOptions(array $validated): array
    {
        if (! empty($validated['baju_size_options_text'])) {
            $validated['baju_size_options'] = array_map('trim', explode(',', $validated['baju_size_options_text']));
        }
        if (! empty($validated['sepatu_size_options_text'])) {
            $validated['sepatu_size_options'] = array_map('trim', explode(',', $validated['sepatu_size_options_text']));
        }

        unset($validated['baju_size_options_text'], $validated['sepatu_size_options_text']);

        return $validated;
    }
}
