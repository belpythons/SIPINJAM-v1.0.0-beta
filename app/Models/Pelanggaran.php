<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Catatan pelanggaran.
 *
 * `pengajuan_item_id` wajib terisi: pelaku SELALU diturunkan dari pemilik
 * pengajuan pada baris aset yang diperiksa, tidak pernah ditebak dari kedekatan
 * waktu (cacat B-05).
 */
class Pelanggaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelanggaran';

    public const JENIS = [
        'terlambat', 'tidak_bersih', 'rusak_ringan', 'rusak_berat',
        'hilang', 'tidak_digunakan', 'lpj_telat',
    ];

    public const TINDAK_MENUNGGU = 'menunggu';

    public const TINDAK_DISEPAKATI = 'disepakati';

    public const TINDAK_LUNAS = 'lunas';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'bukti' => 'array',
            'poin' => 'integer',
            'nilai_ganti_rugi' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(PengajuanItem::class, 'pengajuan_item_id');
    }

    public function sanksi(): HasMany
    {
        return $this->hasMany(Sanksi::class);
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    /** Ganti rugi yang belum lunas — dasar blokir sampai diselesaikan. */
    public function scopeBelumLunas($query)
    {
        return $query->where('status_tindak_lanjut', '!=', self::TINDAK_LUNAS);
    }
}
