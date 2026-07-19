<?php

namespace App\Http\Controllers\Distribution;

use App\Http\Controllers\Controller;
use App\Models\DistributionStage;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistributionStageController extends Controller
{
    public function index(): View
    {
        $stages = DistributionStage::orderBy('stage_order')->paginate(10);

        return view('distribution.stage.index', compact('stages'));
    }

    public function create(): View
    {
        return view('distribution.stage.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stage_order' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $stage = DistributionStage::create($validated);

        AuditService::log('create', 'distribution_stage', $stage->id, null, $stage->toArray());

        return redirect()->route('distribution.stages.index')
            ->with('success', 'Tahap distribusi berhasil dibuat.');
    }

    public function show(DistributionStage $stage): View
    {
        $stage->loadCount('schedules', 'transactions');

        return view('distribution.stage.show', compact('stage'));
    }

    public function edit(DistributionStage $stage): View
    {
        return view('distribution.stage.edit', compact('stage'));
    }

    public function update(Request $request, DistributionStage $stage): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stage_order' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $old = $stage->toArray();
        $stage->update($validated);

        AuditService::log('update', 'distribution_stage', $stage->id, $old, $stage->fresh()->toArray());

        return redirect()->route('distribution.stages.index')
            ->with('success', 'Tahap distribusi berhasil diperbarui.');
    }

    public function destroy(DistributionStage $stage): RedirectResponse
    {
        if ($stage->schedules()->exists()) {
            return redirect()->route('distribution.stages.index')
                ->with('error', 'Tidak dapat menghapus tahap yang masih memiliki jadwal distribusi.');
        }

        AuditService::log('delete', 'distribution_stage', $stage->id, $stage->toArray(), null);
        $stage->delete();

        return redirect()->route('distribution.stages.index')
            ->with('success', 'Tahap distribusi berhasil dihapus.');
    }
}
