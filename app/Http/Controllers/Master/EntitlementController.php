<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntitlementRequest;
use App\Models\Entitlement;
use App\Models\Faculty;
use App\Models\Item;
use App\Models\StudyProgram;
use App\Services\EntitlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EntitlementController extends Controller
{
    public function __construct(
        private readonly EntitlementService $entitlementService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $facultyId = $request->integer('faculty_id') ?: null;
        $faculty = $facultyId ? Faculty::find($facultyId) : null;
        $facultyCode = $faculty?->code;

        $entitlements = Entitlement::with(['items.item', 'studentLevel'])
            ->when($request->input('q'), function ($query, $search) {
                $search = $this->escapeLike($search);
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($facultyCode, fn ($q) => $q->where('code', 'like', "%{$facultyCode}%"))
            ->when($request->filled('is_active'), fn ($q, $v) => $q->where('is_active', $v === '1'))
            ->latest()
            ->paginate(20);

        if ($request->ajax()) {
            $html = view('distribution.entitlement._table', compact('entitlements'))->render();
            $pagination = view('components.alpine-pagination', ['paginator' => $entitlements])->render();

            return response()->json(compact('html', 'pagination'));
        }

        $faculties = Faculty::orderBy('name')->get();

        return view('distribution.entitlement.index', compact('entitlements', 'faculties'));
    }

    public function create(): View
    {
        $studyPrograms = StudyProgram::with(['faculty'])->orderBy('name', 'asc')->get(['*']);

        return view('distribution.entitlement.create', compact('studyPrograms'));
    }

    public function store(EntitlementRequest $request): RedirectResponse
    {
        $this->entitlementService->createEntitlement($request->validated());

        return redirect()->route('distribution.entitlement.index')->with('success', 'Entitlement berhasil ditambahkan.');
    }

    public function show(Entitlement $entitlement): View
    {
        $entitlement->load(['items.item', 'studentLevel']);

        $baseCodes = $entitlement->items
            ->map(fn ($ei) => $ei->item?->base_code ?? $ei->item?->code)
            ->filter()
            ->unique()
            ->values();

        $availableSizes = Item::whereIn('base_code', $baseCodes)
            ->orWhereIn('code', $baseCodes)
            ->with('variants')
            ->get()
            ->groupBy('base_code')
            ->map(fn ($items) => $items->pluck('variants.0.size_label')->filter()->implode(', '))
            ->all();

        return view('distribution.entitlement.show', compact('entitlement', 'availableSizes'));
    }

    public function edit(Entitlement $entitlement): View
    {
        $entitlement->load('items');
        $studyPrograms = StudyProgram::with(['faculty'])->orderBy('name', 'asc')->get(['*']);

        $matchedStudyProgramId = null;
        if ($entitlement->student_level && $entitlement->code
            && str_starts_with($entitlement->code, $entitlement->student_level)) {
            $remainder = substr($entitlement->code, strlen($entitlement->student_level));
            $matchedStudyProgramId = $studyPrograms->first(fn ($p) => $p->faculty?->code
                && str_starts_with($remainder, $p->faculty->code)
                && substr($remainder, strlen($p->faculty->code)) === $p->code
            )?->id;
        }

        return view('distribution.entitlement.edit', compact('entitlement', 'studyPrograms', 'matchedStudyProgramId'));
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
            $entitlement = Entitlement::with('items.item')->findOrFail($entitlementId);
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
    private function getGroupedItems(): Collection
    {
        $items = Item::whereNotNull('base_code')
            ->with(['category', 'variants'])
            ->orderBy('base_code')
            ->get()
            ->groupBy('base_code');

        return $items->map(function ($group) {
            $rep = $group->first();
            $sizes = $group->mapWithKeys(fn ($item) => [
                $item->id => [
                    'id' => $item->id,
                    'code' => $item->code,
                    'label' => $item->variants->first()?->size_label ?? $item->code,
                ],
            ]);

            return (object) [
                'id' => $rep->id,
                'name' => $rep->name,
                'code' => $rep->base_code,
                'gender' => $rep->gender,
                'category' => $rep->category,
                'sizes' => $sizes,
            ];
        })->values();
    }
}
