<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Student $student,
        public readonly string $password,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akun '.config('app.name', 'Horizon').' Anda Sudah Siap',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student-account',
        );
    }
}
