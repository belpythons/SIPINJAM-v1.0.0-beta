<?php

namespace App\Services\Whatsapp;

use App\Contracts\WhatsappDriver;
use App\Support\WhatsappResult;
use Illuminate\Support\Facades\Http;

/**
 * Gateway Fonnte — yang dijanjikan README sejak awal tetapi tidak pernah ada
 * satu baris kodenya (B-12).
 *
 * Retry TIDAK dilakukan di sini, melainkan di level job (SendWhatsappNotification):
 * mengulang di dalam request HTTP akan menahan worker dan menyembunyikan
 * kegagalan dari notification_logs.
 */
class FonnteDriver implements WhatsappDriver
{
    private const ENDPOINT = 'https://api.fonnte.com/send';

    public function send(string $to, string $message, array $opts = []): WhatsappResult
    {
        $token = config('services.fonnte.token');

        if (empty($token)) {
            return WhatsappResult::gagal('Token Fonnte belum dikonfigurasi (services.fonnte.token).');
        }

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->timeout((int) config('services.fonnte.timeout', 10))
                ->asForm()
                ->post(self::ENDPOINT, [
                    // Fonnte menerima 62xxx tanpa tanda plus.
                    'target' => ltrim($to, '+'),
                    'message' => $message,
                    'countryCode' => '62',
                ]);
        } catch (\Throwable $e) {
            // Jaringan mati, DNS gagal, timeout — dikembalikan sebagai hasil,
            // bukan dilempar, supaya pemanggil tidak ikut gagal.
            return WhatsappResult::gagal('Gagal menghubungi Fonnte: '.$e->getMessage());
        }

        $body = $response->json() ?? [];

        if (! $response->successful()) {
            return WhatsappResult::gagal(
                'Fonnte menolak permintaan (HTTP '.$response->status().').',
                $body,
            );
        }

        // Fonnte membalas HTTP 200 meski gagal; statusnya ada di badan respons.
        if (($body['status'] ?? false) !== true) {
            return WhatsappResult::gagal(
                (string) ($body['reason'] ?? 'Fonnte menolak pesan tanpa alasan yang disebutkan.'),
                $body,
            );
        }

        $id = $body['id'] ?? null;

        return WhatsappResult::berhasil(
            is_array($id) ? (string) ($id[0] ?? '') : (string) $id,
            $body,
        );
    }

    public function nama(): string
    {
        return 'fonnte';
    }
}
