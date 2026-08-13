<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\SmtpSetting;
use App\Services\MailSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SmtpSettingController extends Controller
{
    public function show(): View
    {
        $s = SmtpSetting::firstOrCreate(
            ['id' => 1],
            [
                'mailer'       => config('mail.default', 'smtp'),
                'from_address' => config('mail.from.address', 'hello@example.com'),
                'from_name'    => config('mail.from.name', config('app.name')),
                'is_active'    => true,
                'created_by'   => Auth::id(),
            ]
        );

        return view('system.smtp-settings', [
            'settings'   => $s,
            'mailers'    => MailSettingsService::mailers(),
            'apiDrivers' => MailSettingsService::apiDrivers(),
            'hasOtp'     => (bool) session('smtp.otp'),
            'hasPending' => (bool) session('smtp.pending_verified'),
        ]);
    }

    /**
     * Send a 4-digit OTP to the target email using the (unsaved) form config.
     */
    public function test(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'to_email' => ['required', 'email'],
        ]);

        // Build config from FORM values (falls back to DB secrets if blank)
        $existing = SmtpSetting::current();
        $data     = MailSettingsService::buildFromRequest($request, $existing);

        // Only real delivery mailers can be verified via OTP
        if (! MailSettingsService::isVerifiable($data['mailer'])) {
            return $this->otpError('Mailer "'.$data['mailer'].'" tidak mengirim email nyata, sehingga tidak bisa diverifikasi dengan OTP.', $request);
        }

        $hash = MailSettingsService::configHash($data);

        $payload = session('smtp.otp');

        // Cooldown gate for resend
        if ($payload && isset($payload['sent_at'])) {
            if (! MailSettingsService::canResend((int) $payload['sent_at'])) {
                $wait = 30 - (now()->getTimestamp() - (int) $payload['sent_at']);
                return $this->otpError("Tunggu {$wait} detik sebelum mengirim ulang.", $request);
            }
        }

        // Apply config temporarily
        MailSettingsService::apply($data);
        Mail::forgetMailers();

        $code = MailSettingsService::generateOtp();
        $to   = $validated['to_email'];

        try {
            Mail::raw(
                "Kode OTP verifikasi SMTP Anda adalah: {$code}\n\nKode berlaku 5 menit.\nJangan bagikan kode ini kepada siapa pun.",
                fn ($m) => $m->to($to)->subject('Kode OTP Verifikasi SMTP - UniStock')
            );

            session([
                'smtp.otp' => [
                    'code_hash'   => MailSettingsService::otpHashed($code),
                    'expires_at'  => now()->addMinutes(5)->getTimestamp(),
                    'config_hash' => $hash,
                    'config'      => $data,
                    'to_email'    => $to,
                    'sent_at'     => now()->getTimestamp(),
                ],
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'ok'      => true,
                    'message' => 'Kode OTP terkirim ke '.$to,
                    'to_email' => $to,
                ]);
            }

            return back()->with('otp_sent', true)->with('otp_email', $to);
        } catch (\Throwable $e) {
            session()->forget('smtp.otp');

            \Illuminate\Support\Facades\Log::error('SMTP OTP test gagal', [
                'mailer' => $data['mailer'] ?? null,
                'host'   => $data['host'] ?? null,
                'port'   => $data['port'] ?? null,
                'to'     => $to,
                'from'   => $data['from_address'] ?? null,
                'error'  => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->otpError('Gagal mengirim kode: '.$e->getMessage(), $request);
            }

            return back()->withErrors(['otp' => 'Gagal mengirim kode: '.$e->getMessage()]);
        }
    }

    /**
     * Verify the OTP and stage the config as a non-active pending candidate.
     */
    public function verify(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'otp_code' => ['required', 'string', 'size:4', 'regex:/^[0-9]{4}$/'],
        ]);

        $payload = session('smtp.otp');

        if (! $payload) {
            return $this->otpError('Kirim kode OTP terlebih dahulu.', $request);
        }

        // Expired
        if ((int) $payload['expires_at'] < now()->getTimestamp()) {
            session()->forget('smtp.otp');
            return $this->otpError('Kode OTP telah kedaluwarsa. Kirim ulang.', $request);
        }

        // Wrong code (constant-time)
        $expected = $payload['code_hash'];
        $provided = MailSettingsService::otpHashed($validated['otp_code']);
        if (! hash_equals($expected, $provided)) {
            return $this->otpError('Kode OTP salah. Periksa kembali atau kirim ulang.', $request);
        }

        // Stage the exact config that was tested (NOT active yet)
        session([
            'smtp.pending_config'   => $payload['config'] ?? [],
            'smtp.pending_verified' => true,
        ]);
        session()->forget('smtp.otp');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok'      => true,
                'message' => 'OTP benar. Klik "Simpan & Aktifkan" untuk memakai konfigurasi baru.',
            ]);
        }

        return back()->with('otp_verified', true)->with('otp_email', $payload['to_email']);
    }

    /**
     * Persist the verified pending candidate as the active config.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $pending = session('smtp.pending_config');

        if (! $pending) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Verifikasi OTP dulu sebelum menyimpan. Klik "Uji Koneksi", kirim kode, lalu verifikasi.',
                ], 422);
            }

            return back()->withErrors([
                'test' => 'Verifikasi OTP dulu sebelum menyimpan. Klik "Uji Koneksi", kirim kode, lalu verifikasi.',
            ])->withInput();
        }

        // Save
        $s = SmtpSetting::write($pending, Auth::id());

        $s->refresh();
        MailSettingsService::apply($s->toArray());
        Mail::forgetMailers();

        // Clear staged candidate
        session()->forget(['smtp.pending_config', 'smtp.pending_verified']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok'      => true,
                'message' => 'Konfigurasi SMTP berhasil diaktifkan.',
                'config'  => [
                    'mailer'       => $s->mailer,
                    'host'         => $s->host,
                    'port'         => $s->port,
                    'from_address' => $s->from_address,
                    'from_name'    => $s->from_name,
                    'is_active'    => (bool) $s->is_active,
                ],
            ]);
        }

        return back()->with('success', 'Konfigurasi SMTP berhasil diaktifkan.');
    }

    /**
     * Helper for OTP-level errors (non-validation), kept consistent for JSON/AJAX.
     */
    private function otpError(string $message, Request $request, int $status = 422): RedirectResponse|JsonResponse
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => false, 'message' => $message], $status);
        }

        return back()->withErrors(['otp' => $message])->withInput();
    }
}