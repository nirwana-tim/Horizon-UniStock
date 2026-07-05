<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramLevelRequest;
use App\Models\ProgramLevel;
use App\Services\Master\ProgramLevelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProgramLevelController extends Controller
{
    public function __construct(
        protected ProgramLevelService $programLevelService
    ) {}

    public function index(): View
    {
        $levels = ProgramLevel::withCount('students')->latest()->paginate(15);

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
