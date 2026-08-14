<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    /**
     * Registrasi publik dinonaktifkan. Akun dibuat via admin / impor mahasiswa.
     */
    public function create(): never
    {
        abort(404);
    }

    /**
     * Registrasi publik dinonaktifkan. Akun dibuat via admin / impor mahasiswa.
     */
    public function store(Request $request): never
    {
        abort(404);
    }
}
