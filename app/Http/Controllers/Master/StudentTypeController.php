<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\StudentType;
use Illuminate\View\View;

class StudentTypeController extends Controller
{
    public function index(): View
    {
        $studentTypes = StudentType::orderBy('kode')->get();

        return view('master.student-type.index', compact('studentTypes'));
    }

    public function show(StudentType $studentType): View
    {
        return view('master.student-type.show', compact('studentType'));
    }
}
