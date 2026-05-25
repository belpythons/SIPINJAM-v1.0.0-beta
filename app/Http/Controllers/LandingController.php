<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function index(): Response
    {
        $ruangans = Ruangan::where('status', 'tersedia')
            ->orderBy('lokasi')
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode', 'kapasitas', 'lokasi', 'deskripsi', 'status']);

        $barangs = Barang::where('status', 'tersedia')
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode', 'stok_total', 'stok_tersedia', 'kategori', 'deskripsi', 'status']);

        return Inertia::render('Landing', [
            'ruangans' => $ruangans,
            'barangs'  => $barangs,
        ]);
    }
}
