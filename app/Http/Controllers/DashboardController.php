<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        // ── Statistik peminjaman user ────────────────────────
        $stats = [
            'total'          => Peminjaman::where('user_id', $user->id)->count(),
            'sedang_dipinjam' => Peminjaman::where('user_id', $user->id)
                                    ->where('status', Peminjaman::STATUS_APPROVED)->count(),
            'menunggu'       => Peminjaman::where('user_id', $user->id)
                                    ->where('status', Peminjaman::STATUS_PENDING)->count(),
            'selesai'        => Peminjaman::where('user_id', $user->id)
                                    ->where('status', Peminjaman::STATUS_DONE)->count(),
        ];

        // ── Katalog Ruangan ──────────────────────────────────
        $ruangans = Ruangan::orderBy('lokasi')
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode', 'kapasitas', 'lokasi', 'deskripsi', 'status']);

        // ── Katalog Barang dengan stok real-time ─────────────
        $barangs = Barang::orderBy('kategori')
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode', 'stok_total', 'stok_tersedia', 'kategori', 'deskripsi', 'status'])
            ->map(function (Barang $barang) {
                // Hitung jumlah unit yang sedang aktif dipinjam (pending + approved)
                $aktiveDipinjam = Peminjaman::where('barang_id', $barang->id)
                    ->whereIn('status', [
                        Peminjaman::STATUS_PENDING,
                        Peminjaman::STATUS_APPROVED,
                    ])
                    ->count();

                $barang->sedang_dipinjam = $aktiveDipinjam;
                $barang->stok_tersedia   = max(0, $barang->stok_total - $aktiveDipinjam);

                return $barang;
            });

        return Inertia::render('User/Dashboard', [
            'stats'    => $stats,
            'ruangans' => $ruangans,
            'barangs'  => $barangs,
        ]);
    }
}