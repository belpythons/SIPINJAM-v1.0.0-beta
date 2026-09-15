<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Sanksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LaporanPelanggaranController extends Controller
{
    public function index(): Response
    {
        $laporans = Laporan::with(['pelapor:id,name', 'peminjaman.user:id,name', 'ditindakOleh:id,name'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/KelolaLaporan', [
            'laporans' => $laporans,
        ]);
    }

    public function putuskanSanksi(Request $request, Laporan $laporan): RedirectResponse
    {
        $data = $request->validate([
            'hari' => ['required', 'integer', 'min:1', 'max:365'],
            'alasan' => ['required', 'string', 'max:500'],
        ]);

        if ($laporan->status !== Laporan::STATUS_MENUNGGU) {
            return redirect()->back()->with('error', 'Laporan ini sudah ditindaklanjuti.');
        }

        DB::transaction(function () use ($laporan, $data) {
            $pelaku = $laporan->peminjaman->user;

            Sanksi::create([
                'user_id' => $pelaku->id,
                'pelanggaran_id' => null,
                'jenis' => Sanksi::JENIS_BLOKIR,
                'mulai_at' => now(),
                'sampai_at' => now()->addDays($data['hari']),
                'alasan' => $data['alasan'],
                'dibuat_oleh' => Auth::id(),
            ]);

            // Sinkronkan kolom cache is_blocked/blocked_until agar LoginRequest
            // (yang masih membaca kolom mentah, bukan isBlocked()) ikut menolak login.
            $pelaku->blockFor($data['hari'], $data['alasan']);

            $laporan->update([
                'status' => Laporan::STATUS_DITINDAK,
                'ditindak_oleh' => Auth::id(),
                'catatan_admin' => $data['alasan'],
            ]);
        });

        return redirect()->back()->with('success', 'Sanksi berhasil dijatuhkan.');
    }

    public function tolak(Request $request, Laporan $laporan): RedirectResponse
    {
        if ($laporan->status !== Laporan::STATUS_MENUNGGU) {
            return redirect()->back()->with('error', 'Laporan ini sudah ditindaklanjuti.');
        }

        $laporan->update([
            'status' => Laporan::STATUS_DITOLAK,
            'ditindak_oleh' => Auth::id(),
            'catatan_admin' => $request->input('catatan_admin'),
        ]);

        return redirect()->back()->with('success', 'Laporan ditolak.');
    }
}
