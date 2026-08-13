<?php

namespace App\Mail;

use App\Models\DistributionSchedule;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DistributionScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Student $student,
        public readonly DistributionSchedule $schedule,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Jadwal Pengambilan Seragam — '.$this->schedule->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.distribution-schedule',
        );
    }
}
