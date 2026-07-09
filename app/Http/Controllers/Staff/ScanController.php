<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DistributionSchedule;
use App\Models\ItemVariant;
use App\Models\Student;
use App\Models\StockBalance;
use App\Services\DistributionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('distribution.scan', compact('activeSchedule'));
    }

    public function search(Request $request): View|RedirectResponse|JsonResponse
    {
        $request->validate([
            'query' => 'required|string|max:100',
        ]);

        $student = $this->distributionService->findStudent($request->input('query'));

        if ($request->ajax() || $request->wantsJson()) {
            if (!$student) {
                // Uniform response — same structure regardless of existence
                return response()->json([
                    'found' => false,
                    'message' => 'Mahasiswa tidak ditemukan.',
                ]);
            }

            return response()->json([
                'found' => true,
                'redirect' => route('distribution.search') . '?query=' . urlencode($student->nim),
            ]);
        }

        if (!$student) {
            return back()->withErrors(['query' => 'Mahasiswa tidak ditemukan. Pastikan NIM valid.']);
        }

        return $this->showDistribution($student);
    }

    public function process(Request $request): RedirectResponse
    {
        // Filter out items that are not checked (they won't have item_id submitted)
        $items = array_filter($request->input('items', []), function ($item) {
            return isset($item['item_id']) && !empty($item['item_id']);
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
        $eligibility = null;

        if ($activeSchedule) {
            $entitlement = $this->distributionService->getEntitlementForStudent($student, $activeSchedule);
            $activeSchedule->load('items.item.variants');
            $scheduleItems = $activeSchedule->items->pluck('item')->filter();
        }

        $eligibility = $this->distributionService->getStudentEligibility($student);

        $sizeProfile = $student->activeSizeProfile;
        if ($sizeProfile) {
            foreach ($sizeProfile->sizeItems as $sizeItem) {
                // Cari size_label dari variant yang cocok
                $variant = ItemVariant::where('item_id', $sizeItem->item_id)
                    ->where('size', $sizeItem->size)
                    ->first();
                $studentSizes[$sizeItem->item_id] = [
                    'size'        => $sizeItem->size,
                    'size_label'  => $variant?->size_label ?? $sizeItem->size,
                    'change_count'=> $sizeItem->change_count,
                ];
            }
        }

        $distributedItems = \Illuminate\Support\Facades\DB::table('distribution_items')
            ->join('distribution_transactions', 'distribution_items.transaction_id', '=', 'distribution_transactions.id')
            ->where('distribution_transactions.student_id', '=', $student->id)
            ->select('distribution_items.item_id', \Illuminate\Support\Facades\DB::raw('SUM(distribution_items.quantity) as total_qty'))
            ->groupBy('distribution_items.item_id')
            ->pluck('total_qty', 'item_id')
            ->toArray();

        $entitledQuantities = $entitlement
            ? $entitlement->items->pluck('quantity', 'item_id')->toArray()
            : [];

        $stockInfo = [];
        if ($activeSchedule) {
            foreach ($scheduleItems as $item) {
                $variants = ItemVariant::where('item_id', '=', $item->id, 'and')->get();
                foreach ($variants as $variant) {
                    $balance = StockBalance::where('item_id', '=', $item->id, 'and')
                        ->where('variant_id', '=', $variant->id, 'and')
                        ->first();
                    $stockInfo[$item->id][$variant->size] = $balance ? $balance->quantity : 0;
                }
            }
        }

        return view('distribution.distribution', compact(
            'student',
            'activeSchedule',
            'entitlement',
            'scheduleItems',
            'studentSizes',
            'stockInfo',
            'eligibility',
            'distributedItems',
            'entitledQuantities'
        ));
    }
}
