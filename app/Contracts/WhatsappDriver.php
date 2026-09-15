<?php

namespace App\Contracts;

use App\Support\WhatsappResult;

/**
 * Antarmuka pengiriman WhatsApp.
 *
 * Ada supaya gateway dapat diganti (Fonnte → Wablas → WA Cloud API) tanpa
 * menyentuh satu pun pemanggil. Implementasi TIDAK boleh melempar exception
 * untuk kegagalan pengiriman biasa — kembalikan WhatsappResult::gagal(), karena
 * kegagalan kirim tidak boleh menggagalkan transaksi bisnis.
 */
interface WhatsappDriver
{
    /**
     * @param  string  $to  Nomor tujuan dalam bentuk E.164 (+62...)
     * @param  array<string, mixed>  $opts
     */
    public function send(string $to, string $message, array $opts = []): WhatsappResult;

    /** Nama driver, untuk dicatat di notification_logs. */
    public function nama(): string;
}
