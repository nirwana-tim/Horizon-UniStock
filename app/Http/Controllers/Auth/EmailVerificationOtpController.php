<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailVerificationOtpController extends Controller
{
    public function sendOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email_kampus' => ['required', 'email', 'max:255'],
        ]);

        $student = Student::where('user_id', Auth::id())->firstOrFail();

        OtpCode::where('nim', $student->nim)->whereNull('used_at')->where('expires_at', '>', now())->update(['used_at' => now()]);

        session()->forget(['pending_email', 'otp_attempts']);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'nim' => $student->nim,
            'email' => $validated['email_kampus'],
            'code' => $code,
            'type' => 'email_verification',
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::raw("Kode verifikasi email kampus Anda: $code\n\nKode berlaku 10 menit.", function ($message) use ($validated) {
                $message->to($validated['email_kampus'])
                    ->subject('Verifikasi Email Kampus - UniStock');
            });
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('warning', 'Gagal mengirim email verifikasi. Coba lagi nanti.');
        }

        session(['pending_email' => $validated['email_kampus']]);

        return redirect()->route('student.email.verify-form')
            ->with('success', 'Kode OTP telah dikirim ke email kampus Anda.');
    }

    public function showVerifyForm(): View
    {
        if (!session('pending_email')) {
            return view('auth.verify-email-otp', ['email' => '']);
        }

        return view('auth.verify-email-otp', ['email' => session('pending_email')]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $pendingEmail = session('pending_email');

        if (!$pendingEmail) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'Session tidak valid. Silakan mulai ulang.']);
        }

        $attempts = (int) session('otp_attempts', 0);
        if ($attempts >= 5) {
            session()->forget(['pending_email', 'otp_attempts']);
            OtpCode::where('nim', $student->nim)->whereNull('used_at')->update(['used_at' => now()]);
            return redirect()->route('student.email.send-otp')
                ->withErrors(['error' => 'Terlalu banyak percobaan. Silakan kirim ulang OTP.']);
        }

        $otp = OtpCode::where('nim', $student->nim)
            ->where('email', $pendingEmail)
            ->where('code', $validated['code'])
            ->where('type', 'email_verification')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            session(['otp_attempts' => $attempts + 1]);
            return back()->withErrors(['code' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);
        }

        session()->forget(['pending_email', 'otp_attempts']);

        $otp->update(['used_at' => now()]);

        $student->update([
            'email_kampus' => $pendingEmail,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('email_success', 'Email kampus berhasil diverifikasi!');
    }
}
