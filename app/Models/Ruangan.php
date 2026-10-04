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
    ];

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