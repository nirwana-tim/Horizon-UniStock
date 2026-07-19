<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(Role::SuperAdmin->value) || $user->hasRole(Role::Admin->value)) {
            return app(ReportController::class)->salesDashboard($request);
        }

        if ($user->hasRole(Role::Staff->value)) {
            return $this->staffDashboard();
        }

        return $this->studentDashboard();
    }

    private function staffDashboard(): View
    {
        $activeSchedule = DistributionSchedule::with('programLevel', 'faculty')
            ->where('is_active', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->first();

        $todayCount = DistributionTransaction::whereDate('created_at', today())->count();

        $recentTransactions = DistributionTransaction::with('student.user', 'schedule')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboards.staff', compact('activeSchedule', 'todayCount', 'recentTransactions'));
    }

    private function studentDashboard(): View
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $student->load(['activeSizeProfile.sizeItems.item', 'studyProgram', 'programLevel']);

        $selectedSizes = [];
        if ($student->activeSizeProfile) {
            foreach ($student->activeSizeProfile->sizeItems as $si) {
                $selectedSizes[$si->item_id] = ['size' => $si->size, 'name' => $si->item?->name ?? 'Item'];
            }
        }

        $data = [
            'student' => $student,
            'hasFilledSize' => ! is_null($student->activeSizeProfile),
            'selectedSizes' => $selectedSizes,
            'hasQr' => true,
            'activeSchedules' => DistributionSchedule::query()
                ->where('is_active', true)
                ->where('date', '>=', now()->format('Y-m-d'))
                ->forStudent($student)
                ->orderBy('date')
                ->take(5)
                ->get(),
            'recentTransactions' => DistributionTransaction::with('schedule')
                ->where('student_id', $student->id)
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('dashboards.student', $data);
    }
}
