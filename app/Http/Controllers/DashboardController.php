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
    public function index(Request $request): Response|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        // ── Statistik peminjaman user ────────────────────────
        $stats = [
            'total' => Peminjaman::where('user_id', $user->id)->count(),
            'sedang_dipinjam' => Peminjaman::where('user_id', $user->id)
                ->where('status', Peminjaman::STATUS_APPROVED)->count(),
            'menunggu' => Peminjaman::where('user_id', $user->id)
                ->where('status', Peminjaman::STATUS_PENDING)->count(),
            'selesai' => Peminjaman::where('user_id', $user->id)
                ->where('status', Peminjaman::STATUS_DONE)->count(),
        ];

        // ── Katalog Ruangan ──────────────────────────────────
        $ruangans = Ruangan::orderBy('lokasi')
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode', 'kapasitas', 'lokasi', 'deskripsi', 'status', 'image_path']);

        // ── Katalog Barang dengan stok real-time ─────────────
        $barangs = Barang::withCount(['peminjamans as sedang_dipinjam' => function ($query) {
            $query->whereIn('status', [
                Peminjaman::STATUS_PENDING,
                Peminjaman::STATUS_APPROVED,
            ]);
        }])
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode', 'stok_total', 'stok_tersedia', 'kategori', 'deskripsi', 'status', 'image_path'])
            ->map(function (Barang $barang) {
                $barang->sedang_dipinjam = $barang->sedang_dipinjam ?? 0;
                $barang->stok_tersedia = max(0, $barang->stok_total - $barang->sedang_dipinjam);

                return $barang;
            });

        // ── Kalender Events (Approved / Sedang Dipinjam) ────
        $calendarEvents = Peminjaman::with(['ruangan', 'barang'])
            ->where('status', Peminjaman::STATUS_APPROVED)
            ->get()
            ->map(function (Peminjaman $p) {
                $isRuangan = $p->tipe === 'ruangan';
                $nama = $p->nama_item ?? ($isRuangan ? $p->ruangan?->nama : $p->barang?->nama) ?? 'Peminjaman';

                return [
                    'id' => $p->id,
                    'title' => ($isRuangan ? '🏢 ' : '📦 ').$nama,
                    'start' => $p->tanggal_mulai?->format('Y-m-d'),
                    'end' => $p->tanggal_selesai?->addDay()->format('Y-m-d'), // FullCalendar end is exclusive
                    'backgroundColor' => $isRuangan ? '#2563eb' : '#d97706', // blue (Ruangan) vs amber (Barang)
                    'borderColor' => $isRuangan ? '#1d4ed8' : '#b45309',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'tipe' => $p->tipe,
                        'nama' => $nama,
                        'jam_mulai' => $p->jam_mulai,
                        'jam_selesai' => $p->jam_selesai,
                        'keterangan' => $p->keterangan,
                    ],
                ];
            });

        return Inertia::render('User/Dashboard', [
            'stats' => $stats,
            'ruangans' => $ruangans,
            'barangs' => $barangs,
            'calendarEvents' => $calendarEvents,
        ]);
    }
}
