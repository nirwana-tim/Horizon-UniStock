<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use App\Services\Master\VendorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function __construct(
        protected VendorService $vendorService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = Vendor::withCount('stockReceives');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $vendors = $query->latest()->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('master.vendor._table', compact('vendors'))->render(),
            ]);
        }

        return view('master.vendor.index', compact('vendors'));
    }

    public function create(): View
    {
        return view('master.vendor.create');
    }

    public function store(VendorRequest $request): RedirectResponse
    {
        $this->vendorService->store($request->validated());

        return redirect()->route('master-data.vendor.index')->with('success', 'Vendor berhasil ditambahkan.');
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

        return redirect()->route('master-data.vendor.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        $this->vendorService->destroy($vendor);

        return redirect()->route('master-data.vendor.index')->with('success', 'Vendor berhasil dihapus.');
    }
}
