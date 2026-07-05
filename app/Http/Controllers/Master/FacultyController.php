<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyRequest;
use App\Models\Faculty;
use App\Services\Master\FacultyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FacultyController extends Controller
{
    public function __construct(
        protected FacultyService $facultyService
    ) {}

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
        $this->facultyService->store($request->validated());

        return redirect()->route('master-data.faculty.index')->with('success', 'Fakultas berhasil ditambahkan.');
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
        $this->facultyService->update($faculty, $request->validated());

        return redirect()->route('master-data.faculty.index')->with('success', 'Fakultas berhasil diperbarui.');
    }

    public function destroy(Faculty $faculty): RedirectResponse
    {
        $this->facultyService->destroy($faculty);

        return redirect()->route('master-data.faculty.index')->with('success', 'Fakultas berhasil dihapus.');
    }
}
