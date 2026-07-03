<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistributionPeriodRequest;
use App\Models\DistributionPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DistributionPeriodController extends Controller
{
    public function index(): View
    {
        $periods = DistributionPeriod::withCount('distributionStages', 'entitlements')->latest()->paginate(15);

        return view('master.distribution-period.index', compact('periods'));
    }

    public function create(): View
    {
        return view('master.distribution-period.create');
    }

    public function store(DistributionPeriodRequest $request): RedirectResponse
    {
        DistributionPeriod::create($request->validated());

        return redirect()->route('master.distribution-period.index')->with('success', 'Periode distribusi berhasil ditambahkan.');
    }

    public function show(DistributionPeriod $distributionPeriod): View
    {
        $distributionPeriod->load(['distributionStages', 'entitlements']);

        return view('master.distribution-period.show', compact('distributionPeriod'));
    }

    public function edit(DistributionPeriod $distributionPeriod): View
    {
        return view('master.distribution-period.edit', compact('distributionPeriod'));
    }

    public function update(DistributionPeriodRequest $request, DistributionPeriod $distributionPeriod): RedirectResponse
    {
        $distributionPeriod->update($request->validated());

        return redirect()->route('master.distribution-period.index')->with('success', 'Periode distribusi berhasil diperbarui.');
    }

    public function destroy(DistributionPeriod $distributionPeriod): RedirectResponse
    {
        $distributionPeriod->delete();

        return redirect()->route('master.distribution-period.index')->with('success', 'Periode distribusi berhasil dihapus.');
    }
}
