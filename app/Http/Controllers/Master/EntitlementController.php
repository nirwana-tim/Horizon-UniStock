<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntitlementRequest;
use App\Models\Entitlement;
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
        $entitlements = Entitlement::with('items.item')
            ->latest()
            ->paginate(15);

        return view('distribution.entitlement.index', compact('entitlements'));
    }

    public function create(): View
    {
        $items = $this->getGroupedItems();
        $programLevels = ProgramLevel::orderBy('code', 'asc')->get(['*']);
        $studyPrograms = StudyProgram::with(['faculty'])->orderBy('name', 'asc')->get(['*']);

        return view('distribution.entitlement.create', compact('items', 'programLevels', 'studyPrograms'));
    }

    public function store(EntitlementRequest $request): RedirectResponse
    {
        $this->entitlementService->createEntitlement($request->validated());

        return redirect()->route('distribution.entitlement.index')->with('success', 'Entitlement berhasil ditambahkan.');
    }

    public function show(Entitlement $entitlement): View
    {
        $entitlement->load('items.item');

        return view('distribution.entitlement.show', compact('entitlement'));
    }

    public function edit(Entitlement $entitlement): View
    {
        $entitlement->load('items');
        $items = $this->getGroupedItems();
        $programLevels = ProgramLevel::orderBy('code', 'asc')->get(['*']);
        $studyPrograms = StudyProgram::with(['faculty'])->orderBy('name', 'asc')->get(['*']);

        return view('distribution.entitlement.edit', compact('entitlement', 'items', 'programLevels', 'studyPrograms'));
    }

    public function update(EntitlementRequest $request, Entitlement $entitlement): RedirectResponse
    {
        $this->entitlementService->updateEntitlement($entitlement, $request->validated());

        return redirect()->route('distribution.entitlement.index')->with('success', 'Entitlement berhasil diperbarui.');
    }

    public function destroy(Entitlement $entitlement): RedirectResponse
    {
        $this->entitlementService->deleteEntitlement($entitlement);

        return redirect()->route('distribution.entitlement.index')->with('success', 'Entitlement berhasil dihapus.');
    }

    /**
     * Get items grouped by base_code (product level, not size level).
     * Returns one representative item per product group with size info.
     */
    private function getGroupedItems(): \Illuminate\Support\Collection
    {
        return Item::whereNotNull('base_code')
            ->select('base_code')
            ->distinct()
            ->orderBy('base_code', 'asc')
            ->get()
            ->map(function ($row) {
                $rep = Item::where('base_code', '=', $row->base_code, 'and')
                    ->with(['category', 'variants'])
                    ->first();

                return (object) [
                    'id' => $rep->id,
                    'name' => $rep->name,
                    'code' => $row->base_code,
                    'gender' => $rep->gender,
                    'category' => $rep->category,
                    'sizes' => Item::where('base_code', '=', $row->base_code, 'and')
                        ->pluck('code', 'id')
                        ->map(fn($code, $id) => [
                            'id' => $id,
                            'code' => $code,
                            'label' => Item::find($id, ['*'])->variants->first()->size_label ?? $code,
                        ]),
                ];
            });
    }
}
