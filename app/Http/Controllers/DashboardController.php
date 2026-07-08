<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\Faculty;
use App\Models\Item;
use App\Models\StockBalance;
use App\Models\StockOpname;
use App\Models\StockReceive;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        if ($user->hasRole(Role::SuperAdmin->value)) {
            return view('dashboards.super-admin');
        }

        if ($user->hasRole(Role::Admin->value)) {
            return view('dashboards.finance');
        }

        if ($user->hasRole(Role::Staff->value)) {
            return $this->staffDashboard();
        }

        return $this->studentDashboard();
    }

    public function stats(): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(Role::SuperAdmin->value)) {
            return response()->json([
                'totalUsers' => User::count(),
                'totalStudents' => Student::count(),
                'totalItems' => Item::count(),
                'totalStockReceives' => StockReceive::count(),
                'outOfStockItems' => StockBalance::where('quantity', '<=', 0)->count(),
            ]);
        }

        return response()->json([
            'totalFaculties' => Faculty::count(),
            'totalStudyPrograms' => StudyProgram::count(),
            'totalItems' => Item::count(),
            'monthlyReceives' => StockReceive::whereMonth('receive_date', now()->month)->count(),
            'draftOpnames' => StockOpname::where('status', 'draft')->count(),
            'outOfStockItems' => StockBalance::where('quantity', '<=', 0)->count(),
        ]);
    }

    public function lowStock(): View|JsonResponse
    {
        $lowStockItems = StockBalance::with('item')
            ->where('quantity', '>', 0)
            ->where('quantity', '<=', 5)
            ->orderBy('quantity')
            ->take(10)
            ->get();

        if (request()->wantsJson()) {
            return response()->json([
                'html' => view('dashboards._low-stock', compact('lowStockItems'))->render(),
            ]);
        }

        return view('dashboards._low-stock', compact('lowStockItems'));
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
        $student->load(['activeSizeProfile', 'studyProgram', 'programLevel']);

        $data = [
            'student' => $student,
            'hasFilledSize' => !is_null($student->activeSizeProfile),
            'hasQr' => true,
            'activeSchedules' => DistributionSchedule::query()
                ->where('is_active', true)
                ->where('date', '>=', now()->format('Y-m-d'))
                ->where(fn ($q) => $q->whereNull('program_level_id')->orWhere('program_level_id', $student->program_level_id))
                ->where(fn ($q) => $q->whereNull('faculty_id')->orWhere('faculty_id', $student->studyProgram?->faculty_id))
                ->where(fn ($q) => $q->whereNull('study_program_id')->orWhere('study_program_id', $student->study_program_id))
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
