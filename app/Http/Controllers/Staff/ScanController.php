<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemPrice;
use App\Models\StockBalance;
use App\Models\Student;
use App\Services\DistributionService;
use App\Services\StudentSizeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function __construct(
        private readonly DistributionService $distributionService,
        private readonly StudentSizeService $studentSizeService,
    ) {}

    public function index(): View
    {
        $activeSchedule = DistributionSchedule::activeNow()->first();

        $staff = auth()->user();

        $todayCount = DistributionTransaction::whereDate('created_at', today())
            ->whereHas('schedule', fn ($q) => $q->where('is_active', true))
            ->count();

        return view('distribution.scan', compact('activeSchedule', 'staff', 'todayCount'));
    }

    public function search(Request $request): View|RedirectResponse|JsonResponse
    {
        $request->validate([
            'query' => 'required|string|max:100',
        ]);

        $student = $this->distributionService->findStudent($request->input('query'));

        if ($request->ajax() || $request->wantsJson()) {
            if (! $student) {
                // Uniform response — same structure regardless of existence
                return response()->json([
                    'found' => false,
                    'message' => 'Mahasiswa tidak ditemukan.',
                ]);
            }

            return response()->json([
                'found' => true,
                'redirect' => route('distribution.scan.student', $student->nim),
            ]);
        }

        if (! $student) {
            return back()->withErrors(['query' => 'Mahasiswa tidak ditemukan. Pastikan NIM valid.']);
        }

        return $this->showDistribution($student);
    }

    public function searchByQuery(Request $request): RedirectResponse
    {
        $nim = $request->query('query');
        if ($nim) {
            return redirect()->route('distribution.scan.student', $nim);
        }

        return redirect()->route('distribution.scan.index');
    }

    public function showByNim(Request $request, string $nim): View|RedirectResponse
    {
        $student = $this->distributionService->findStudent($nim);

        if (! $student) {
            return redirect()->route('distribution.scan.index')
                ->with('error', 'Mahasiswa dengan NIM '.$nim.' tidak ditemukan.');
        }

        $scheduleId = $request->query('schedule_id');

        return $this->showDistribution($student, $scheduleId);
    }

    public function selectSchedule(string $nim): View|RedirectResponse
    {
        $student = $this->distributionService->findStudent($nim);

        if (! $student) {
            return redirect()->route('distribution.scan.index')
                ->with('error', 'Mahasiswa dengan NIM '.$nim.' tidak ditemukan.');
        }

        $expiredSchedules = DistributionSchedule::where('is_active', true)
            ->where('date', '<=', today())
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($schedule) use ($student) {
                $hasTransaction = DistributionTransaction::where('schedule_id', $schedule->id)
                    ->where('student_id', $student->id)
                    ->exists();
                $schedule->student_has_taken = $hasTransaction;

                return $schedule;
            });

        return view('distribution.select-schedule', compact('student', 'expiredSchedules'));
    }

    public function process(Request $request): RedirectResponse
    {
        // Filter out items that are not checked (they won't have item_id submitted)
        $items = array_filter($request->input('items', []), function ($item) {
            return isset($item['item_id']) && ! empty($item['item_id']);
        });
        $request->merge(['items' => $items]);

        $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'schedule_id' => 'required|integer|exists:distribution_schedules,id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.actual_size' => 'required|string|max:10',
            'items.*.expected_size' => 'nullable|string|max:10',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'Pilih minimal satu barang yang akan didistribusikan.',
            'items.min' => 'Pilih minimal satu barang yang akan didistribusikan.',
        ]);

        $student = Student::with(['studyProgram', 'generation'])->findOrFail($request->input('student_id'));
        $schedule = DistributionSchedule::findOrFail($request->input('schedule_id'));
        $staff = auth()->user();

        try {
            $transaction = $this->distributionService->processDistribution(
                $student,
                $schedule,
                $staff,
                $request->input('items'),
                $request->input('notes')
            );
        } catch (\Exception $e) {
            return redirect()->route('distribution.scan.index')
                ->with('error', 'Distribusi gagal: '.$e->getMessage());
        }

        return redirect()->route('distribution.scan.index')
            ->with('success', "Distribusi berhasil dicatat untuk {$student->nim} - {$student->name}.");
    }

    private function showDistribution(Student $student, ?string $scheduleId = null): View
    {
        if ($scheduleId) {
            $activeSchedule = DistributionSchedule::find($scheduleId);
        } else {
            $activeSchedule = DistributionSchedule::activeNow()->first();
        }

        $entitlement = null;
        $scheduleItems = collect();
        $studentSizes = [];
        $variantOptions = [];
        $eligibility = null;

        if ($activeSchedule) {
            $entitlement = $this->distributionService->getEntitlementForStudent($student);
            $activeSchedule->load('items.item.variants', 'items.item.category');
            $scheduleItems = $activeSchedule->items->pluck('item')->filter();
        }

        $eligibility = $this->distributionService->getStudentEligibility($student);

        $sizeProfile = $student->activeSizeProfile;
        $studentSizes = [];
        if ($sizeProfile && $scheduleItems->isNotEmpty()) {
            $sizeProfile->load('sizeItems');
            $sizeItemsByItemId = $sizeProfile->sizeItems->keyBy('item_id');
            $categories = ItemCategory::whereIn('id', $scheduleItems->pluck('category_id'))
                ->pluck('code', 'id');

            foreach ($scheduleItems as $item) {
                $baseCode = $item->base_code ?? $item->code;
                if (! $baseCode) {
                    continue;
                }

                $catCode = $categories[$item->category_id] ?? null;
                $stored = $catCode === 'UNF' ? $sizeProfile->baju_size
                    : ($catCode === 'SHO' ? $sizeProfile->sepatu_size : null);

                $sizeItem = $sizeItemsByItemId->get($item->id);

                if (empty($stored) && ! $sizeItem) {
                    continue;
                }

                if (! empty($stored)) {
                    $resolved = $this->studentSizeService->resolveSizeValue($item, $stored)
                        ?? ['code' => $stored, 'label' => $stored];
                    $studentSizes[$baseCode] = [
                        'size' => $resolved['code'],
                        'size_label' => $resolved['label'],
                        'change_count' => $sizeItem?->change_count ?? 0,
                    ];

                    continue;
                }

                $resolved = $this->studentSizeService->resolveSizeValue($item, $sizeItem->size)
                    ?? ['code' => $sizeItem->size, 'label' => $sizeItem->size];
                $studentSizes[$baseCode] = [
                    'size' => $resolved['code'],
                    'size_label' => $resolved['label'],
                    'change_count' => $sizeItem->change_count,
                ];
            }
        }

        $distributedItems = DB::table('distribution_items')
            ->join('distribution_transactions', 'distribution_items.transaction_id', '=', 'distribution_transactions.id')
            ->join('items', 'distribution_items.item_id', '=', 'items.id')
            ->where('distribution_transactions.student_id', $student->id)
            ->whereIn('distribution_transactions.status', ['completed', 'partial'])
            ->where('distribution_transactions.schedule_id', $activeSchedule?->id)
            ->select('items.base_code', DB::raw('SUM(distribution_items.quantity) as total_qty'))
            ->whereNotNull('items.base_code')
            ->groupBy('items.base_code')
            ->pluck('total_qty', 'base_code')
            ->toArray();

        $entitledQuantities = [];
        if ($entitlement) {
            $itemIds = $entitlement->items->pluck('item_id');
            $itemBaseCodes = Item::whereIn('id', $itemIds)->pluck('base_code', 'id');
            $entitledQuantities = $entitlement->items->pluck('quantity', 'item_id')
                ->mapWithKeys(fn ($qty, $itemId) => isset($itemBaseCodes[$itemId]) ? [$itemBaseCodes[$itemId] => $qty] : [])
                ->toArray();
        }

        $stockInfo = [];
        if ($activeSchedule) {
            $baseCodes = $scheduleItems->pluck('base_code')->filter()->unique()->values();
            $groupItems = Item::whereIn('base_code', $baseCodes)->with('variants')->get();
            $noBaseScheduleItems = $scheduleItems->filter(fn ($i) => empty($i->base_code))->values();

            $balanceItems = $groupItems->concat($noBaseScheduleItems);
            $allBalances = StockBalance::whereIn('item_id', $balanceItems->pluck('id'))
                ->whereIn('variant_id', $balanceItems->flatMap->variants->pluck('id'))
                ->get()
                ->keyBy(fn ($b) => $b->item_id.'-'.$b->variant_id);

            foreach ($groupItems as $item) {
                $baseCode = $item->base_code;
                if (! $baseCode) {
                    continue;
                }
                foreach ($item->variants as $variant) {
                    $balance = $allBalances[$item->id.'-'.$variant->id] ?? null;
                    $stockInfo[$baseCode][$variant->size] = ($stockInfo[$baseCode][$variant->size] ?? 0)
                        + ($balance ? $balance->quantity : 0);
                }
            }

            foreach ($noBaseScheduleItems as $item) {
                $code = $item->code;
                if (! $code) {
                    continue;
                }
                foreach ($item->variants as $variant) {
                    $balance = $allBalances[$item->id.'-'.$variant->id] ?? null;
                    $stockInfo[$code][$variant->size] = ($stockInfo[$code][$variant->size] ?? 0)
                        + ($balance ? $balance->quantity : 0);
                }
            }
        }

        // Fallback ukuran harapan untuk item yang belum memiliki ukuran mahasiswa
        // (mis. kategori selain UNF/SHO seperti ALM) agar stok tetap terbaca.
        if ($scheduleItems->isNotEmpty()) {
            foreach ($scheduleItems as $item) {
                $baseCode = $item->base_code ?? $item->code;
                if (! $baseCode || isset($studentSizes[$baseCode])) {
                    continue;
                }

                $variants = $item->variants;
                if ($variants->isEmpty()) {
                    continue;
                }

                $candidate = null;
                if ($sizeProfile?->baju_size) {
                    $resolved = $this->studentSizeService->resolveSizeValue($item, $sizeProfile->baju_size);
                    if ($resolved && $variants->contains('size', $resolved['code'])) {
                        $candidate = $resolved;
                    }
                }

                if (! $candidate && $sizeProfile?->sepatu_size) {
                    $resolved = $this->studentSizeService->resolveSizeValue($item, $sizeProfile->sepatu_size);
                    if ($resolved && $variants->contains('size', $resolved['code'])) {
                        $candidate = $resolved;
                    }
                }

                if (! $candidate) {
                    $stocked = collect($stockInfo[$baseCode] ?? []);
                    $size = $stocked->isNotEmpty()
                        ? $stocked->sort()->reverse()->keys()->first()
                        : $variants->first()->size;
                    $candidate = [
                        'code' => $size,
                        'label' => $variants->firstWhere('size', $size)?->size_label ?? $size,
                    ];
                }

                $studentSizes[$baseCode] = [
                    'size' => $candidate['code'],
                    'size_label' => $candidate['label'],
                    'change_count' => 0,
                ];
            }
        }

        $itemPrices = [];
        $itemIds = $scheduleItems->pluck('id');
        if ($itemIds->isNotEmpty()) {
            $allPrices = ItemPrice::whereIn('item_id', $itemIds)
                ->where('effective_date', '<=', $activeSchedule->date)
                ->orderBy('effective_date', 'desc')
                ->get()
                ->groupBy('item_id')
                ->map(fn ($prices) => $prices->first()->selling_price);
            foreach ($scheduleItems as $item) {
                $itemPrices[$item->id] = $allPrices[$item->id] ?? $item->selling_price ?? 0;
            }
        }

        $baseCodes = $scheduleItems->pluck('base_code')->filter()->unique();
        $preloadedGroupVariants = Item::whereIn('base_code', $baseCodes)
            ->with('variants')
            ->get()
            ->groupBy('base_code');
        foreach ($scheduleItems as $item) {
            $baseCode = $item->base_code ?? $item->code;
            if (isset($variantOptions[$baseCode])) {
                continue;
            }
            if ($item->base_code) {
                $group = $preloadedGroupVariants->get($item->base_code, collect());
                $variantOptions[$baseCode] = $group->flatMap->variants;
            } else {
                $variantOptions[$baseCode] = $item->variants;
            }
        }

        return view('distribution.distribution', compact(
            'student',
            'activeSchedule',
            'entitlement',
            'scheduleItems',
            'studentSizes',
            'variantOptions',
            'stockInfo',
            'eligibility',
            'distributedItems',
            'entitledQuantities',
            'itemPrices'
        ));
    }
}
