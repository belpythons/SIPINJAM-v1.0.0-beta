<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    // ── Status Constants ─────────────────────────────────
    public const STATUS_PENDING = 'menunggu';

    public const STATUS_APPROVED = 'sedang_dipinjam';

    public const STATUS_REJECTED = 'ditolak';

    public const STATUS_DONE = 'selesai';

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
        'jumlah',
        'tanggal',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'mulai_at',
        'selesai_at',
        'keterangan',
        'alasan_sistem',
        'status',
        'nomor_surat',
        'approved_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'mulai_at' => 'datetime',
            'selesai_at' => 'datetime',
            'approved_at' => 'datetime',
            'completed_at' => 'datetime',
            'jumlah' => 'integer',
        ];
    }

    // ── Model Events ─────────────────────────────────────

    /**
     * Jaga agar mulai_at/selesai_at SELALU terisi.
     *
     * B-15: pengecekan bentrok jadwal kini bertumpu penuh pada kedua kolom ini.
     * Bila sebuah baris tersimpan tanpa keduanya (lewat factory, seeder, atau
     * CRUD admin), bentrok akan lolos tanpa suara. Menurunkannya di sini —
     * bukan hanya di BookingService — menutup seluruh jalur penulisan sekaligus.
     */
    protected static function booted(): void
    {
        static::saving(function (self $peminjaman) {
            if (empty($peminjaman->mulai_at) && ! empty($peminjaman->tanggal_mulai)) {
                $peminjaman->mulai_at = static::combineDateTime(
                    $peminjaman->tanggal_mulai,
                    $peminjaman->jam_mulai,
                    '00:00:00'
                );
            }

            if (empty($peminjaman->selesai_at) && ! empty($peminjaman->tanggal_selesai)) {
                $peminjaman->selesai_at = static::combineDateTime(
                    $peminjaman->tanggal_selesai,
                    $peminjaman->jam_selesai,
                    '23:59:59'
                );
            }
        });
    }

    /**
     * Gabungkan tanggal + jam menjadi satu Carbon.
     */
    public static function combineDateTime(mixed $tanggal, mixed $jam, string $jamDefault): ?Carbon
    {
        if (empty($tanggal)) {
            return null;
        }

        try {
            $tanggal = Carbon::parse($tanggal)->format('Y-m-d');
            $jam = ! empty($jam)
                ? Carbon::parse($jam)->format('H:i:s')
                : $jamDefault;

            return Carbon::parse($tanggal.' '.$jam);
        } catch (\Throwable) {
            return null;
        }
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
