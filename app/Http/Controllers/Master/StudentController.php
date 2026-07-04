<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Models\ProgramLevel;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Services\Master\StudentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(
        protected StudentService $studentService
    ) {}

    public function index(): View
    {
        $students = Student::with(['studyProgram', 'programLevel'])
            ->latest()
            ->paginate(20);

        $studyPrograms = StudyProgram::with('faculty')->orderBy('name')->get();
        $programLevels = ProgramLevel::orderBy('name')->get();

        return view('master.student.index', compact('students', 'studyPrograms', 'programLevels'));
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

        return redirect()->route('master.student.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show(Student $student): View
    {
        $student->load(['studyProgram.faculty', 'programLevel', 'user', 'distributionTransactions.items.item']);

        return view('master.student.show', compact('student'));
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

        return redirect()->route('master.student.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->studentService->destroy($student);

        return redirect()->route('master.student.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
