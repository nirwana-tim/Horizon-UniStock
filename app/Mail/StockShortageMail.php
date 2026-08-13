<?php

namespace App\Mail;

use App\Models\DistributionTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockShortageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly DistributionTransaction $transaction,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Peringatan Stok Kurang — '.$this->transaction->schedule?->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.stock-shortage',
        );
    }
}
