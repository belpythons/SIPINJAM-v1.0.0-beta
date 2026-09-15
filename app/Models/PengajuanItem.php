<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PengajuanItem extends Model
{
    use HasFactory;

    // ── Status per baris aset ────────────────────────────
    public const STATUS_DIAJUKAN = 'diajukan';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_BERJALAN = 'berjalan';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_BERMASALAH = 'bermasalah';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const STATUSES = [
        self::STATUS_DIAJUKAN, self::STATUS_DISETUJUI, self::STATUS_DITOLAK,
        self::STATUS_BERJALAN, self::STATUS_SELESAI, self::STATUS_BERMASALAH,
        self::STATUS_DIBATALKAN,
    ];

    /**
     * Status yang benar-benar memesan slot aset.
     *
     * Dipakai AvailabilityService — hanya baris berstatus ini yang mengurangi
     * ketersediaan. Baris "diajukan" TIDAK memesan slot, sehingga stok yang
     * habis pada satu rentang tidak memblokir rentang lain (pelajaran B-02).
     */
    public const STATUS_MEMESAN_SLOT = [
        self::STATUS_DISETUJUI, self::STATUS_BERJALAN,
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'mulai_at' => 'datetime',
            'selesai_at' => 'datetime',
            'jumlah' => 'integer',
        ];
    }

    // ── Relations ────────────────────────────────────────

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function assetable(): MorphTo
    {
        return $this->morphTo();
    }

    public function serahTerima(): HasMany
    {
        return $this->hasMany(SerahTerima::class);
    }

    public function pelanggaran(): HasMany
    {
        return $this->hasMany(Pelanggaran::class);
    }

    // ── Scopes ───────────────────────────────────────────

    public function scopeMemesanSlot($query)
    {
        return $query->whereIn('status_item', self::STATUS_MEMESAN_SLOT);
    }

    /**
     * Irisan waktu setengah terbuka [mulai, selesai).
     *
     * Dua slot yang bersentuhan tepat di ujungnya (08:00–12:00 dan 12:00–15:00)
     * TIDAK dianggap bentrok.
     */
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
