<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DistributionSchedule;
use App\Models\Student;
use App\Services\StudentSizeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SizeController extends Controller
{
    public function __construct(
        private readonly StudentSizeService $sizeService
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $entitlementItems = $this->sizeService->getEntitlementItems($student);

        $existingSizes = [];
        if ($student->activeSizeProfile) {
            foreach ($student->activeSizeProfile->sizeItems as $sizeItem) {
                $existingSizes[$sizeItem->item_id] = $sizeItem->size;
            }
        }

        $canUpdate = true;
        if ($student->activeSizeProfile) {
            $profile = $student->activeSizeProfile;
            if ($profile->sizeItems->contains('change_count', '>=', 1)) {
                $canUpdate = false;
            }
        }

        return view('student.size-input', compact(
            'student',
            'entitlementItems',
            'existingSizes',
            'canUpdate'
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

        $this->sizeService->saveSizes($student, $validated['sizes']);

        return redirect()->route('student.qr')
            ->with('success', 'Ukuran berhasil disimpan. Silakan lihat QR Code Anda.');
    }

    public function qr(): View
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        if (!$student->qr_token) {
            $this->sizeService->generateQr($student);
        }

        $activeSchedules = DistributionSchedule::with('programLevel', 'faculty')
            ->where('is_active', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->take(5)
            ->get();

        return view('student.qr-show', compact('student', 'activeSchedules'));
    }
}
