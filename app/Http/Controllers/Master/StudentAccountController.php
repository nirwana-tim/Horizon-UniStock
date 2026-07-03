<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentAccountController extends Controller
{
    public function index(): View
    {
        $studentsWithoutAccount = Student::whereNull('user_id')
            ->with(['studyProgram', 'programLevel'])
            ->paginate(20);

        $totalStudents = Student::count();
        $totalWithAccount = Student::whereNotNull('user_id')->count();
        $totalWithoutAccount = $studentsWithoutAccount->total();

        return view('master.student-account.index', compact(
            'studentsWithoutAccount',
            'totalStudents',
            'totalWithAccount',
            'totalWithoutAccount',
        ));
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['required', 'integer', 'exists:students,id'],
        ]);

        $students = Student::whereIn('id', $validated['student_ids'])
            ->whereNull('user_id')
            ->get();

        $generated = 0;

        DB::transaction(function () use ($students, &$generated) {
            foreach ($students as $student) {
                $password = Str::random(12);

                $user = User::create([
                    'name' => $student->name,
                    'email' => $student->email_kampus ?? "{$student->nim}@temp.horizon.ac.id",
                    'password' => Hash::make($password),
                    'must_change_password' => true,
                ]);

                $user->assignRole('student');

                $student->update([
                    'user_id' => $user->id,
                ]);

                AuditService::log('create', 'student_account', $user->id, null, [
                    'student_id' => $student->id,
                    'nim' => $student->nim,
                    'name' => $student->name,
                ]);

                $generated++;
            }
        });

        return redirect()->route('master.student-account.index')
            ->with('success', "$generated akun mahasiswa berhasil digenerate.");
    }

    public function generateAll(Request $request): RedirectResponse
    {
        $students = Student::whereNull('user_id')->get();
        $ids = $students->pluck('id')->toArray();

        if (empty($ids)) {
            return redirect()->route('master.student-account.index')
                ->with('info', 'Semua mahasiswa sudah memiliki akun.');
        }

        $request->merge(['student_ids' => $ids]);

        return $this->generate($request);
    }
}
