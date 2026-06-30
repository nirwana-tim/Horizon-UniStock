<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudyProgramRequest;
use App\Models\Faculty;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudyProgramController extends Controller
{
    public function index(): View
    {
        $programs = StudyProgram::with('faculty')->latest()->paginate(15);

        return view('master.study-program.index', compact('programs'));
    }

    public function create(): View
    {
        $faculties = Faculty::orderBy('name')->get();

        return view('master.study-program.create', compact('faculties'));
    }

    public function store(StudyProgramRequest $request): RedirectResponse
    {
        StudyProgram::create($request->validated());

        return redirect()->route('master.study-program.index')->with('success', 'Program studi berhasil ditambahkan.');
    }

    public function show(StudyProgram $program): View
    {
        $program->load(['faculty', 'students']);

        return view('master.study-program.show', compact('program'));
    }

    public function edit(StudyProgram $program): View
    {
        $faculties = Faculty::orderBy('name')->get();

        return view('master.study-program.edit', compact('program', 'faculties'));
    }

    public function update(StudyProgramRequest $request, StudyProgram $program): RedirectResponse
    {
        $program->update($request->validated());

        return redirect()->route('master.study-program.index')->with('success', 'Program studi berhasil diperbarui.');
    }

    public function destroy(StudyProgram $program): RedirectResponse
    {
        $program->delete();

        return redirect()->route('master.study-program.index')->with('success', 'Program studi berhasil dihapus.');
    }
}
