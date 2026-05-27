<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    // ── Status Constants ─────────────────────────────────
    public const STATUS_PENDING  = 'menunggu';
    public const STATUS_APPROVED = 'sedang_dipinjam';
    public const STATUS_REJECTED = 'ditolak';
    public const STATUS_DONE     = 'selesai';

    /**
     * Daftar semua status yang valid.
     */
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_DONE,
    ];

    // ── Mass-Assignment Protection ───────────────────────
    protected $fillable = [
        'user_id',
        'tipe',
        'barang_id',
        'ruangan_id',
        'nama_item',
        'tanggal',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'status',
        'approved_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai'   => 'date',
            'tanggal_selesai' => 'date',
            'approved_at'     => 'datetime',
            'completed_at'    => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
}