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
        // Kolom P1 (§6.2)
        'nilai_perolehan',
        'satuan',
        'kondisi',
        'lokasi_penyimpanan',
        'is_consumable',
        'butuh_operator',
        'min_lead_time_jam',
        'role_diizinkan',
    ];

    protected function casts(): array
    {
        return [
            'role_diizinkan' => 'array',
            'is_consumable' => 'boolean',
            'butuh_operator' => 'boolean',
            'min_lead_time_jam' => 'integer',
            'nilai_perolehan' => 'decimal:2',
            'stok_total' => 'integer',
            'stok_tersedia' => 'integer',
        ];
    }

    public function items()
    {
        return $this->morphMany(PengajuanItem::class, 'assetable');
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'barang_id');
    }
}
