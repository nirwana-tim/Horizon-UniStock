<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\Student;
use App\Services\QrCodeService;
use App\Services\StudentSizeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SizeController extends Controller
{
    public function __construct(
        private readonly StudentSizeService $sizeService,
        private readonly QrCodeService $qrCodeService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $activeEvent = $this->sizeService->getActiveEventForStudent($student);
        $entitlementItems = $this->sizeService->getEntitlementItems($student);

        $existingSizes = [];
        if ($student->activeSizeProfile) {
            foreach ($student->activeSizeProfile->sizeItems as $sizeItem) {
                $existingSizes[$sizeItem->item_id] = $sizeItem->size;
            }
        }

        $changedItemIds = [];
        if ($student->activeSizeProfile) {
            $changedItemIds = $student->activeSizeProfile->sizeItems
                ->where('change_count', '>=', 1)
                ->pluck('item_id')
                ->toArray();
        }

        $maxChangedItemIds = [];
        if ($student->activeSizeProfile && $activeEvent) {
            $maxChangedItemIds = $student->activeSizeProfile->sizeItems
                ->filter(fn ($si) => ! $activeEvent->canEdit($si))
                ->pluck('item_id')
                ->toArray();
        }

        return view('student.size-input', compact(
            'student',
            'activeEvent',
            'entitlementItems',
            'existingSizes',
            'changedItemIds',
            'maxChangedItemIds'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'sizes' => 'required|array',
            'sizes.*' => 'nullable|string|max:10',
        ]);

        try {
            $this->sizeService->saveSizes($student, $validated['sizes']);
        } catch (\RuntimeException $e) {
            return redirect()->route('student.sizes.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('student.qr')
            ->with('success', 'Ukuran berhasil disimpan. Silakan lihat QR Code Anda.');
    }

    public function qr(): View
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $qrDataUrl = $this->qrCodeService->getQrPngDataUrl($student);

        $activeSchedules = DistributionSchedule::with('programLevel', 'faculty')
            ->where('is_active', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->forStudent($student)
            ->orderBy('date')
            ->take(5)
            ->get();

        return view('student.qr-show', compact('student', 'activeSchedules', 'qrDataUrl'));
    }

    public function items(): View
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();
        $student->load(['activeSizeProfile.sizeItems.item', 'studyProgram', 'programLevel']);

        $entitlementItems = $this->sizeService->getEntitlementItems($student);

        $receivedTransactions = DistributionTransaction::with(['items.item', 'schedule'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['completed', 'partial'])
            ->latest()
            ->get();

        $selectedSizes = [];
        if ($student->activeSizeProfile) {
            foreach ($student->activeSizeProfile->sizeItems as $si) {
                $selectedSizes[$si->item_id] = $si->size;
            }
        }

        $receivedItemIds = $receivedTransactions->flatMap->items->pluck('item_id')->unique()->toArray();

        return view('student.items', compact(
            'student', 'entitlementItems', 'receivedTransactions',
            'selectedSizes', 'receivedItemIds'
        ));
    }
}
