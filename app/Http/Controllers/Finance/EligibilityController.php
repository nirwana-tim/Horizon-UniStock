<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\EligibilityRecord;
use App\Models\Student;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EligibilityController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->input('q', $request->input('search'));
        $search = str_replace(['%', '_'], ['\%', '\_'], $search ?? '');

        $students = Student::with(['studyProgram.faculty', 'eligibilityRecords'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('email_kampus', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20);

        if ($request->ajax()) {
            $html = view('finance.eligibility._table', compact('students', 'search'))->render();
            $pagination = view('components.alpine-pagination', ['paginator' => $students])->render();
            return response()->json(compact('html', 'pagination'));
        }

        return view('finance.eligibility.index', compact('students', 'search'));
    }

    public function toggle(Student $student): RedirectResponse
    {
        DB::transaction(function () use ($student, &$message) {
            $record = EligibilityRecord::where('student_id', $student->id)
                ->lockForUpdate()
                ->first();

            if ($record) {
                AuditService::log('delete', 'eligibility_record', $record->id, $record->toArray(), null);
                $record->delete();
                $message = "Status kelayakan untuk mahasiswa {$student->name} berhasil dihapus (Set Belum Lunas).";
            } else {
                $newRecord = EligibilityRecord::create([
                    'student_id' => $student->id,
                    'is_eligible' => true,
                    'payment_status' => 'Paid',
                ]);
                AuditService::log('create', 'eligibility_record', $newRecord->id, null, $newRecord->toArray());
                $message = "Mahasiswa {$student->name} berhasil di-set Layak (Lunas).";
            }
        });

        return redirect()->back()->with('success', $message);
    }
}
