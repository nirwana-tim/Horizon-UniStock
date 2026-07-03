<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use App\Services\Master\VendorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function __construct(
        protected VendorService $vendorService
    ) {}

    public function index(): View
    {
        $vendors = Vendor::withCount('stockReceives')->latest()->paginate(15);

        return view('master.vendor.index', compact('vendors'));
    }

    public function create(): View
    {
        return view('master.vendor.create');
    }

    public function store(VendorRequest $request): RedirectResponse
    {
        $this->vendorService->store($request->validated());

        return redirect()->route('master.vendor.index')->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function show(Vendor $vendor): View
    {
        $vendor->load('stockReceives');

        return view('master.vendor.show', compact('vendor'));
    }

    public function edit(Vendor $vendor): View
    {
        return view('master.vendor.edit', compact('vendor'));
    }

    public function update(VendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $this->vendorService->update($vendor, $request->validated());

        return redirect()->route('master.vendor.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        $this->vendorService->destroy($vendor);

        return redirect()->route('master.vendor.index')->with('success', 'Vendor berhasil dihapus.');
    }
}
