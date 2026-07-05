<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return $this->superAdminDashboard();
        }

        if ($user->hasRole('admin')) {
            return $this->financeDashboard();
        }

        if ($user->hasRole('staff')) {
            return $this->staffDashboard();
        }

        return $this->studentDashboard();
    }

    private function superAdminDashboard(): View
    {
        $data = [
            'totalUsers' => User::count(),
            'totalStudents' => Student::count(),
            'totalItems' => Item::count(),
            'totalStockReceives' => StockReceive::count(),
            'outOfStockItems' => StockBalance::where('quantity', '<=', 0)->count(),
            'lowStockItems' => StockBalance::with('item')
                ->where('quantity', '>', 0)
                ->where('quantity', '<=', 5)
                ->orderBy('quantity')
                ->take(10)
                ->get(),
        ];

        return view('dashboards.super-admin', $data);
    }

    private function financeDashboard(): View
    {
        $data = [
            'totalFaculties' => Faculty::count(),
            'totalStudyPrograms' => StudyProgram::count(),
            'totalItems' => Item::count(),
            'monthlyReceives' => StockReceive::whereMonth('receive_date', now()->month)->count(),
            'draftOpnames' => StockOpname::where('status', 'draft')->count(),
            'outOfStockItems' => StockBalance::where('quantity', '<=', 0)->count(),
            'lowStockItems' => StockBalance::with('item')
                ->where('quantity', '>', 0)
                ->where('quantity', '<=', 5)
                ->orderBy('quantity')
                ->take(10)
                ->get(),
        ];

        return view('dashboards.finance', $data);
    }

    private function staffDashboard(): View
    {
        return view('dashboards.staff');
    }

    private function studentDashboard(): View
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $student->load(['activeSizeProfile', 'studyProgram', 'programLevel']);

        $data = [
            'student' => $student,
            'hasFilledSize' => !is_null($student->activeSizeProfile),
            'hasQr' => !is_null($student->qr_token),
            'activeSchedules' => DistributionSchedule::query()
                ->where('is_active', true)
                ->where('date', '>=', now()->format('Y-m-d'))
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
