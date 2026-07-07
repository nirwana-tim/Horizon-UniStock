<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramLevelRequest;
use App\Models\ProgramLevel;
use App\Services\Master\ProgramLevelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramLevelController extends Controller
{
    public function __construct(
        protected ProgramLevelService $programLevelService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = ProgramLevel::withCount('students');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $levels = $query->orderBy('code')->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('master.program-level._table', compact('levels'))->render(),
            ]);
        }

        return view('master.program-level.index', compact('levels'));
    }

    public function create(): View
    {
        return view('master.program-level.create');
    }

    public function store(ProgramLevelRequest $request): RedirectResponse
    {
        $this->programLevelService->store($request->validated());

        return redirect()->route('master-data.program-level.index')->with('success', 'Level program berhasil ditambahkan.');
    }

    public function show(ProgramLevel $programLevel): View
    {
        $programLevel->loadCount('students');

        return view('master.program-level.show', ['level' => $programLevel]);
    }

    public function edit(ProgramLevel $programLevel): View
    {
        return view('master.program-level.edit', ['level' => $programLevel]);
    }

    public function update(ProgramLevelRequest $request, ProgramLevel $programLevel): RedirectResponse
    {
        $this->programLevelService->update($programLevel, $request->validated());

        return redirect()->route('master-data.program-level.index')->with('success', 'Level program berhasil diperbarui.');
    }

    public function destroy(ProgramLevel $programLevel): RedirectResponse
    {
        $this->programLevelService->destroy($programLevel);

        return redirect()->route('master-data.program-level.index')->with('success', 'Level program berhasil dihapus.');
    }
}
