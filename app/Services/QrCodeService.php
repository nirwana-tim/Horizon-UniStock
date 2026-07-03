<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generateToken(Student $student): string
    {
        if ($student->qr_token) {
            return $student->qr_token;
        }

        $token = (string) Str::uuid();

        $student->update([
            'qr_token' => $token,
            'qr_generated_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? $student->user_id,
            'action' => 'generate_qr',
            'model_type' => Student::class,
            'model_id' => $student->id,
            'new_values' => ['qr_token' => $token, 'qr_generated_at' => now()->toDateTimeString()],
            'ip_address' => request()->ip(),
        ]);

        return $token;
    }

    public function getQrSvg(Student $student, int $size = 200): string
    {
        if (!$student->qr_token) {
            $this->generateToken($student);
        }

        return QrCode::size($size)
            ->color(38, 38, 38)
            ->backgroundColor(255, 255, 255)
            ->generate($student->qr_token);
    }

    public function validateScan(string $qrToken): ?Student
    {
        return Student::where('qr_token', $qrToken)->first();
    }

    public function hasQr(Student $student): bool
    {
        return !is_null($student->qr_token);
    }

    public function getQrDataUrl(Student $student, int $size = 200): string
    {
        $svg = $this->getQrSvg($student, $size);
        $base64 = base64_encode($svg);

        return 'data:image/svg+xml;base64,' . $base64;
    }
}
