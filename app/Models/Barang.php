<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';

    protected $fillable = [
        'nama',
        'kode',
        'stok_total',
        'stok_tersedia',
        'kategori',
        'deskripsi',
        'status',
        'foto',
        'image_path',
    ];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'barang_id');
    }
}