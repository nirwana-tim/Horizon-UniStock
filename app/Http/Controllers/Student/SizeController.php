<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\SizeChangeEvent;
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

        $events = $this->sizeService->getEventsForStudent($student);

        $eventIds = $events->pluck('id');
        $submissions = \App\Models\SizeEventSubmission::where('student_id', $student->id)
            ->whereIn('event_id', $eventIds)
            ->get()
            ->keyBy('event_id');

        return view('student.sizes-index', compact('student', 'events', 'submissions'));
    }

    public function input(SizeChangeEvent $event): View
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        if (! $event->isApplicableToStudent($student)) {
            abort(403, 'Event ini tidak berlaku untuk kamu.');
        }

        $entitlementItems = $this->sizeService->getEntitlementItems($student);

        $existingSizes = [];
        if ($student->activeSizeProfile) {
            foreach ($student->activeSizeProfile->sizeItems as $sizeItem) {
                $existingSizes[$sizeItem->item_id] = $sizeItem->size;
            }
        }

        $submission = \App\Models\SizeEventSubmission::where('student_id', $student->id)
            ->where('event_id', $event->id)->first();
        $submissionCount = $submission?->submission_count ?? 0;
        $canEdit = $submissionCount < $event->max_changes;
        $remainingChanges = $event->max_changes - $submissionCount;

        return view('student.size-input', compact(
            'student', 'event', 'entitlementItems',
            'existingSizes', 'canEdit', 'remainingChanges'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'sizes' => 'required|array',
            'sizes.*' => 'nullable|string|max:10',
            'event_id' => 'nullable|integer|exists:size_change_events,id',
        ]);

        try {
            $this->sizeService->saveSizes($student, $validated['sizes'], $validated['event_id'] ?? null);
        } catch (\RuntimeException $e) {
            $route = $validated['event_id']
                ? route('student.sizes.input', $validated['event_id'])
                : route('student.sizes.index');
            return redirect()->to($route)
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
