<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistributionStageRequest;
use App\Models\DistributionPeriod;
use App\Models\DistributionStage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DistributionStageController extends Controller
{
    public function index(): View
    {
        $stages = DistributionStage::with('period')->latest()->paginate(15);

        return view('master.distribution-stage.index', compact('stages'));
    }

    public function create(): View
    {
        $periods = DistributionPeriod::where('is_active', true)->orderBy('name')->get();

        return view('master.distribution-stage.create', compact('periods'));
    }

    public function store(DistributionStageRequest $request): RedirectResponse
    {
        DistributionStage::create($request->validated());

        return redirect()->route('master.distribution-stage.index')->with('success', 'Tahap distribusi berhasil ditambahkan.');
    }

    public function show(DistributionStage $distributionStage): View
    {
        $distributionStage->load(['period', 'schedules']);

        return view('master.distribution-stage.show', compact('distributionStage'));
    }

    public function edit(DistributionStage $distributionStage): View
    {
        $periods = DistributionPeriod::orderBy('name')->get();

        return view('master.distribution-stage.edit', compact('distributionStage', 'periods'));
    }

    public function update(DistributionStageRequest $request, DistributionStage $distributionStage): RedirectResponse
    {
        $distributionStage->update($request->validated());

        return redirect()->route('master.distribution-stage.index')->with('success', 'Tahap distribusi berhasil diperbarui.');
    }

    public function destroy(DistributionStage $distributionStage): RedirectResponse
    {
        $distributionStage->delete();

        return redirect()->route('master.distribution-stage.index')->with('success', 'Tahap distribusi berhasil dihapus.');
    }
}
