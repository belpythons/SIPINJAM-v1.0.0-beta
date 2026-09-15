<?php

namespace App\Rules;

use App\Support\PhoneNumber;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Nomor WhatsApp Indonesia yang dapat dinormalisasi ke E.164.
 *
 * Menerima 08xx, 62xx, +62xx, maupun 8xx — penolakan hanya terjadi bila
 * PhoneNumber::normalize() tidak dapat memahaminya sama sekali.
 */
class NomorWhatsapp implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (PhoneNumber::normalize((string) $value) === null) {
            $fail('Nomor WhatsApp tidak valid. Contoh yang benar: 081234567890 atau +6281234567890.');
        }
    }
}
