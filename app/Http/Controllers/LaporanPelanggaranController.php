<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaporanRequest;
use App\Models\Laporan;
use App\Models\Peminjaman;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LaporanPelanggaranController extends Controller
{
    public function create(): Response
    {
        // Daftar peminjaman yang sudah/sedang berjalan — sumber pelaku yang
        // sah, bukan tebakan (hindari bug lama B-05).
        $peminjamans = Peminjaman::with(['user:id,name'])
            ->whereIn('status', [Peminjaman::STATUS_APPROVED, Peminjaman::STATUS_DONE])
            ->latest()
            ->limit(100)
            ->get(['id', 'user_id', 'tipe', 'nama_item', 'tanggal_mulai', 'tanggal_selesai', 'status']);

        return Inertia::render('User/LaporPelanggaran', [
            'peminjamans' => $peminjamans,
        ]);
    }

    public function store(StoreLaporanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Laporan::create([
            'pelapor_id' => Auth::id(),
            'peminjaman_id' => $data['peminjaman_id'],
            'jenis' => $data['jenis'],
            'deskripsi' => $data['deskripsi'],
            'bukti' => $request->hasFile('bukti')
                ? [ImageService::cropAndSave($request->file('bukti'), 'laporan')]
                : null,
            'status' => Laporan::STATUS_MENUNGGU,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Laporan pelanggaran berhasil dikirim. Admin akan meninjau laporan Anda.');
    }
}
