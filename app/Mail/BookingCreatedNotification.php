<?php

namespace App\Mail;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCreatedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Peminjaman $peminjaman
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[SIPINJAM] Peminjaman Baru #' . $this->peminjaman->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-created',
        );
    }
}
