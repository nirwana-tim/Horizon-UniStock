<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordStudentController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password-student');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nim' => ['required', 'string', 'exists:students,nim'],
        ]);

        $student = Student::where('nim', $validated['nim'])->first();

        if (!$student->email_kampus) {
            return back()->withErrors(['nim' => 'Email kampus belum didaftarkan. Silakan isi email kampus terlebih dahulu.']);
        }

        if (!$student->user) {
            return back()->withErrors(['nim' => 'Akun mahasiswa belum di-generate. Hubungi admin.']);
        }

        $status = Password::sendResetLink(['email' => $student->user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('login')
                ->with('status', 'Link reset password telah dikirim ke email kampus Anda.');
        }

        return back()->withErrors(['nim' => 'Gagal mengirim email reset. Coba lagi nanti.']);
    }
}
