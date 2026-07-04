<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntitlementRequest;
use App\Models\Entitlement;
use App\Models\Faculty;
use App\Models\Item;
use App\Models\ProgramLevel;
use App\Models\StudyProgram;
use App\Services\EntitlementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EntitlementController extends Controller
{
    public function __construct(
        private readonly EntitlementService $entitlementService
    ) {}

    public function index(): View
    {
        $entitlements = Entitlement::with(['studyProgram', 'programLevel', 'items.item'])
            ->latest()
            ->paginate(15);

        return view('master.entitlement.index', compact('entitlements'));
    }

    public function create(): View
    {
        $faculties = Faculty::orderBy('name')->get();
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();
        $programLevels = ProgramLevel::orderBy('name')->get();
        $items = Item::with('category', 'variants')->orderBy('name')->get();

        return view('master.entitlement.create', compact('faculties', 'studyPrograms', 'programLevels', 'items'));
    }

    public function store(EntitlementRequest $request): RedirectResponse
    {
        $this->entitlementService->createEntitlement($request->validated());

        return redirect()->route('master.entitlement.index')->with('success', 'Hak barang (entitlement) berhasil ditambahkan.');
    }

    public function show(Entitlement $entitlement): View
    {
        $entitlement->load(['studyProgram', 'programLevel', 'items.item']);

        return view('master.entitlement.show', compact('entitlement'));
    }

    public function edit(Entitlement $entitlement): View
    {
        $entitlement->load('items');
        $faculties = Faculty::orderBy('name')->get();
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();
        $programLevels = ProgramLevel::orderBy('name')->get();
        $items = Item::with('category', 'variants')->orderBy('name')->get();

        return view('master.entitlement.edit', compact('entitlement', 'faculties', 'studyPrograms', 'programLevels', 'items'));
    }

    public function update(EntitlementRequest $request, Entitlement $entitlement): RedirectResponse
    {
        $this->entitlementService->updateEntitlement($entitlement, $request->validated());

        return redirect()->route('master.entitlement.index')->with('success', 'Hak barang (entitlement) berhasil diperbarui.');
    }

    public function destroy(Entitlement $entitlement): RedirectResponse
    {
        $this->entitlementService->deleteEntitlement($entitlement);

        return redirect()->route('master.entitlement.index')->with('success', 'Hak barang (entitlement) berhasil dihapus.');
    }
}
