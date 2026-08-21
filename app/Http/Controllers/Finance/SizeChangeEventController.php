<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\SizeChangeEventRequest;
use App\Models\Faculty;
use App\Models\ItemSize;
use App\Models\SizeChangeEvent;
use App\Models\StudentGeneration;
use App\Models\StudentLevel;
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

        $bajuMasterSizes = $this->flaggedSizeLabels('is_baju');
        $sepatuMasterSizes = $this->flaggedSizeLabels('is_sepatu');

        return view('finance.size-events.create', compact('faculties', 'studyPrograms', 'generations', 'studentLevels', 'bajuMasterSizes', 'sepatuMasterSizes'));
    }

    public function store(SizeChangeEventRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['allow_reedit'] = $request->boolean('allow_reedit', false);
        $validated['created_by'] = auth()->id();

        $validated = $this->applySizeOptions($validated);

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

        $bajuMasterSizes = $this->flaggedSizeLabels('is_baju');
        $sepatuMasterSizes = $this->flaggedSizeLabels('is_sepatu');

        $bajuSelected = $sizeEvent->baju_size_options ?? [];
        $sepatuSelected = $sizeEvent->sepatu_size_options ?? [];

        $bajuCustomText = implode(', ', array_values(array_diff($bajuSelected, $bajuMasterSizes)));
        $sepatuCustomText = implode(', ', array_values(array_diff($sepatuSelected, $sepatuMasterSizes)));

        return view('finance.size-events.edit', compact(
            'sizeEvent', 'faculties', 'studyPrograms', 'generations', 'studentLevels',
            'bajuMasterSizes', 'sepatuMasterSizes', 'bajuSelected', 'sepatuSelected', 'bajuCustomText', 'sepatuCustomText'
        ));
    }

    public function update(SizeChangeEventRequest $request, SizeChangeEvent $sizeEvent): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['allow_reedit'] = $request->boolean('allow_reedit', false);

        $validated = $this->applySizeOptions($validated);

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

    private function applySizeOptions(array $validated): array
    {
        $validated['baju_size_options'] = $this->resolveSizeOptions($validated, 'baju');
        $validated['sepatu_size_options'] = $this->resolveSizeOptions($validated, 'sepatu');

        unset(
            $validated['baju_size_options_custom'],
            $validated['baju_size_options_text'],
            $validated['sepatu_size_options_custom'],
            $validated['sepatu_size_options_text'],
        );

        return $validated;
    }

    private function resolveSizeOptions(array $validated, string $category): ?array
    {
        $optionsKey = $category.'_size_options';
        $customKey = $category.'_size_options_custom';
        $textKey = $category.'_size_options_text';

        $chips = $validated[$optionsKey] ?? null;
        $custom = $validated[$customKey] ?? null;
        $legacy = $validated[$textKey] ?? null;

        if (is_array($chips) || ($custom !== null && trim($custom) !== '')) {
            $merged = array_merge($chips ?? [], $this->parseSizeOptionText($custom) ?? []);
            $merged = array_values(array_unique(array_filter(array_map('trim', $merged))));

            return $merged === [] ? null : $merged;
        }

        return $this->parseSizeOptionText($legacy);
    }

    private function flaggedSizeLabels(string $column): array
    {
        return ItemSize::where($column, true)
            ->orderBy('code')
            ->pluck('label')
            ->unique()
            ->values()
            ->all();
    }

    private function parseSizeOptionText(?string $text): ?array
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        $parts = array_filter(array_map('trim', explode(',', $text)), fn ($value) => $value !== '');

        return array_values($parts);
    }
}
