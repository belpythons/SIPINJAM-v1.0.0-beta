<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Peminjaman; // Pastikan model Peminjaman di-import di sini

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function kelolaUser()
    {
        return view('admin.kelola_user');
    }

    public function kelolaPeminjaman()
    {
        // Mengambil semua data peminjaman dari tabel 'peminjamans'
        // with('user') digunakan jika Anda punya relasi ke tabel users
        $peminjamans = Peminjaman::with('user')->orderBy('created_at', 'desc')->get();
        
        return view('admin.kelola_peminjaman', compact('peminjamans'));
    }

    // --- METHOD BARU UNTUK SETUJUI/TOLAK ---

    public function setujuiPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status = 'disetujui';
        $peminjaman->save();

        return redirect()->back()->with('success', 'Peminjaman berhasil disetujui!');
    }

    public function tolakPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status = 'ditolak';
        $peminjaman->save();

        return redirect()->back()->with('error', 'Peminjaman ditolak!');
    }

    // ---------------------------------------

    public function kelolaRuangan()
    {   
        return view('admin.kelola_ruangan');
    }

    public function kelolaBarang()
    {   
        return view('admin.kelola_barang');
    }
}