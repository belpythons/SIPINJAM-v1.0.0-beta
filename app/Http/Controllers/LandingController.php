<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Barang;
use App\Models\Peminjaman;
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
            ->get(['id', 'nama', 'kode', 'kapasitas', 'lokasi', 'deskripsi', 'status', 'image_path']);

        $barangs = Barang::where('status', 'tersedia')
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode', 'stok_total', 'stok_tersedia', 'kategori', 'deskripsi', 'status', 'image_path']);

        $banners = Banner::orderBy('id', 'asc')->get(['id', 'image_path']);

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

        return Inertia::render('Landing', [
            'ruangans' => $ruangans,
            'barangs' => $barangs,
            'banners' => $banners,
            'calendarEvents' => $calendarEvents,
        ]);
    }
}
