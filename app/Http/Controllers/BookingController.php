<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman; // Pastikan model yang digunakan adalah Peminjaman
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function generatePDF($id)
    {
        // Tambahkan with('user') agar nama user terbawa ke PDF
        $booking = Peminjaman::with('user')->findOrFail($id);

        // Keamanan: Hanya izinkan cetak jika sudah disetujui/selesai
        if ($booking->status == 'menunggu') {
            return redirect()->back()->with('error', 'PDF belum tersedia karena peminjaman belum disetujui.');
        }

        // Load view khusus untuk PDF 
        $pdf = Pdf::loadView('user.bookings.pdf', compact('booking'));
        
        return $pdf->download('Bukti-Peminjaman-' . $booking->id . '.pdf');
    }

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
        // 1. Validasi Input
        $request->validate([
            'tipe_peminjaman' => 'required',
            'nama_item'       => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'waktu_mulai'     => 'required',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'waktu_selesai'   => 'required',
            'keterangan'      => 'required|string', // Hapus min:10 untuk menghindari error validasi diam-diam jika keterangan pendek
            'catatan'         => 'nullable|string',
        ]);

        // Gabungkan Keterangan dan Catatan
        $keteranganLengkap = $request->keterangan;
        if ($request->filled('catatan')) {
            $keteranganLengkap .= "\nCatatan: " . $request->catatan;
        }

        // 2. Simpan ke database menggunakan model Peminjaman
        $peminjaman = Peminjaman::create([
            'user_id'         => Auth::id(),
            'tipe'            => $request->tipe_peminjaman, 
            'nama_item'       => $request->nama_item,
            'tanggal_mulai'   => $request->tanggal_mulai, // Pastikan ini sesuai dengan nama kolom di tabel peminjamans Anda
            'tanggal_selesai' => $request->tanggal_selesai, // Pastikan ini sesuai dengan nama kolom di tabel peminjamans Anda
            'jam_mulai'       => $request->waktu_mulai,   
            'jam_selesai'     => $request->waktu_selesai, 
            'keterangan'      => $keteranganLengkap,
            'status'          => 'menunggu',
        ]);

        // 3. Response Berdasarkan Jenis Request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil diajukan!'
            ]);
        }

        // Redirect jika dari form biasa
        return redirect()->route('bookings.index')->with('success', 'Peminjaman berhasil diajukan!');
    }
}