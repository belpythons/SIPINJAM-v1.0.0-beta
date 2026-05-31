<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Ruangan;
use App\Models\Barang;
use App\Services\BookingService;
use App\Services\ImageService;
use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;
use App\Http\Requests\StoreRuanganRequest;
use App\Http\Requests\UpdateRuanganRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {}

    // ────────────────────────────────────────
    // Dashboard
    // ────────────────────────────────────────
    public function dashboard()
    {
        $totalPending = Peminjaman::where('status', Peminjaman::STATUS_PENDING)->count();
        $totalApproved = Peminjaman::where('status', Peminjaman::STATUS_APPROVED)->count();
        $totalUsers = User::count();
        $totalRuangan = Ruangan::count();
        $totalBarang = Barang::count();

        // Peminjaman sedang berjalan (untuk tabel countdown)
        $activePeminjamans = Peminjaman::with(['user', 'ruangan', 'barang'])
            ->where('status', Peminjaman::STATUS_APPROVED)
            ->orderBy('tanggal_selesai')
            ->orderBy('jam_selesai')
            ->get()
            ->map(function (Peminjaman $p) {
                return [
                    'id'              => $p->id,
                    'user_name'       => $p->user?->name ?? '-',
                    'nama_item'       => $p->nama_item,
                    'tipe'            => $p->tipe,
                    'tanggal_mulai'   => $p->tanggal_mulai?->format('Y-m-d'),
                    'tanggal_selesai' => $p->tanggal_selesai?->format('Y-m-d'),
                    'jam_mulai'       => $p->jam_mulai,
                    'jam_selesai'     => $p->jam_selesai,
                    // Target datetime for countdown (ISO 8601)
                    'target_datetime' => $p->tanggal_selesai?->format('Y-m-d') . 'T' . $p->jam_selesai . ':00',
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'pending'   => $totalPending,
                'approved'  => $totalApproved,
                'users'     => $totalUsers,
                'ruangan'   => $totalRuangan,
                'barang'    => $totalBarang,
            ],
            'activePeminjamans' => $activePeminjamans,
        ]);
    }

    // ════════════════════════════════════════
    // KELOLA USER — CRUD Lengkap
    // ════════════════════════════════════════
    public function kelolaUser()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/KelolaUser', [
            'users' => $users,
        ]);
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
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);
            $user->assignRole($request->role);

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

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            $user->syncRoles([$request->role]);

            return redirect()->back()->with('success', 'User berhasil diperbarui!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function destroyUser(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

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
    public function kelolaPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'ruangan', 'barang'])->orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/KelolaPeminjaman', [
            'peminjamans' => $peminjamans,
        ]);
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

    /**
     * Admin menandai peminjaman selesai (validasi pengembalian).
     */
    public function selesaiPeminjaman(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            $this->bookingService->completeBooking($peminjaman);
            return redirect()->back()->with('success', 'Peminjaman berhasil diselesaikan dan stok dikembalikan!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menyelesaikan peminjaman: ' . $e->getMessage());
        }
    }

    // ════════════════════════════════════════
    // KELOLA RUANGAN — CRUD Lengkap
    // ════════════════════════════════════════
    public function kelolaRuangan()
    {
        $ruangans = Ruangan::orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/KelolaRuangan', [
            'ruangans' => $ruangans,
        ]);
    }

    public function storeRuangan(StoreRuanganRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $data = $request->only(['nama', 'kode', 'kapasitas', 'lokasi', 'deskripsi', 'status']);

            if ($request->hasFile('image_path')) {
                $data['image_path'] = ImageService::cropAndSave($request->file('image_path'), 'ruangan');
            }

            Ruangan::create($data);

            return redirect()->back()->with('success', 'Ruangan berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan ruangan: ' . $e->getMessage())->withInput();
        }
    }

    public function updateRuangan(UpdateRuanganRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $ruangan = Ruangan::findOrFail($id);
            $data = $request->only(['nama', 'kode', 'kapasitas', 'lokasi', 'deskripsi', 'status']);

            if ($request->hasFile('image_path')) {
                ImageService::deleteOldImage($ruangan->image_path);
                $data['image_path'] = ImageService::cropAndSave($request->file('image_path'), 'ruangan');
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

            ImageService::deleteOldImage($ruangan->image_path);

            $ruangan->delete();

            return redirect()->back()->with('success', 'Ruangan berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus ruangan: ' . $e->getMessage());
        }
    }

    // ════════════════════════════════════════
    // KELOLA BARANG — CRUD Lengkap
    // ════════════════════════════════════════
    public function kelolaBarang()
    {
        $barangs = Barang::orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/KelolaBarang', [
            'barangs' => $barangs,
        ]);
    }

    public function storeBarang(StoreBarangRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $data = $request->only(['nama', 'kode', 'stok_total', 'stok_tersedia', 'kategori', 'deskripsi', 'status']);

            if ($request->hasFile('image_path')) {
                $data['image_path'] = ImageService::cropAndSave($request->file('image_path'), 'barang');
            }

            Barang::create($data);

            return redirect()->back()->with('success', 'Barang berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan barang: ' . $e->getMessage())->withInput();
        }
    }

    public function updateBarang(UpdateBarangRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $barang = Barang::findOrFail($id);
            $data = $request->only(['nama', 'kode', 'stok_total', 'stok_tersedia', 'kategori', 'deskripsi', 'status']);

            if ($request->hasFile('image_path')) {
                ImageService::deleteOldImage($barang->image_path);
                $data['image_path'] = ImageService::cropAndSave($request->file('image_path'), 'barang');
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

            ImageService::deleteOldImage($barang->image_path);

            $barang->delete();

            return redirect()->back()->with('success', 'Barang berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }

    // ════════════════════════════════════════
    // LAPOR RUANGAN BERANTAKAN
    // ════════════════════════════════════════

    /**
     * Cari peminjaman terakhir yang selesai (check-out) pada ruangan di hari ini,
     * lalu blokir user yang bersangkutan selama 30 hari.
     */
    public function laporBerantakan(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'feedback' => 'required|string|max:500',
        ]);

        try {
            $ruangan = Ruangan::findOrFail($id);

            // Cari peminjaman "selesai" paling terakhir pada ruangan ini hari ini
            $peminjaman = Peminjaman::where('ruangan_id', $ruangan->id)
                ->where('status', Peminjaman::STATUS_DONE)
                ->whereDate('tanggal_selesai', today())
                ->latest('completed_at')
                ->with('user')
                ->first();

            // Fallback: jika tidak ada yang selesai hari ini,
            // cari yang masih "sedang_dipinjam" pada ruangan hari ini
            if (!$peminjaman) {
                $peminjaman = Peminjaman::where('ruangan_id', $ruangan->id)
                    ->where('status', Peminjaman::STATUS_APPROVED)
                    ->whereDate('tanggal_mulai', '<=', today())
                    ->whereDate('tanggal_selesai', '>=', today())
                    ->latest('created_at')
                    ->with('user')
                    ->first();
            }

            if (!$peminjaman || !$peminjaman->user) {
                return redirect()->back()->with(
                    'error',
                    "Tidak ditemukan peminjaman terkait pada ruangan \"{$ruangan->nama}\" hari ini."
                );
            }

            $user = $peminjaman->user;

            // Jangan blokir admin
            if ($user->hasRole('admin')) {
                return redirect()->back()->with(
                    'error',
                    'Tidak dapat menerapkan sanksi ke akun admin.'
                );
            }

            // Jika user sudah diblokir, skip
            if ($user->isBlocked()) {
                return redirect()->back()->with(
                    'error',
                    "User \"{$user->name}\" sudah dalam status blokir."
                );
            }

            $user->blockFor(30, $request->input('feedback'));

            return redirect()->back()->with(
                'success',
                "User \"{$user->name}\" (peminjam terakhir ruangan \"{$ruangan->nama}\") telah diblokir selama 30 hari."
            );
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal melapor: ' . $e->getMessage());
        }
    }

    // ════════════════════════════════════════
    // PROFILE ADMIN
    // ════════════════════════════════════════

    public function profileEdit()
    {
        return Inertia::render('Admin/ProfileEdit', [
            'user' => auth()->user(),
        ]);
    }

    public function profileUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $user->name = $request->name;
        $user->nickname = $request->nickname;

        if ($user->email !== $request->email) {
            $user->email = $request->email;
            $user->email_verified_at = null;
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        }

        $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}