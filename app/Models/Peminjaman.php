<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    // 1. Beritahu Laravel nama tabel yang benar secara manual
    protected $table = 'peminjamans';

    // 2. Izinkan kita menyimpan data ke kolom-kolom tabel ini nanti
    protected $guarded = [];

    // 3. TAMBAHKAN INI: Relasi ke tabel users agar bisa mengambil nama peminjam
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}