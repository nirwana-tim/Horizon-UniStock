<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Models\DistributionItem;
use App\Models\Entitlement;
use App\Models\ProgramLevel;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Services\Master\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(
        protected StudentService $studentService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = Student::with(['studyProgram', 'programLevel']);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('email_kampus', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('master.student._table', compact('students'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $students])->render(),
            ]);
        }

        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();
        $programLevels = ProgramLevel::orderBy('name')->get();

        $studentsWithoutAccount = Student::whereNull('user_id')
            ->with(['studyProgram', 'programLevel'])
            ->paginate(10, ['*'], 'account_page');

        $totalStudents = Student::count();
        $totalWithAccount = Student::whereNotNull('user_id')->count();
        $totalWithoutAccount = Student::whereNull('user_id')->count();

        return view('master.student.index', compact(
            'students',
            'studyPrograms',
            'programLevels',
            'studentsWithoutAccount',
            'totalStudents',
            'totalWithAccount',
            'totalWithoutAccount',
        ));
    }

    public function create(): View
    {
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();
        $programLevels = ProgramLevel::orderBy('name')->get();

        return view('master.student.create', compact('studyPrograms', 'programLevels'));
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        $this->studentService->store($request->validated());

        return redirect()->route('students.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show(Student $student): View
    {
        $student->load([
            'studyProgram.faculty',
            'programLevel',
            'user',
            'distributionTransactions' => fn($q) => $q->latest(),
            'distributionTransactions.items.item',
            'distributionTransactions.schedule',
        ]);

        $entitlement = $student->entitlement_code
            ? Entitlement::where('code', $student->entitlement_code)
                ->where('is_active', true)
                ->with('items.item')
                ->first()
            : null;

        // Group received items by base_code (product group) to match entitlement items
        $receivedItems = DistributionItem::whereHas('transaction', fn($q) =>
            $q->where('student_id', $student->id)
        )
            ->with(['item', 'transaction.schedule'])
            ->get()
            ->groupBy(fn($di) => $di->item->base_code ?? $di->item_id)
            ->map(fn($items) => [
                'item' => $items->first()->item,
                'total_qty' => $items->sum('quantity'),
                'details' => $items->map(fn($di) => [
                    'quantity' => $di->quantity,
                    'schedule' => $di->transaction?->schedule?->name ?? '-',
                    'date' => $di->transaction?->pickup_time?->format('d/m/Y H:i:s') ?? '-',
                    'size' => $di->actual_size ?? $di->expected_size ?? '-',
                ]),
            ]);

        return view('master.student.show', compact('student', 'entitlement', 'receivedItems'));
    }

    public function edit(Student $student): View
    {
        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();
        $programLevels = ProgramLevel::orderBy('name')->get();

        return view('master.student.edit', compact('student', 'studyPrograms', 'programLevels'));
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
            $generated[] = "{$student->name} (NIM: {$student->nim}) -> Password: <strong>{$password}</strong>";
        }

        if (empty($generated)) {
            return redirect()->route('students.index', ['tab' => 'generate-akun'])
                ->with('info', 'Tidak ada akun baru yang digenerate.');
        }

        $message = "Berhasil membuat " . count($generated) . " akun mahasiswa:<br>" . implode('<br>', $generated);

        return redirect()->route('students.index', ['tab' => 'generate-akun'])
            ->with('success', $message);
    }

    public function generateAll(Request $request): RedirectResponse
    {
        $students = Student::whereNull('user_id')->get();

        if ($students->isEmpty()) {
            return redirect()->route('students.index', ['tab' => 'generate-akun'])
                ->with('info', 'Semua mahasiswa sudah memiliki akun.');
        }

        $generated = [];

        foreach ($students as $student) {
            [$user, $password] = $this->studentService->generateAccount($student);
            $generated[] = "{$student->name} (NIM: {$student->nim}) -> Password: <strong>{$password}</strong>";
        }

        $message = "Berhasil membuat " . count($generated) . " akun mahasiswa:<br>" . implode('<br>', $generated);

        return redirect()->route('students.index', ['tab' => 'generate-akun'])
            ->with('success', $message);
    }
}
