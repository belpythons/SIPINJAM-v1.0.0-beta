<?php

namespace App\Support;

/**
 * Hasil satu percobaan pengiriman WhatsApp.
 *
 * Dibuat sebagai nilai, bukan exception: kegagalan kirim adalah kondisi yang
 * diharapkan dan harus tercatat, bukan kejadian luar biasa yang membatalkan
 * transaksi bisnis di sekitarnya.
 */
class WhatsappResult
{
    private function __construct(
        public readonly bool $berhasil,
        public readonly ?string $idPesan = null,
        public readonly ?string $error = null,
        public readonly array $mentah = [],
    ) {}

    public static function berhasil(?string $idPesan = null, array $mentah = []): self
    {
        return new self(true, $idPesan, null, $mentah);
    }

    public static function gagal(string $error, array $mentah = []): self
    {
        return new self(false, null, $error, $mentah);
    }
}
