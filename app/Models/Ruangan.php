<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangans';

    protected $fillable = [
        'nama',
        'kode',
        'kapasitas',
        'lokasi',
        'deskripsi',
        'status',
        'foto',
        'image_path',
        // Kolom P1 (§6.2)
        'fasilitas',
        'jam_buka',
        'jam_tutup',
        'buffer_menit',
        'pengelola_unit',
        'butuh_persetujuan_khusus',
        'min_lead_time_jam',
        'role_diizinkan',
    ];

    protected function casts(): array
    {
        return [
            'fasilitas' => 'array',
            'role_diizinkan' => 'array',
            'butuh_persetujuan_khusus' => 'boolean',
            'buffer_menit' => 'integer',
            'min_lead_time_jam' => 'integer',
            'kapasitas' => 'integer',
        ];
    }

    public function items()
    {
        return $this->morphMany(PengajuanItem::class, 'assetable');
    }

    protected $appends = ['is_terpakai'];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'ruangan_id');
    }

    public function getIsTerpakaiAttribute()
    {
        return $this->peminjamans()
            ->where('status', 'sedang_dipinjam')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->whereTime('jam_mulai', '<=', now())
            ->whereTime('jam_selesai', '>=', now())
            ->exists();
    }
}
