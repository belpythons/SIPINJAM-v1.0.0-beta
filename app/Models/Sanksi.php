<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sanksi extends Model
{
    use HasFactory;

    protected $table = 'sanksi';

    public const JENIS_PERINGATAN = 'peringatan';

    public const JENIS_BLOKIR = 'blokir';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'mulai_at' => 'datetime',
            'sampai_at' => 'datetime',
            'dicabut_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pelanggaran(): BelongsTo
    {
        return $this->belongsTo(Pelanggaran::class);
    }

    /**
     * Sanksi blokir yang sedang berlaku: sudah mulai, belum berakhir
     * (sampai_at null = sampai ganti rugi selesai), dan belum dicabut.
     */
    public function scopeAktif($query)
    {
        return $query->where('jenis', self::JENIS_BLOKIR)
            ->whereNull('dicabut_at')
            ->where('mulai_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('sampai_at')->orWhere('sampai_at', '>', now());
            });
    }
}
