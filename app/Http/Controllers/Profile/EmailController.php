<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\Student;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailController extends Controller
{
    public function showChangeForm(): View
    {
        return view('profile.email.verify-password');
    }

    public function verifyPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        return redirect()->route('profile.email.input-email');
    }

    public function showEmailForm(): View
    {
        return view('profile.email.input-email');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email_pribadi' => ['required', 'email', 'max:255'],
        ]);

        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        if ($request->input('email_pribadi') === $student->email_pribadi) {
            return back()->withErrors(['email_pribadi' => 'Email sama dengan email yang sudah tersimpan.']);
        }

        OtpCode::where('user_id', $user->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->update(['used_at' => now()]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id' => $user->id,
            'email' => $request->input('email_pribadi'),
            'code' => $code,
            'type' => 'email_pribadi_change',
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::raw(
                "Kode verifikasi email pribadi Anda: $code\n\nKode berlaku 10 menit.\n\nJika ini bukan Anda, abaikan email ini.",
                function ($message) use ($request) {
                    $message->to($request->input('email_pribadi'))
                        ->subject('Verifikasi Email Pribadi - UniStock');
                }
            );
        } catch (\Exception $e) {
            return back()->with('warning', 'Gagal mengirim email verifikasi. Coba lagi nanti.');
        }

        session(['pending_email_pribadi' => $request->input('email_pribadi')]);

        return redirect()->route('profile.email.verify-otp')
            ->with('success', 'Kode OTP telah dikirim ke email baru Anda.');
    }

    public function showOtpForm(): View
    {
        return view('profile.email.verify-otp', [
            'email' => session('pending_email_pribadi'),
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();
        $pendingEmail = session('pending_email_pribadi');

        if (!$pendingEmail) {
            return redirect()->route('profile.email.change')
                ->withErrors(['error' => 'Sesi tidak valid. Silakan mulai ulang.']);
        }

        $attempts = (int) session('otp_attempts', 0);
        if ($attempts >= 5) {
            session()->forget(['pending_email_pribadi', 'otp_attempts']);
            OtpCode::where('user_id', $user->id)->whereNull('used_at')->update(['used_at' => now()]);
            return redirect()->route('profile.email.change')
                ->withErrors(['error' => 'Terlalu banyak percobaan. Silakan kirim ulang OTP.']);
        }

        $otp = OtpCode::where('user_id', $user->id)
            ->where('email', $pendingEmail)
            ->where('code', $request->input('code'))
            ->where('type', 'email_pribadi_change')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            session(['otp_attempts' => $attempts + 1]);
            return back()->withErrors(['code' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);
        }

        $otp->update(['used_at' => now()]);
        session()->forget(['pending_email_pribadi', 'otp_attempts']);

        $oldEmail = $student->email_pribadi;

        $student->update(['email_pribadi' => $pendingEmail]);

        AuditService::log(
            'email_pribadi.updated',
            Student::class,
            $student->id,
            ['email_pribadi' => $oldEmail],
            ['email_pribadi' => $pendingEmail]
        );

        try {
            Mail::raw(
                "Email pribadi akun UniStock Anda telah diubah dari {$oldEmail} menjadi {$pendingEmail}.\n\nJika ini bukan Anda, segera hubungi admin.",
                function ($message) use ($oldEmail) {
                    $message->to($oldEmail)
                        ->subject('Pemberitahuan Perubahan Email - UniStock');
                }
            );
        } catch (\Exception $e) {
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Email pribadi berhasil diperbarui.');
    }
}
