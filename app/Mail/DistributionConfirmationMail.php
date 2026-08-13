<?php

namespace App\Mail;

use App\Models\DistributionTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DistributionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly DistributionTransaction $transaction,
    ) {}

    public function envelope(): Envelope
    {
        $student = $this->transaction->student;

        return new Envelope(
            subject: 'Konfirmasi Pengambilan Seragam — '.$student->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.distribution-confirmation',
        );
    }
}
