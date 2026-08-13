<?php

namespace App\Services;

use App\Mail\DistributionConfirmationMail;
use App\Mail\DistributionScheduleMail;
use App\Mail\StockShortageMail;
use App\Mail\StudentAccountMail;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\EmailNotification;
use App\Models\SmtpSetting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendStudentAccount(Student $student, string $password, bool $resend = false): bool
    {
        $email = $student->resolveNotificationEmail();

        if (! SmtpSetting::isActiveConfigured()) {
            EmailNotification::create([
                'student_id' => $student->id,
                'type' => $resend ? 'resend_account' : 'student_account',
                'status' => 'skipped',
                'error_message' => 'SMTP belum dikonfigurasi',
            ]);

            return false;
        }

        if (! $email) {
            EmailNotification::create([
                'student_id' => $student->id,
                'type' => $resend ? 'resend_account' : 'student_account',
                'status' => 'failed',
                'error_message' => 'Email tidak tersedia',
            ]);

            Log::warning('Tidak ada email untuk mahasiswa', ['student_id' => $student->id]);

            return false;
        }

        $notification = EmailNotification::create([
            'student_id' => $student->id,
            'type' => $resend ? 'resend_account' : 'student_account',
            'status' => 'pending',
        ]);

        try {
            Mail::to($email)->send(new StudentAccountMail($student, $password));

            $notification->update(['status' => 'sent', 'sent_at' => now()]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email akun mahasiswa', [
                'student_id' => $student->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            $notification->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            return false;
        }
    }

    public function notifyScheduleCreated(DistributionSchedule $schedule): void
    {
        $students = Student::query()
            ->whereNotNull('user_id')
            ->where(fn ($q) => $q->whereNotNull('email_kampus')->orWhereNotNull('email_pribadi'))
            ->get()
            ->filter(fn (Student $student) => DistributionSchedule::whereKey($schedule->id)
                ->forStudent($student)
                ->exists());

        foreach ($students as $student) {
            $email = $student->resolveNotificationEmail();
            $notification = EmailNotification::create([
                'student_id' => $student->id,
                'schedule_id' => $schedule->id,
                'type' => 'schedule_created',
            ]);

            if (! $email) {
                $notification->update(['status' => 'failed', 'error_message' => 'Email tidak tersedia']);

                continue;
            }

            try {
                Mail::to($email)->send(new DistributionScheduleMail($student, $schedule));
                $notification->update(['status' => 'sent', 'sent_at' => now()]);
            } catch (\Throwable $e) {
                Log::error('Gagal kirim notifikasi jadwal', [
                    'student_id' => $student->id,
                    'schedule_id' => $schedule->id,
                    'error' => $e->getMessage(),
                ]);
                $notification->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            }
        }
    }

    public function sendDistributionConfirmation(DistributionTransaction $transaction): void
    {
        $student = $transaction->student;
        $email = $student->resolveNotificationEmail();

        if (! $email) {
            Log::warning('Tidak ada email untuk kirim konfirmasi', ['student_id' => $student->id]);

            return;
        }

        $notification = EmailNotification::create([
            'student_id' => $student->id,
            'schedule_id' => $transaction->schedule_id,
            'type' => 'distribution_confirmation',
        ]);

        try {
            Mail::to($email)->send(new DistributionConfirmationMail($transaction));
            $notification->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('Gagal kirim konfirmasi distribusi', [
                'student_id' => $student->id,
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            $notification->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }

    public function resendStudentAccount(Student $student, string $password): bool
    {
        return $this->sendStudentAccount($student, $password, resend: true);
    }

    public function sendShortageAlert(DistributionTransaction $transaction): void
    {
        if (! SmtpSetting::isActiveConfigured()) {
            return;
        }

        $admins = User::role(['super_admin', 'admin'])->get();

        foreach ($admins as $admin) {
            if (! $admin->email) {
                continue;
            }

            try {
                Mail::to($admin->email)->send(new StockShortageMail($transaction));
            } catch (\Throwable $e) {
                Log::error('Gagal kirim peringatan stok kurang', [
                    'transaction_id' => $transaction->id,
                    'admin_email' => $admin->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function latestAccountNotification(Student $student): ?EmailNotification
    {
        return $student->emailNotifications()
            ->whereIn('type', ['student_account', 'resend_account'])
            ->latest('id')
            ->first();
    }
}
