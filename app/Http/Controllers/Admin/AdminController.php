<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\StoreRuanganRequest;
use App\Http\Requests\UpdateBarangRequest;
use App\Http\Requests\UpdateRuanganRequest;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\BookingService;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
                    'id' => $p->id,
                    'user_name' => $p->user?->name ?? '-',
                    'nama_item' => $p->nama_item,
                    'tipe' => $p->tipe,
                    'tanggal_mulai' => $p->tanggal_mulai?->format('Y-m-d'),
                    'tanggal_selesai' => $p->tanggal_selesai?->format('Y-m-d'),
                    'jam_mulai' => $p->jam_mulai,
                    'jam_selesai' => $p->jam_selesai,
                    // Target datetime for countdown (ISO 8601)
                    'target_datetime' => $p->tanggal_selesai?->format('Y-m-d').'T'.$p->jam_selesai.':00',
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'pending' => $totalPending,
                'approved' => $totalApproved,
                'users' => $totalUsers,
                'ruangan' => $totalRuangan,
                'barang' => $totalBarang,
            ],
            'activePeminjamans' => $activePeminjamans,
        ]);
    }

    // ════════════════════════════════════════
    // KELOLA USER — CRUD Lengkap
    // ════════════════════════════════════════
    public function kelolaUser(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        // B-20: sebelumnya ->get() memuat SELURUH tabel users ke memori.
        $users = User::query()
            ->with('roles:id,name')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/KelolaUser', [
            'users' => $users,
            'filters' => ['search' => $search],
        ]);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            // Divalidasi terhadap tabel roles agar peran baru di P1 langsung berlaku
            // tanpa mengedit controller ini.
            'role' => ['required', Rule::exists('roles', 'name')],
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            // Peran HANYA disimpan lewat Spatie — kolom users.role sudah dihapus.
            $user->assignRole($request->role);

            return redirect()->back()->with('success', 'User berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan user: '.$e->getMessage());
        }
    }

    public function updateUser(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            // Divalidasi terhadap tabel roles agar peran baru di P1 langsung berlaku
            // tanpa mengedit controller ini.
            'role' => ['required', Rule::exists('roles', 'name')],
        ]);

        try {
            $user = User::findOrFail($id);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            $user->syncRoles([$request->role]);

            return redirect()->back()->with('success', 'User berhasil diperbarui!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui user: '.$e->getMessage());
        }
    }

    public function destroyUser(int $id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
            }

            $user->delete();

            return redirect()->back()->with('success', 'User berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus user: '.$e->getMessage());
        }
    }

    // ════════════════════════════════════════
    // KELOLA PEMINJAMAN
    // ════════════════════════════════════════
    public function kelolaPeminjaman(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');

        // B-20: sebelumnya ->get() memuat SELURUH tabel peminjamans ke memori.
        $peminjamans = Peminjaman::with(['user', 'ruangan', 'barang'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_item', 'like', "%{$search}%")
                        ->orWhere('nomor_surat', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->when(in_array($status, Peminjaman::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/KelolaPeminjaman', [
            'peminjamans' => $peminjamans,
            'filters' => ['search' => $search, 'status' => $status],
        ]);
    }

    public function setujuiPeminjaman(int $id): RedirectResponse
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            $this->bookingService->approveBooking($peminjaman);

            return redirect()->back()->with('success', 'Peminjaman berhasil disetujui!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui peminjaman: '.$e->getMessage());
        }
    }

    public function tolakPeminjaman(int $id): RedirectResponse
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            $this->bookingService->rejectBooking($peminjaman);

            return redirect()->back()->with('error', 'Peminjaman ditolak!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menolak peminjaman: '.$e->getMessage());
        }
    }

    /**
     * Admin menandai peminjaman selesai (validasi pengembalian).
     */
    public function selesaiPeminjaman(int $id): RedirectResponse
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            $this->bookingService->completeBooking($peminjaman);

            return redirect()->back()->with('success', 'Peminjaman berhasil diselesaikan dan stok dikembalikan!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menyelesaikan peminjaman: '.$e->getMessage());
        }
    }

    // ════════════════════════════════════════
    // KELOLA RUANGAN — CRUD Lengkap
    // ════════════════════════════════════════
    public function kelolaRuangan()
    {
        $ruangans = Ruangan::orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return Inertia::render('Admin/KelolaRuangan', [
            'ruangans' => $ruangans,
        ]);
    }

    public function storeRuangan(StoreRuanganRequest $request): RedirectResponse
    {
        try {
            $data = $request->only(['nama', 'kode', 'kapasitas', 'lokasi', 'deskripsi', 'status']);

            if ($request->hasFile('image_path')) {
                $data['image_path'] = ImageService::cropAndSave($request->file('image_path'), 'ruangan');
            }

            Ruangan::create($data);

            return redirect()->back()->with('success', 'Ruangan berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan ruangan: '.$e->getMessage())->withInput();
        }
    }

    public function updateRuangan(UpdateRuanganRequest $request, int $id): RedirectResponse
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
            return redirect()->back()->with('error', 'Gagal memperbarui ruangan: '.$e->getMessage())->withInput();
        }
    }

    public function destroyRuangan(int $id): RedirectResponse
    {
        try {
            $ruangan = Ruangan::findOrFail($id);

            ImageService::deleteOldImage($ruangan->image_path);

            $ruangan->delete();

            return redirect()->back()->with('success', 'Ruangan berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus ruangan: '.$e->getMessage());
        }
    }

    // ════════════════════════════════════════
    // KELOLA BARANG — CRUD Lengkap
    // ════════════════════════════════════════
    public function kelolaBarang()
    {
        $barangs = Barang::orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return Inertia::render('Admin/KelolaBarang', [
            'barangs' => $barangs,
        ]);
    }

    public function storeBarang(StoreBarangRequest $request): RedirectResponse
    {
        try {
            $data = $request->only(['nama', 'kode', 'stok_total', 'stok_tersedia', 'kategori', 'deskripsi', 'status']);

            if ($request->hasFile('image_path')) {
                $data['image_path'] = ImageService::cropAndSave($request->file('image_path'), 'barang');
            }

            Barang::create($data);

            return redirect()->back()->with('success', 'Barang berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan barang: '.$e->getMessage())->withInput();
        }
    }

    public function updateBarang(UpdateBarangRequest $request, int $id): RedirectResponse
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
            return redirect()->back()->with('error', 'Gagal memperbarui barang: '.$e->getMessage())->withInput();
        }
    }

    public function destroyBarang(int $id): RedirectResponse
    {
        try {
            $barang = Barang::findOrFail($id);

            ImageService::deleteOldImage($barang->image_path);

            $barang->delete();

            return redirect()->back()->with('success', 'Barang berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus barang: '.$e->getMessage());
        }
    }

    // ════════════════════════════════════════
    // LAPOR RUANGAN BERANTAKAN
    // ════════════════════════════════════════

    /**
     * B-05 DIHAPUS — "Lapor Berantakan".
     *
     * Metode lama menetapkan pelaku lewat TEBAKAN: mencari "peminjaman selesai
     * terakhir di ruangan ini hari ini", lalu memblokirnya 30 hari. Bila ada dua
     * kegiatan berurutan di ruangan yang sama, yang dihukum bisa orang yang
     * salah — dan tidak ada bukti apa pun yang tertaut.
     *
     * Penggantinya, "Catat Temuan", mewajibkan petugas memilih pengajuan/baris
     * aset terkait sehingga pelaku diambil dari data. Itu bergantung pada alur
     * pemeriksaan di P5 dan dibangun di sana.
     *
     * Sengaja tidak diganti dengan versi sementara: lebih baik tidak ada fitur
     * daripada fitur yang memblokir orang tak bersalah.
     */

    // ════════════════════════════════════════
    // PROFILE ADMIN
    // ════════════════════════════════════════

    public function profileEdit()
    {
        return Inertia::render('Admin/ProfileEdit', [
            'user' => auth()->user(),
        ]);
    }

    public function profileUpdate(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
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
            $user->avatar = '/storage/'.$path;
        }

        $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}
