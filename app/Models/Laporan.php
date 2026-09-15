<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    protected $table = 'laporan';

    public const JENIS_BELUM_KEMBALI = 'belum_kembali';

    public const JENIS_RUSAK = 'rusak';

    public const JENIS_RUANGAN_BERANTAKAN = 'ruangan_berantakan';

    public const JENIS = [
        self::JENIS_BELUM_KEMBALI,
        self::JENIS_RUSAK,
        self::JENIS_RUANGAN_BERANTAKAN,
    ];

    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_DITINDAK = 'ditindak';

    public const STATUS_DITOLAK = 'ditolak';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'bukti' => 'array',
        ];
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function ditindakOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditindak_oleh');
    }
}
