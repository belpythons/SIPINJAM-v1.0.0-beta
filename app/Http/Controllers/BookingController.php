<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Peminjaman::where('user_id', Auth::id())
                            ->latest()
                            ->get();

        $stats = [
            'total'     => $bookings->count(),
            'pending'   => $bookings->where('status', 'menunggu')->count(),
            'approved'  => $bookings->where('status', 'sedang_dipinjam')->count(),
            'completed' => $bookings->where('status', 'selesai')->count(),
        ];

        return view('user.bookings.index', compact('bookings', 'stats'));
    }
    
    public function store(Request $request)
{
    // 1. Validasi disesuaikan dengan input dari Dashboard & Riwayat
    // Tips: Gunakan nama field yang dikirim JavaScript di Dashboard
    $request->validate([
        'tipe_peminjaman' => 'required',
        'nama_item'       => 'required|string|max:255',
        'tanggal_mulai'   => 'required|date',
        'waktu_mulai'     => 'required',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        'waktu_selesai'   => 'required',
        'keterangan'      => 'required|string|min:10',
        'catatan'         => 'nullable|string',
    ]);

    $keteranganLengkap = $request->keterangan;
    if ($request->filled('catatan')) {
        $keteranganLengkap .= "\nCatatan: " . $request->catatan;
    }

    // 2. Simpan ke database
    $peminjaman = Peminjaman::create([
        'user_id'         => Auth::id(),
        'tipe'            => $request->tipe_peminjaman, 
        'nama_item'       => $request->nama_item,
        'tanggal'         => $request->tanggal_mulai,
        'tanggal_mulai'   => $request->tanggal_mulai,
        'tanggal_selesai' => $request->tanggal_selesai,
        'jam_mulai'       => $request->waktu_mulai,   
        'jam_selesai'     => $request->waktu_selesai, 
        'keterangan'      => $keteranganLengkap,
        'status'          => 'menunggu',
    ]);

    // 3. (PENTING) Cek apakah permintaan datang dari AJAX (Dashboard) atau Form Biasa
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil diajukan!'
        ]);
    }

    // Jika dari form riwayat biasa, tetap gunakan redirect
    return redirect()->back()->with('success', 'Peminjaman berhasil diajukan!');
}
}