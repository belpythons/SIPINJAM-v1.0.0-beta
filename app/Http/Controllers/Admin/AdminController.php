<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {}

    public function dashboard(): \Illuminate\View\View
    {
        return view('admin.dashboard');
    }

    public function kelolaUser(): \Illuminate\View\View
    {
        return view('admin.kelola_user');
    }

    public function storeUser(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,user',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan!');
    }

    public function kelolaPeminjaman(): \Illuminate\View\View
    {
        $peminjamans = Peminjaman::with('user')->orderBy('created_at', 'desc')->get();

        return view('admin.kelola_peminjaman', compact('peminjamans'));
    }

    public function setujuiPeminjaman(int $id): \Illuminate\Http\RedirectResponse
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $this->bookingService->approveBooking($peminjaman);

        return redirect()->back()->with('success', 'Peminjaman berhasil disetujui!');
    }

    public function tolakPeminjaman(int $id): \Illuminate\Http\RedirectResponse
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $this->bookingService->rejectBooking($peminjaman);

        return redirect()->back()->with('error', 'Peminjaman ditolak!');
    }

    public function kelolaRuangan(): \Illuminate\View\View
    {
        return view('admin.kelola_ruangan');
    }

    public function kelolaBarang(): \Illuminate\View\View
    {
        return view('admin.kelola_barang');
    }
}