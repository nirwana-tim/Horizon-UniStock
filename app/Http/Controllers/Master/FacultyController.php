<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyRequest;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FacultyController extends Controller
{
    public function index(): View
    {
        $faculties = Faculty::withCount('studyPrograms')->latest()->paginate(15);

        return view('master.faculty.index', compact('faculties'));
    }

    public function create(): View
    {
        return view('master.faculty.create');
    }

    public function store(FacultyRequest $request): RedirectResponse
    {
        Faculty::create($request->validated());

        return redirect()->route('master.faculty.index')->with('success', 'Fakultas berhasil ditambahkan.');
    }

    public function show(Faculty $faculty): View
    {
        $faculty->load('studyPrograms');

        return view('master.faculty.show', compact('faculty'));
    }

    public function edit(Faculty $faculty): View
    {
        return view('master.faculty.edit', compact('faculty'));
    }

    public function update(FacultyRequest $request, Faculty $faculty): RedirectResponse
    {
        $faculty->update($request->validated());

        return redirect()->route('master.faculty.index')->with('success', 'Fakultas berhasil diperbarui.');
    }

    public function destroy(Faculty $faculty): RedirectResponse
    {
        $faculty->delete();

        return redirect()->route('master.faculty.index')->with('success', 'Fakultas berhasil dihapus.');
    }
}
