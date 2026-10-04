<?php

namespace App\Mail;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Peminjaman $peminjaman,
        public readonly string $statusBaru
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->statusBaru === 'disetujui' ? 'Disetujui ✅' : 'Ditolak ❌';

        return new Envelope(
            subject: '[SIPINJAM] Peminjaman #' . $this->peminjaman->id . ' — ' . $label,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-status',
        );
    }
}
