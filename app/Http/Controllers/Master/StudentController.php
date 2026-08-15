<?php

namespace App\Http\Controllers\Master;

use App\Exports\CredentialsExport;
use App\Exports\StudentExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Models\DistributionItem;
use App\Models\Entitlement;
use App\Models\Student;
use App\Models\StudentGeneration;
use App\Models\StudyProgram;
use App\Services\AuditService;
use App\Services\Master\StudentService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentController extends Controller
{
    public function __construct(
        protected StudentService $studentService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = Student::with(['studyProgram', 'generation', 'studentLevel']);

        if ($search = $request->input('q')) {
            $search = $this->escapeLike($search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('email_kampus', 'like', "%{$search}%");
            });
        }

        if ($studyProgramId = $request->input('study_program_id')) {
            $query->where('study_program_id', $studyProgramId);
        }

        if ($generationId = $request->input('generation_id')) {
            $query->where('generation_id', $generationId);
        }

        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        $students = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('master.student._table', compact('students'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $students])->render(),
            ]);
        }

        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();
        $generations = StudentGeneration::orderBy('name')->get();

        return view('master.student.index', compact(
            'students',
            'studyPrograms',
            'generations',
            'perPage',
        ));
    }

    public function generateIndex(Request $request): View
    {
        $studentsWithoutAccount = Student::whereNull('user_id')
            ->with(['studyProgram', 'generation', 'studentLevel'])
            ->paginate(10, ['*'], 'account_page');

        $studentsPending = Student::whereNotNull('user_id')
            ->whereHas('user', fn ($q) => $q->where('must_change_password', true))
            ->with(['studyProgram', 'generation', 'user'])
            ->orderBy('nim')
            ->limit(100)
            ->get();

        $notificationService = app(NotificationService::class);
        $latestEmails = $notificationService->latestAccountNotificationsForStudents($studentsPending);
        $studentsPending = $studentsPending->map(function (Student $student) use ($latestEmails) {
            $student->latest_email = $latestEmails[$student->id] ?? null;

            return $student;
        });

        $totalStudents = cache()->remember('student-count-total', 120, fn () => Student::count());
        $totalWithAccount = cache()->remember('student-count-with-account', 120, fn () => Student::whereNotNull('user_id')->count());
        $totalWithoutAccount = cache()->remember('student-count-without-account', 120, fn () => Student::whereNull('user_id')->count());

        return view('master.student.generate', compact(
            'studentsWithoutAccount',
            'studentsPending',
            'totalStudents',
            'totalWithAccount',
            'totalWithoutAccount',
        ));
    }

    public function create(): View
    {
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();

        return view('master.student.create', compact('studyPrograms'));
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        $this->studentService->store($request->validated());

        return redirect()->route('students.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show(Student $student): View
    {
        $student->load(['studyProgram.faculty', 'generation', 'user', 'studentLevel']);

        return view('master.student.show', compact('student'));
    }

    public function entitlement(Student $student): View
    {
        $entitlement = $student->entitlement_code
            ? Entitlement::where('code', $student->entitlement_code)
                ->where('is_active', true)
                ->where('student_level', $student->student_level)
                ->with('items.item')
                ->first()
            : null;

        $receivedItems = DistributionItem::whereHas('transaction', fn ($q) => $q->where('student_id', $student->id)
        )
            ->with('item')
            ->get()
            ->groupBy(fn ($di) => $di->item->base_code ?? $di->item_id)
            ->map(fn ($items) => ['total_qty' => $items->sum('quantity')]);

        return view('master.student._entitlement', compact('student', 'entitlement', 'receivedItems'));
    }

    public function receivedItems(Student $student): View
    {
        $receivedItems = DistributionItem::whereHas('transaction', fn ($q) => $q->where('student_id', $student->id)
        )
            ->with(['item', 'transaction.schedule'])
            ->get()
            ->groupBy(fn ($di) => $di->item?->base_code ?? $di->item_id)
            ->map(fn ($items) => [
                'item' => $items->first()?->item,
                'total_qty' => $items->sum('quantity'),
                'details' => $items->map(fn ($di) => [
                    'quantity' => $di->quantity,
                    'schedule' => $di->transaction?->schedule?->name ?? '-',
                    'date' => $di->transaction?->pickup_time?->format('d/m/Y H:i:s') ?? '-',
                    'size' => $di->actual_size ?? $di->expected_size ?? '-',
                ]),
            ]);

        return view('master.student._received-items', compact('receivedItems'));
    }

    public function transactions(Student $student): View
    {
        $student->load([
            'distributionTransactions' => fn ($q) => $q->latest(),
            'distributionTransactions.items.item',
            'distributionTransactions.schedule',
        ]);

        return view('master.student._transactions', compact('student'));
    }

    public function edit(Student $student): View
    {
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();

        return view('master.student.edit', compact('student', 'studyPrograms'));
    }

    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        $this->studentService->update($student, $request->validated());

        return redirect()->route('students.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->studentService->destroy($student);

        return redirect()->route('students.index')->with('success', 'Mahasiswa berhasil dihapus.');
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

        $generated = [];

        foreach ($students as $student) {
            [$user, $password] = $this->studentService->generateAccount($student);
            $this->setCredentialPassword($student->nim, $password);
            $generated[] = [
                'name' => $student->name,
                'nim' => $student->nim,
                'password' => $password,
            ];
        }

        if (empty($generated)) {
            return redirect()->route('students.generate-index')
                ->with('info', 'Tidak ada akun baru yang digenerate.');
        }

        AuditService::log('generate_accounts', Student::class, implode(',', collect($students)->pluck('id')->all()), null, ['count' => count($generated)]);

        $message = 'Berhasil membuat '.count($generated).' akun mahasiswa. Distributed kredensial di halaman berikut.';

        return redirect()->route('students.credentials')
            ->with('success', $message);
    }

    public function export(Request $request): BinaryFileResponse
    {
        return (new StudentExport(
            search: $request->input('q'),
            studyProgramId: $request->input('study_program_id'),
            generationId: $request->input('generation_id'),
        ))->download('students-'.now()->format('Ymd').'.xlsx');
    }

    public function promoteForm(Request $request): View
    {
        $query = Student::with(['studyProgram.faculty', 'generation', 'studentLevel']);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()->paginate(20);
        $generations = StudentGeneration::orderBy('name')->get();
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();

        return view('master.student.promote', compact('students', 'generations', 'studyPrograms'));
    }

    public function promote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['required', 'integer', 'exists:students,id'],
            'target_generation_id' => ['nullable', 'integer', 'exists:student_generations,id'],
            'target_study_program_id' => ['nullable', 'integer', 'exists:study_programs,id'],
        ]);

        $count = $this->studentService->promoteStudents(
            $validated['student_ids'],
            $validated['target_generation_id'] ?? null,
            $validated['target_study_program_id'] ?? null,
        );

        return redirect()->route('students.index')
            ->with('success', "{$count} mahasiswa berhasil dipromosikan ke semester/level berikutnya.");
    }

    public function generateAll(Request $request): RedirectResponse
    {
        $students = Student::whereNull('user_id')->get();

        if ($students->isEmpty()) {
            return redirect()->route('students.generate-index')
                ->with('info', 'Semua mahasiswa sudah memiliki akun.');
        }

        $generated = [];

        foreach ($students as $student) {
            [$user, $password] = $this->studentService->generateAccount($student);
            $this->setCredentialPassword($student->nim, $password);
            $generated[] = [
                'name' => $student->name,
                'nim' => $student->nim,
                'password' => $password,
            ];
        }

        AuditService::log('generate_all_accounts', Student::class, implode(',', collect($students)->pluck('id')->all()), null, ['count' => count($generated)]);

        $message = 'Berhasil membuat '.count($generated).' akun mahasiswa. Distributed kredensial di halaman berikut.';

        return redirect()->route('students.credentials')
            ->with('success', $message);
    }

    public function showCredentials(): View
    {
        $students = Student::whereNotNull('user_id')
            ->whereHas('user', fn ($q) => $q->where('must_change_password', true))
            ->with(['studyProgram', 'generation', 'user'])
            ->orderBy('nim')
            ->get();

        $notificationService = app(NotificationService::class);
        $latestEmails = $notificationService->latestAccountNotificationsForStudents($students);

        $students = $students->map(function (Student $student) use ($latestEmails) {
            $student->temp_password = $this->credentialPassword($student->nim);
            $student->latest_email = $latestEmails[$student->id] ?? null;

            return $student;
        });

        $totalStudents = cache()->remember('student-count-total', 120, fn () => Student::count());
        $totalWithAccount = cache()->remember('student-count-with-account', 120, fn () => Student::whereNotNull('user_id')->count());
        $totalWithoutAccount = cache()->remember('student-count-without-account', 120, fn () => Student::whereNull('user_id')->count());

        return view('master.student.credentials', compact(
            'students',
            'totalStudents',
            'totalWithAccount',
            'totalWithoutAccount',
        ));
    }

    public function getPassword(Student $student): JsonResponse
    {
        abort_unless($this->canViewCredentials(), 403);

        $password = $this->credentialPassword($student->nim);

        AuditService::log(
            'get_password',
            Student::class,
            $student->id,
            null,
            ['nim' => $student->nim, 'revealed' => $password !== null],
        );

        return response()->json([
            'password' => $password,
        ]);
    }

    public function exportCredentials(): BinaryFileResponse
    {
        $students = Student::whereNotNull('user_id')
            ->whereHas('user', fn ($q) => $q->where('must_change_password', true))
            ->with(['studyProgram', 'user'])
            ->orderBy('nim')
            ->get();

        $passwords = [];
        foreach ($students as $student) {
            $password = $this->credentialPassword($student->nim);
            if ($password !== null) {
                $passwords[$student->nim] = $password;
            }
        }

        return (new CredentialsExport($students->all(), $passwords))
            ->download('kredensial-'.now()->format('Ymdhis').'.xlsx');
    }

    public function resetPassword(Student $student): RedirectResponse
    {
        if (! $student->user_id) {
            return redirect()->route('students.credentials')
                ->with('error', 'Mahasiswa belum memiliki akun.');
        }

        [$user, $password] = $this->studentService->resetPassword($student);

        $this->setCredentialPassword($student->nim, $password);

        AuditService::log('reset_password', Student::class, $student->id, null, ['nim' => $student->nim]);

        return redirect()->route('students.credentials')
            ->with('success', "Password untuk {$student->name} ({$student->nim}) berhasil di-reset.");
    }

    public function resendEmail(Student $student): RedirectResponse
    {
        if (! $student->user_id) {
            return redirect()->route('students.credentials')
                ->with('error', 'Mahasiswa belum memiliki akun.');
        }

        $password = $this->credentialPassword($student->nim);

        if (! $password) {
            return redirect()->route('students.credentials')
                ->with('warning', "Password sementara untuk {$student->nim} tidak tersedia. Reset password terlebih dahulu.");
        }

        $sent = app(NotificationService::class)->resendStudentAccount($student, $password);

        return redirect()->route('students.credentials')
            ->with($sent ? 'success' : 'error', $sent
                ? "Email kredensial untuk {$student->name} berhasil dikirim."
                : 'Email gagal dikirim. Periksa konfigurasi SMTP atau coba lagi.');
    }

    public function resendAllFailed(): RedirectResponse
    {
        $students = Student::whereNotNull('user_id')
            ->whereHas('user', fn ($q) => $q->where('must_change_password', true))
            ->get()
            ->filter(fn (Student $student) => $this->credentialPassword($student->nim) !== null);

        if ($students->isEmpty()) {
            return redirect()->route('students.credentials')
                ->with('info', 'Tidak ada kredensial yang tersedia untuk dikirim ulang.');
        }

        $sent = 0;
        $failed = 0;

        foreach ($students as $student) {
            $ok = app(NotificationService::class)->resendStudentAccount($student, $this->credentialPassword($student->nim));
            $ok ? $sent++ : $failed++;
        }

        return redirect()->route('students.credentials')
            ->with($failed ? 'warning' : 'success', "{$sent} email berhasil dikirim ulang".($failed ? ", {$failed} gagal." : '.'));
    }

    public function destroyCredentials(): RedirectResponse
    {
        session()->forget('credentials.passwords');

        return redirect()->route('students.credentials')
            ->with('success', 'Data kredensial sementara berhasil dibersihkan.');
    }

    private function canViewCredentials(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['super_admin', 'admin']);
    }

    private function credentialPassword(string $nim): ?string
    {
        $encrypted = session('credentials.passwords.'.$nim);

        if (! $encrypted) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable) {
            return null;
        }
    }

    private function setCredentialPassword(string $nim, string $password): void
    {
        session(['credentials.passwords.'.$nim => Crypt::encryptString($password)]);
    }

    private function forgetCredentialPassword(string $nim): void
    {
        session()->forget('credentials.passwords.'.$nim);
    }

    public function toggleStatus(Student $student): RedirectResponse
    {
        $newStatus = match ($student->status) {
            'active' => 'leave',
            'leave' => 'active',
            default => 'active',
        };

        $student->update(['status' => $newStatus]);

        $label = $newStatus === 'leave' ? 'Cuti' : 'Aktif';

        return redirect()->back()
            ->with('success', "Status mahasiswa {$student->name} ({$student->nim}) berhasil diubah menjadi {$label}.");
    }
}
