<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil ID User yang sedang login
        $user = $request->user(); 
        
        // 2. Hitung statistik untuk user tersebut
        $totalPeminjaman = Peminjaman::where('user_id', $user->id)->count();
        $sedangDipinjam = Peminjaman::where('user_id', $user->id)->where('status', 'sedang_dipinjam')->count();
        $menunggu = Peminjaman::where('user_id', $user->id)->where('status', 'menunggu')->count();
        $selesai = Peminjaman::where('user_id', $user->id)->where('status', 'selesai')->count();

        // 3. Data untuk Chart (Opsional, siap dipakai kalau temanmu butuh nanti)
        $enamBulanLalu = Carbon::now()->subMonths(6);
        $trenPeminjaman = Peminjaman::select(
                DB::raw('DATE_FORMAT(tanggal_mulai, "%Y-%m") as bulan'),
                DB::raw('count(*) as total')
            )
            ->where('user_id', $user->id)
            ->where('tanggal_mulai', '>=', $enamBulanLalu)
            ->groupBy('bulan')
            ->orderBy('bulan', 'ASC')
            ->get();

        // 4. Kirim data LANGSUNG ke tampilan (view) dashboard.blade.php
        return view('dashboard', compact(
            'user', 
            'totalPeminjaman', 
            'sedangDipinjam', 
            'menunggu', 
            'selesai', 
            'trenPeminjaman'
        ));
    }
}