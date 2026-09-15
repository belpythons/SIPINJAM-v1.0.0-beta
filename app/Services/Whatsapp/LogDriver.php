<?php

namespace App\Services\Whatsapp;

use App\Contracts\WhatsappDriver;
use App\Support\PhoneNumber;
use App\Support\WhatsappResult;
use Illuminate\Support\Facades\Log;

/**
 * Driver untuk lokal & test: tidak mengirim apa pun, hanya mencatat.
 *
 * Nomor tujuan ditulis TERSAMAR ke log — log aplikasi bukan tempat yang tepat
 * untuk menyimpan data pribadi (UU PDP).
 */
class LogDriver implements WhatsappDriver
{
    public function send(string $to, string $message, array $opts = []): WhatsappResult
    {
        Log::info('[WhatsApp:log] pesan tidak benar-benar dikirim', [
            'tujuan' => PhoneNumber::mask($to),
            'panjang_pesan' => mb_strlen($message),
        ]);

        return WhatsappResult::berhasil('log-'.uniqid());
    }

    public function nama(): string
    {
        return 'log';
    }
}
