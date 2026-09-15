<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Berita acara serah terima, keluar maupun kembali.
 *
 * Terikat ke satu baris aset (pengajuan_item), bukan ke pengajuan — pemeriksaan
 * terjadi per aset, dan dari sinilah pelaku pelanggaran diturunkan.
 */
class SerahTerima extends Model
{
    use HasFactory;

    protected $table = 'serah_terima';

    public const TIPE_KELUAR = 'keluar';

    public const TIPE_KEMBALI = 'kembali';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'waktu' => 'datetime',
            'checklist' => 'array',
            'foto' => 'array',
            'jumlah' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(PengajuanItem::class, 'pengajuan_item_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
