<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tata tertib berversi — dasar hukum penegakan sanksi.
 *
 * Menggantikan model App\Models\TataTertib lama yang kosong dan tidak punya
 * tabel sama sekali (T-17 pada dokumen rencana).
 */
class TataTertibVersion extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['berlaku_sejak' => 'datetime'];
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(UserAgreement::class);
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /** Versi yang berlaku saat ini. */
    public static function aktif(): ?self
    {
        return static::where('berlaku_sejak', '<=', now())
            ->orderByDesc('berlaku_sejak')
            ->first();
    }
}
