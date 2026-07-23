<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentGenerationRequest;
use App\Models\StudentGeneration;
use App\Services\Master\StudentGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentGenerationController extends Controller
{
    public function __construct(
        protected StudentGenerationService $studentGenerationService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = StudentGeneration::withCount('students');

        if ($search = $request->input('q')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $generations = $query->orderBy('code')->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('master.student-generation._table', compact('generations'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $generations])->render(),
            ]);
        }

        return view('master.student-generation.index', compact('generations'));
    }

    public function create(): View
    {
        return view('master.student-generation.create');
    }

    public function store(StudentGenerationRequest $request): RedirectResponse
    {
        $this->studentGenerationService->store($request->validated());

        return redirect()->route('master-data.student-generation.index')->with('success', 'Generasi mahasiswa berhasil ditambahkan.');
    }

    public function show(StudentGeneration $studentGeneration): View
    {
        $studentGeneration->loadCount('students');

        return view('master.student-generation.show', ['generation' => $studentGeneration]);
    }

    public function edit(StudentGeneration $studentGeneration): View
    {
        return view('master.student-generation.edit', ['generation' => $studentGeneration]);
    }

    public function update(StudentGenerationRequest $request, StudentGeneration $studentGeneration): RedirectResponse
    {
        $this->studentGenerationService->update($studentGeneration, $request->validated());

        return redirect()->route('master-data.student-generation.index')->with('success', 'Generasi mahasiswa berhasil diperbarui.');
    }

    public function destroy(StudentGeneration $studentGeneration): RedirectResponse
    {
        $this->studentGenerationService->destroy($studentGeneration);

        return redirect()->route('master-data.student-generation.index')->with('success', 'Generasi mahasiswa berhasil dihapus.');
    }
}
