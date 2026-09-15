<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kode OTP verifikasi nomor WhatsApp. Kode disimpan ter-hash, tidak pernah
 * dalam bentuk asli. Alur pemakaiannya dibangun di P2.
 */
class PhoneVerification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $hidden = ['code_hash'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sudahKedaluwarsa(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
