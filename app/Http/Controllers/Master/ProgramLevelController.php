<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramLevelRequest;
use App\Models\ProgramLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProgramLevelController extends Controller
{
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
        ProgramLevel::create($request->validated());

        return redirect()->route('master.program-level.index')->with('success', 'Level program berhasil ditambahkan.');
    }

    public function show(ProgramLevel $level): View
    {
        $level->load('students');

        return view('master.program-level.show', compact('level'));
    }

    public function edit(ProgramLevel $level): View
    {
        return view('master.program-level.edit', compact('level'));
    }

    public function update(ProgramLevelRequest $request, ProgramLevel $level): RedirectResponse
    {
        $level->update($request->validated());

        return redirect()->route('master.program-level.index')->with('success', 'Level program berhasil diperbarui.');
    }

    public function destroy(ProgramLevel $level): RedirectResponse
    {
        $level->delete();

        return redirect()->route('master.program-level.index')->with('success', 'Level program berhasil dihapus.');
    }
}
