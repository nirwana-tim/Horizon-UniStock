<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\StudentLevel;
use Illuminate\View\View;

class StudentLevelController extends Controller
{
    public function index(): View
    {
        $studentLevels = StudentLevel::orderBy('kode')->get();

        return view('master.student-level.index', compact('studentLevels'));
    }

    public function show(StudentLevel $studentLevel): View
    {
        return view('master.student-level.show', compact('studentLevel'));
    }
}
