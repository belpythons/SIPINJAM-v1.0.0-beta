<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - SiPinjam Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-sp.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { DEFAULT: '#ea580c', dark: '#c2410c', light: '#f97316' } } } } }
    </script>
</head>
<body class="bg-[#f8f9fa] text-gray-800 font-sans flex h-screen overflow-hidden">

    @include('admin.partials.sidebar', ['activePage' => 'user'])

    <main class="flex-1 lg:ml-64 h-screen overflow-y-auto flex flex-col relative">
        @include('admin.partials.navbar')
        <div class="p-8 flex-1">

        {{-- Toast --}}
        @if(session('success'))
            <div id="toast" class="fixed top-6 right-6 z-[60] bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in">
                <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
                <button onclick="document.getElementById('toast').remove()" class="ml-2 opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div id="toast" class="fixed top-6 right-6 z-[60] bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in">
                <i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span>
                <button onclick="document.getElementById('toast').remove()" class="ml-2 opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Kelola User</h2>
                <p class="text-gray-500 mt-1">Kelola pengguna sistem SiPinjam</p>
            </div>
            <button onclick="openAddModal()" class="bg-brand hover:bg-brand-dark text-white px-5 py-2.5 rounded-lg font-medium shadow-md transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <span class="text-gray-800 font-semibold text-sm">Total User</span>
                <span class="text-4xl font-bold text-gray-900 mt-4">{{ $users->count() }}</span>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <span class="text-gray-800 font-semibold text-sm">Admin</span>
                <span class="text-4xl font-bold text-orange-600 mt-4">{{ $users->where('role', 'admin')->count() }}</span>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <span class="text-gray-800 font-semibold text-sm">User Biasa</span>
                <span class="text-4xl font-bold text-blue-600 mt-4">{{ $users->where('role', 'user')->count() }}</span>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8 flex-1">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Nama</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Email</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Role</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Login via</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Terdaftar</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $u)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($u->avatar)
                                            <img src="{{ $u->avatar }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-semibold text-sm">
                                                {{ strtoupper(substr($u->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <span class="font-medium text-gray-900">{{ $u->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $u->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $u->role === 'admin' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @if($u->google_id)
                                        <span class="inline-flex items-center gap-1 text-xs"><i class="fab fa-google text-red-500"></i> Google</span>
                                    @else
                                        <span class="text-xs">Email</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $u->created_at?->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick='openEditModal(@json($u))' class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium text-blue-600 hover:bg-blue-50 transition-colors">
                                        <i class="fas fa-pencil text-xs"></i> Edit
                                    </button>
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('admin.user.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user {{ $u->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                                <i class="fas fa-trash text-xs"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">Belum ada data user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        </div>
        <footer class="mt-auto pt-4 pb-2 px-8 flex justify-between items-center text-[13px] text-gray-500 border-t border-gray-200/60">
            <p>&copy; 2026 SiPinjam</p><p>Built with Laravel</p>
        </footer>
    </main>

    {{-- MODAL TAMBAH --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-[480px] overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-start">
                <div><h3 class="text-xl font-bold text-gray-900">Tambah User Baru</h3><p class="text-[13px] text-gray-500 mt-1">Buat akun pengguna baru</p></div>
                <button onclick="closeModal('modalTambah')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
            </div>
            <form action="{{ route('admin.user.store') }}" method="POST" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <div><label class="block text-sm font-medium text-gray-900 mb-1">Nama Lengkap *</label><input type="text" name="name" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></div>
                <div><label class="block text-sm font-medium text-gray-900 mb-1">Email *</label><input type="email" name="email" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></div>
                <div><label class="block text-sm font-medium text-gray-900 mb-1">Password *</label><input type="password" name="password" required minlength="6" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></div>
                <div><label class="block text-sm font-medium text-gray-900 mb-1">Role *</label><select name="role" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white"><option value="user">User</option><option value="admin">Admin</option></select></div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('modalTambah')" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm">Tambah User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="modalEdit" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-[480px] overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-start">
                <div><h3 class="text-xl font-bold text-gray-900">Edit User</h3><p class="text-[13px] text-gray-500 mt-1">Perbarui data pengguna</p></div>
                <button onclick="closeModal('modalEdit')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
            </div>
            <form id="editForm" method="POST" class="p-6 overflow-y-auto space-y-4">
                @csrf @method('PUT')
                <div><label class="block text-sm font-medium text-gray-900 mb-1">Nama Lengkap *</label><input type="text" name="name" id="editName" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></div>
                <div><label class="block text-sm font-medium text-gray-900 mb-1">Email *</label><input type="email" name="email" id="editEmail" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">Password Baru <span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" minlength="6" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div><label class="block text-sm font-medium text-gray-900 mb-1">Role *</label><select name="role" id="editRole" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white"><option value="user">User</option><option value="admin">Admin</option></select></div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('modalEdit')" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <style>@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } } .animate-slide-in { animation: slideIn 0.3s ease-out; }</style>
    <script>
        function openAddModal() { document.getElementById('modalTambah').classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        function openEditModal(data) {
            document.getElementById('editForm').action = '/admin/kelola-user/' + data.id;
            document.getElementById('editName').value = data.name;
            document.getElementById('editEmail').value = data.email;
            document.getElementById('editRole').value = data.role;
            document.getElementById('modalEdit').classList.remove('hidden');
        }
        ['modalTambah', 'modalEdit'].forEach(id => {
            document.getElementById(id)?.addEventListener('click', function(e) { if (e.target === this) closeModal(id); });
        });
        setTimeout(() => { document.getElementById('toast')?.remove(); }, 4000);
    </script>
</body>
</html>