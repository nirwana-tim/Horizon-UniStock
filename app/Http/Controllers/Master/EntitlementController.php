<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntitlementRequest;
use App\Models\Entitlement;
use App\Models\Item;
use App\Models\ProgramLevel;
use App\Models\StudyProgram;
use App\Services\EntitlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EntitlementController extends Controller
{
    public function __construct(
        private readonly EntitlementService $entitlementService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $entitlements = Entitlement::with('items.item')
            ->when($request->input('q'), function ($query, $search) {
                $search = str_replace(['%', '_'], ['\%', '\_'], $search);
                $query->where('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20);

        if ($request->ajax()) {
            $html = view('distribution.entitlement._table', compact('entitlements'))->render();
            $pagination = view('components.alpine-pagination', ['paginator' => $entitlements])->render();
            return response()->json(compact('html', 'pagination'));
        }

        return view('distribution.entitlement.index', compact('entitlements'));
    }

    public function create(): View
    {
        $programLevels = ProgramLevel::orderBy('code', 'asc')->get(['*']);
        $studyPrograms = StudyProgram::with(['faculty'])->orderBy('name', 'asc')->get(['*']);

        return view('distribution.entitlement.create', compact('programLevels', 'studyPrograms'));
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
        $programLevels = ProgramLevel::orderBy('code', 'asc')->get(['*']);
        $studyPrograms = StudyProgram::with(['faculty'])->orderBy('name', 'asc')->get(['*']);

        return view('distribution.entitlement.edit', compact('entitlement', 'programLevels', 'studyPrograms'));
    }

    public function update(EntitlementRequest $request, Entitlement $entitlement): RedirectResponse
    {
        $this->entitlementService->updateEntitlement($entitlement, $request->validated());

        return redirect()->route('distribution.entitlement.index')->with('success', 'Entitlement berhasil diperbarui.');
    }

    public function itemsGrid(Request $request): View
    {
        $items = $this->getGroupedItems();
        $entitlement = null;

        if ($entitlementId = $request->input('entitlement_id')) {
            $entitlement = Entitlement::with('items')->findOrFail($entitlementId);
            $this->authorize('view', $entitlement);
        }

        return view('distribution.entitlement._items-grid', compact('items', 'entitlement'));
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
                            'label' => Item::find($id)?->variants?->first()?->size_label ?? $code,
                        ]),
                ];
            });
    }
}
