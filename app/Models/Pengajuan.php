<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengajuan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengajuan';

    // ── Status header (§7.1) ─────────────────────────────
    public const STATUS_DRAFT = 'draft';

    public const STATUS_DIAJUKAN = 'diajukan';

    public const STATUS_DIVERIFIKASI = 'diverifikasi';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DISETUJUI_SEBAGIAN = 'disetujui_sebagian';

    public const STATUS_BERJALAN = 'berjalan';

    public const STATUS_MENUNGGU_PEMERIKSAAN = 'menunggu_pemeriksaan';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_BERMASALAH = 'bermasalah';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const STATUS_KEDALUWARSA = 'kedaluwarsa';

    public const STATUSES = [
        self::STATUS_DRAFT, self::STATUS_DIAJUKAN, self::STATUS_DIVERIFIKASI,
        self::STATUS_DISETUJUI, self::STATUS_DISETUJUI_SEBAGIAN, self::STATUS_BERJALAN,
        self::STATUS_MENUNGGU_PEMERIKSAAN, self::STATUS_SELESAI, self::STATUS_BERMASALAH,
        self::STATUS_DITOLAK, self::STATUS_DIBATALKAN, self::STATUS_KEDALUWARSA,
    ];

    /** Status tuntas — tidak ada transisi keluar lagi. */
    public const STATUS_FINAL = [
        self::STATUS_SELESAI, self::STATUS_DITOLAK,
        self::STATUS_DIBATALKAN, self::STATUS_KEDALUWARSA,
    ];

    public const JENIS_TUNGGAL = 'tunggal';

    public const JENIS_EVENT = 'event';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'mulai_at' => 'datetime',
            'selesai_at' => 'datetime',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'ttd_basah_at' => 'datetime',
            'jumlah_peserta' => 'integer',
        ];
    }

    // ── Relations ────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PengajuanItem::class);
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(PengajuanTimeline::class)->orderBy('created_at');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // ── Scopes ───────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->whereNotIn('status', self::STATUS_FINAL);
    }

    public function sudahFinal(): bool
    {
        return in_array($this->status, self::STATUS_FINAL, true);
    }
}
