<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Rentang waktu saat sebuah aset tidak dapat dipinjam: pemeliharaan, libur,
 * atau dipakai acara internal. Dikurangkan oleh AvailabilityService.
 */
class AssetBlackout extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'mulai_at' => 'datetime',
            'selesai_at' => 'datetime',
        ];
    }

    public function assetable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeBeririsan($query, $mulai, $selesai)
    {
        return $query->where('mulai_at', '<', $selesai)
            ->where('selesai_at', '>', $mulai);
    }

    public function scopeUntukAset($query, string $type, int $id)
    {
        return $query->where('assetable_type', $type)->where('assetable_id', $id);
    }
}
