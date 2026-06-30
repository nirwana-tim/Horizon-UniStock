<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DistributionSchedule;
use App\Models\ItemVariant;
use App\Models\Student;
use App\Models\StockBalance;
use App\Services\DistributionService;
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
            ->with('stage')
            ->first();

        return view('staff.scan', compact('activeSchedule'));
    }

    public function search(Request $request): View|RedirectResponse
    {
        $request->validate([
            'query' => 'required|string|max:100',
        ]);

        $student = $this->distributionService->findStudent($request->input('query'));

        if (!$student) {
            return back()->withErrors(['query' => 'Mahasiswa tidak ditemukan. Pastikan NIM atau QR token valid.']);
        }

        return $this->showDistribution($student);
    }

    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'student_id' => 'required|integer',
            'schedule_id' => 'required|integer',
            'items' => 'required|array',
            'items.*.item_id' => 'required|integer',
            'items.*.actual_size' => 'required|string|max:10',
            'items.*.expected_size' => 'nullable|string|max:10',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $student = Student::with(['studyProgram', 'programLevel'])->findOrFail($request->input('student_id'));
        $schedule = DistributionSchedule::findOrFail($request->input('schedule_id'));
        $staff = auth()->user();

        $transaction = $this->distributionService->processDistribution(
            $student,
            $schedule,
            $staff,
            $request->input('items')
        );

        return redirect()->route('staff.scan.index')
            ->with('success', "Distribusi berhasil dicatat untuk {$student->nim} - {$student->name}.");
    }

    private function showDistribution(Student $student): View
    {
        $activeSchedule = DistributionSchedule::where('is_active', true)
            ->where('date', today())
            ->with('stage')
            ->first();

        $entitlement = null;
        $scheduleItems = collect();
        $studentSizes = [];

        if ($activeSchedule) {
            $entitlement = $this->distributionService->getEntitlementForStudent($student, $activeSchedule);
            $scheduleItems = $activeSchedule->items->pluck('item')->filter();
        }

        $sizeProfile = $student->activeSizeProfile;
        if ($sizeProfile) {
            foreach ($sizeProfile->sizeItems as $sizeItem) {
                $studentSizes[$sizeItem->item_id] = [
                    'size' => $sizeItem->size,
                    'change_count' => $sizeItem->change_count,
                ];
            }
        }

        $stockInfo = [];
        if ($activeSchedule) {
            foreach ($scheduleItems as $item) {
                $variants = ItemVariant::where('item_id', $item->id)->get();
                foreach ($variants as $variant) {
                    $balance = StockBalance::where('item_id', $item->id)
                        ->where('variant_id', $variant->id)
                        ->first();
                    $stockInfo[$item->id][$variant->size] = $balance ? $balance->quantity : 0;
                }
            }
        }

        return view('staff.distribution', compact(
            'student',
            'activeSchedule',
            'entitlement',
            'scheduleItems',
            'studentSizes',
            'stockInfo'
        ));
    }
}
