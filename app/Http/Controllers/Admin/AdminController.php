<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Ruangan;
use App\Models\Barang;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {}

    // ────────────────────────────────────────
    // Dashboard
    // ────────────────────────────────────────
    public function dashboard(): \Illuminate\View\View
    {
        return view('admin.dashboard');
    }

    // ════════════════════════════════════════
    // KELOLA USER — CRUD Lengkap
    // ════════════════════════════════════════
    public function kelolaUser(): \Illuminate\View\View
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.kelola_user', compact('users'));
    }

    public function storeUser(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,user',
        ]);

        try {
            User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            return redirect()->back()->with('success', 'User berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function updateUser(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role'  => 'required|in:admin,user',
        ]);

        try {
            $user = User::findOrFail($id);

            $data = [
                'name'  => $request->name,
                'email' => $request->email,
                'role'  => $request->role,
            ];

            // Hanya update password jika diisi
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->back()->with('success', 'User berhasil diperbarui!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function destroyUser(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            // Cegah admin menghapus dirinya sendiri
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
            }

            $user->delete();

            return redirect()->back()->with('success', 'User berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    // ════════════════════════════════════════
    // KELOLA PEMINJAMAN
    // ════════════════════════════════════════
    public function kelolaPeminjaman(): \Illuminate\View\View
    {
        $peminjamans = Peminjaman::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.kelola_peminjaman', compact('peminjamans'));
    }

    public function setujuiPeminjaman(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            $this->bookingService->approveBooking($peminjaman);
            return redirect()->back()->with('success', 'Peminjaman berhasil disetujui!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui peminjaman: ' . $e->getMessage());
        }
    }

    public function tolakPeminjaman(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            $this->bookingService->rejectBooking($peminjaman);
            return redirect()->back()->with('error', 'Peminjaman ditolak!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menolak peminjaman: ' . $e->getMessage());
        }
    }

    // ════════════════════════════════════════
    // KELOLA RUANGAN — CRUD Lengkap
    // ════════════════════════════════════════
    public function kelolaRuangan(): \Illuminate\View\View
    {
        $ruangans = Ruangan::orderBy('created_at', 'desc')->get();
        return view('admin.kelola_ruangan', compact('ruangans'));
    }

    public function storeRuangan(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'kode'      => 'required|string|max:50|unique:ruangans,kode',
            'kapasitas' => 'required|integer|min:1',
            'lokasi'    => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'status'    => 'required|in:tersedia,tidak_tersedia',
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $data = $request->only(['nama', 'kode', 'kapasitas', 'lokasi', 'deskripsi', 'status']);

            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('ruangan', 'public');
            }

            Ruangan::create($data);

            return redirect()->back()->with('success', 'Ruangan berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan ruangan: ' . $e->getMessage())->withInput();
        }
    }

    public function updateRuangan(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'kode'      => 'required|string|max:50|unique:ruangans,kode,' . $id,
            'kapasitas' => 'required|integer|min:1',
            'lokasi'    => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'status'    => 'required|in:tersedia,tidak_tersedia',
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $ruangan = Ruangan::findOrFail($id);
            $data = $request->only(['nama', 'kode', 'kapasitas', 'lokasi', 'deskripsi', 'status']);

            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($ruangan->foto) {
                    Storage::disk('public')->delete($ruangan->foto);
                }
                $data['foto'] = $request->file('foto')->store('ruangan', 'public');
            }

            $ruangan->update($data);

            return redirect()->back()->with('success', 'Ruangan berhasil diperbarui!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui ruangan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyRuangan(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $ruangan = Ruangan::findOrFail($id);

            if ($ruangan->foto) {
                Storage::disk('public')->delete($ruangan->foto);
            }

            $ruangan->delete();

            return redirect()->back()->with('success', 'Ruangan berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus ruangan: ' . $e->getMessage());
        }
    }

    // ════════════════════════════════════════
    // KELOLA BARANG — CRUD Lengkap
    // ════════════════════════════════════════
    public function kelolaBarang(): \Illuminate\View\View
    {
        $barangs = Barang::orderBy('created_at', 'desc')->get();
        return view('admin.kelola_barang', compact('barangs'));
    }

    public function storeBarang(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'nama'           => 'required|string|max:255',
            'kode'           => 'required|string|max:50|unique:barangs,kode',
            'stok_total'     => 'required|integer|min:0',
            'stok_tersedia'  => 'required|integer|min:0',
            'kategori'       => 'nullable|string|max:100',
            'deskripsi'      => 'nullable|string',
            'status'         => 'required|in:tersedia,tidak_tersedia',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $data = $request->only(['nama', 'kode', 'stok_total', 'stok_tersedia', 'kategori', 'deskripsi', 'status']);

            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('barang', 'public');
            }

            Barang::create($data);

            return redirect()->back()->with('success', 'Barang berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan barang: ' . $e->getMessage())->withInput();
        }
    }

    public function updateBarang(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'nama'           => 'required|string|max:255',
            'kode'           => 'required|string|max:50|unique:barangs,kode,' . $id,
            'stok_total'     => 'required|integer|min:0',
            'stok_tersedia'  => 'required|integer|min:0',
            'kategori'       => 'nullable|string|max:100',
            'deskripsi'      => 'nullable|string',
            'status'         => 'required|in:tersedia,tidak_tersedia',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $barang = Barang::findOrFail($id);
            $data = $request->only(['nama', 'kode', 'stok_total', 'stok_tersedia', 'kategori', 'deskripsi', 'status']);

            if ($request->hasFile('foto')) {
                if ($barang->foto) {
                    Storage::disk('public')->delete($barang->foto);
                }
                $data['foto'] = $request->file('foto')->store('barang', 'public');
            }

            $barang->update($data);

            return redirect()->back()->with('success', 'Barang berhasil diperbarui!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui barang: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyBarang(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $barang = Barang::findOrFail($id);

            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }

            $barang->delete();

            return redirect()->back()->with('success', 'Barang berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }
}