<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::withCount(['peminjamans as sedang_dipinjam' => function ($query) {
            $query->whereIn('status', [
                Peminjaman::STATUS_PENDING,
                Peminjaman::STATUS_APPROVED,
            ]);
        }])
        ->orderBy('kategori')
        ->orderBy('nama')
        ->get(['id', 'nama', 'kode', 'stok_total', 'stok_tersedia', 'kategori', 'deskripsi', 'status'])
        ->map(function (Barang $barang) {
            $barang->sedang_dipinjam = $barang->sedang_dipinjam ?? 0;
            $barang->stok_tersedia   = max(0, $barang->stok_total - $barang->sedang_dipinjam);
            return $barang;
        });

        return Inertia::render('User/Barang', [
            'barangs' => $barangs,
        ]);
    }
}