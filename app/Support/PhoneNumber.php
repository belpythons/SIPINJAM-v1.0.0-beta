<?php

namespace App\Support;

/**
 * Normalisasi & penyamaran nomor telepon Indonesia.
 *
 * Nomor selalu disimpan dalam bentuk E.164 (+62...) supaya pencarian,
 * unique index, dan pengiriman WhatsApp memakai satu bentuk yang sama.
 * Dipakai penuh oleh alur OTP di P2; di P1 hanya mutator User yang memakainya.
 */
class PhoneNumber
{
    /**
     * Ubah 08xx / 62xx / +62xx / 8xx menjadi E.164 (+62xxxxxxxxxx).
     *
     * Mengembalikan null bila masukan kosong atau jelas bukan nomor.
     */
    public static function normalize(?string $nomor): ?string
    {
        if ($nomor === null) {
            return null;
        }

        // Sisakan digit saja; tanda plus dipulihkan di akhir.
        $digit = preg_replace('/\D+/', '', $nomor) ?? '';

        if ($digit === '') {
            return null;
        }

        $digit = match (true) {
            str_starts_with($digit, '62') => $digit,
            str_starts_with($digit, '0') => '62'.substr($digit, 1),
            str_starts_with($digit, '8') => '62'.$digit,
            default => $digit,
        };

        // Nomor Indonesia yang sah: 62 + 9..13 digit.
        if (! preg_match('/^62\d{9,13}$/', $digit)) {
            return null;
        }

        return '+'.$digit;
    }

    /**
     * Bentuk tersamar untuk peran yang tidak berhak melihat kontak penuh
     * (UU PDP No. 27/2022): +62812****7890.
     */
    public static function mask(?string $nomor): ?string
    {
        $e164 = self::normalize($nomor) ?? $nomor;

        if ($e164 === null || strlen($e164) < 8) {
            return $e164;
        }

        return substr($e164, 0, 6).'****'.substr($e164, -4);
    }
}
