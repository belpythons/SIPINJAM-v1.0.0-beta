<?php

namespace App\Services\Whatsapp;

use App\Contracts\WhatsappDriver;
use App\Models\NotificationLog;
use App\Models\User;
use App\Support\PhoneNumber;
use App\Support\WhatsappResult;

/**
 * Titik masuk tunggal pengiriman WhatsApp.
 *
 * Tugasnya dua: memilih driver sesuai config, dan MENCATAT setiap percobaan ke
 * notification_logs — termasuk yang gagal. Tanpa catatan itu, kegagalan
 * pengiriman menjadi tidak terlihat, dan admin tidak pernah tahu peminjam
 * sebenarnya tidak pernah menerima pemberitahuan.
 */
class WhatsappManager
{
    public function __construct(private readonly WhatsappDriver $driver) {}

    /**
     * @param  array<string, mixed>  $payload  Data untuk jejak audit
     */
    public function kirim(
        ?User $user,
        string $nomor,
        string $pesan,
        string $template = 'ad-hoc',
        array $payload = [],
    ): WhatsappResult {
        $tujuan = PhoneNumber::normalize($nomor);

        if ($tujuan === null) {
            $hasil = WhatsappResult::gagal("Nomor tujuan tidak valid: {$nomor}");
            $this->catat($user, $template, $payload, $hasil);

            return $hasil;
        }

        $hasil = $this->driver->send($tujuan, $pesan);

        $this->catat($user, $template, $payload, $hasil);

        return $hasil;
    }

    public function driver(): WhatsappDriver
    {
        return $this->driver;
    }

    /**
     * Catat percobaan ke notification_logs.
     *
     * Payload SENGAJA tidak memuat nomor penuh maupun isi pesan — keduanya data
     * pribadi, dan tabel log bukan tempatnya (UU PDP).
     */
    private function catat(?User $user, string $template, array $payload, WhatsappResult $hasil): void
    {
        NotificationLog::create([
            'user_id' => $user?->id,
            'kanal' => NotificationLog::KANAL_WA,
            'template' => $template,
            'payload' => array_merge($payload, ['driver' => $this->driver->nama()]),
            'status' => $hasil->berhasil ? NotificationLog::STATUS_TERKIRIM : NotificationLog::STATUS_GAGAL,
            'error' => $hasil->error,
            'sent_at' => $hasil->berhasil ? now() : null,
        ]);
    }
}
