<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    public const JENIS_SURAT_PERMOHONAN = 'surat_permohonan';

    public const JENIS_SURAT_IZIN = 'surat_izin';

    public const JENIS_BAST_KELUAR = 'bast_keluar';

    public const JENIS_BAST_KEMBALI = 'bast_kembali';

    public const JENIS_BA_KERUSAKAN = 'ba_kerusakan';

    public const JENIS = [
        self::JENIS_SURAT_PERMOHONAN, self::JENIS_SURAT_IZIN,
        self::JENIS_BAST_KELUAR, self::JENIS_BAST_KEMBALI, self::JENIS_BA_KERUSAKAN,
    ];

    protected $guarded = ['id'];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
