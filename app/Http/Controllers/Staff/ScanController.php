<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DistributionSchedule;
use App\Models\ItemVariant;
use App\Models\StockBalance;
use App\Models\Student;
use App\Services\DistributionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function __construct(
        private readonly DistributionService $distributionService
    ) {}

    public function index(): View
    {
        $activeSchedule = DistributionSchedule::where('is_active', true)
            ->where('date', today())
            ->first();

        $staff = auth()->user();

        $todayCount = \App\Models\DistributionTransaction::whereDate('created_at', today())
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

    public function showByNim(string $nim): View|RedirectResponse
    {
        $student = $this->distributionService->findStudent($nim);

        if (! $student) {
            return redirect()->route('distribution.scan.index')
                ->with('error', 'Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.');
        }

        return $this->showDistribution($student);
    }

    public function process(Request $request): RedirectResponse
    {
        // Filter out items that are not checked (they won't have item_id submitted)
        $items = array_filter($request->input('items', []), function ($item) {
            return isset($item['item_id']) && ! empty($item['item_id']);
        });
        $request->merge(['items' => $items]);

        $request->validate([
            'student_id' => 'required|integer',
            'schedule_id' => 'required|integer',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer',
            'items.*.actual_size' => 'required|string|max:10',
            'items.*.expected_size' => 'nullable|string|max:10',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'Pilih minimal satu barang yang akan didistribusikan.',
            'items.min' => 'Pilih minimal satu barang yang akan didistribusikan.',
        ]);

        $student = Student::with(['studyProgram', 'programLevel'])->findOrFail($request->input('student_id'));
        $schedule = DistributionSchedule::findOrFail($request->input('schedule_id'));
        $staff = auth()->user();

        $transaction = $this->distributionService->processDistribution(
            $student,
            $schedule,
            $staff,
            $request->input('items'),
            $request->input('notes')
        );

        return redirect()->route('distribution.scan.index')
            ->with('success', "Distribusi berhasil dicatat untuk {$student->nim} - {$student->name}.");
    }

    private function showDistribution(Student $student): View
    {
        $activeSchedule = DistributionSchedule::where('is_active', true)
            ->where('date', today())
            ->first();

        $entitlement = null;
        $scheduleItems = collect();
        $studentSizes = [];
        $variantOptions = [];
        $eligibility = null;

        if ($activeSchedule) {
            $entitlement = $this->distributionService->getEntitlementForStudent($student);
            $activeSchedule->load('items.item.variants');
            $scheduleItems = $activeSchedule->items->pluck('item')->filter();
        }

        $eligibility = $this->distributionService->getStudentEligibility($student);

        $sizeProfile = $student->activeSizeProfile;
        if ($sizeProfile) {
            $sizeProfile->load('sizeItems.item');
            foreach ($sizeProfile->sizeItems as $sizeItem) {
                $baseCode = $sizeItem->item->base_code;
                if (!$baseCode) continue;
                $variant = ItemVariant::where('item_id', $sizeItem->item_id)
                    ->where('size', $sizeItem->size)
                    ->first();
                $studentSizes[$baseCode] = [
                    'size' => $sizeItem->size,
                    'size_label' => $variant?->size_label ?? $sizeItem->size,
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

        $entitledQuantities = $entitlement
            ? $entitlement->items->pluck('quantity', 'item_id')->mapWithKeys(function ($qty, $itemId) {
                $baseCode = Item::where('id', $itemId)->value('base_code');
                return $baseCode ? [$baseCode => $qty] : [];
            })->toArray()
            : [];

        $stockInfo = [];
        if ($activeSchedule) {
            foreach ($scheduleItems as $item) {
                $baseCode = $item->base_code ?? $item->code;
                foreach ($item->variants as $variant) {
                    $balance = StockBalance::where('item_id', $item->id)
                        ->where('variant_id', $variant->id)
                        ->first();
                    $stockInfo[$baseCode][$variant->size] = ($stockInfo[$baseCode][$variant->size] ?? 0)
                        + ($balance ? $balance->quantity : 0);
                }
            }
        }

        foreach ($scheduleItems as $item) {
            $baseCode = $item->base_code ?? $item->code;
            if (isset($variantOptions[$baseCode])) continue;
            if ($item->base_code) {
                $variantOptions[$baseCode] = ItemVariant::whereIn('item_id',
                    Item::where('base_code', $item->base_code)->pluck('id')
                )->get();
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
            'entitledQuantities'
        ));
    }
}
