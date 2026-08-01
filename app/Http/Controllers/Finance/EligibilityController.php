<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\Finance\EligibilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EligibilityController extends Controller
{
    public function __construct(
        protected EligibilityService $eligibilityService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $search = $request->input('q', $request->input('search'));
        $students = $this->eligibilityService->search($search);

        if ($request->ajax()) {
            $html = view('finance.eligibility._table', compact('students', 'search'))->render();
            $pagination = view('components.alpine-pagination', ['paginator' => $students])->render();
            return response()->json(compact('html', 'pagination'));
        }

        return view('finance.eligibility.index', compact('students', 'search'));
    }

    public function toggle(Student $student): RedirectResponse
    {
        $message = $this->eligibilityService->toggle($student);

        return redirect()->back()->with('success', $message);
    }
}
