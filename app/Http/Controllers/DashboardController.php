<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\SizeChangeEvent;
use App\Models\SizeEventSubmission;
use App\Models\Student;
use App\Services\StudentSizeService;
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
        $activeSchedule = DistributionSchedule::with('generation', 'faculty')
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
        $student->load(['activeSizeProfile', 'studyProgram', 'programLevel']);

        $sizeService = app(StudentSizeService::class);
        $sizeEvents = $sizeService->getEventsForStudent($student);

        $eventIds = $sizeEvents->pluck('id');
        $submissions = SizeEventSubmission::where('student_id', $student->id)
            ->whereIn('event_id', $eventIds)
            ->get()
            ->keyBy('event_id');

        $profile = $student->activeSizeProfile;

        return view('dashboards.student', compact('student', 'sizeEvents', 'submissions', 'profile'));
    }
}
