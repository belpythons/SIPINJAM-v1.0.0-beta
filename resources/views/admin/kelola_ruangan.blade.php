<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ruangan - SiPinjam Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-sp.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { DEFAULT: '#ea580c', dark: '#c2410c', light: '#f97316' } } } } }
    </script>
</head>
<body class="bg-[#f8f9fa] text-gray-800 font-sans flex h-screen overflow-hidden">

    @include('admin.partials.sidebar', ['activePage' => 'ruangan'])

    <main class="flex-1 lg:ml-64 h-screen overflow-y-auto flex flex-col relative">
        @include('admin.partials.navbar')
        <div class="p-8 flex-1">

        {{-- Toast Notification --}}
        @if(session('success'))
            <div id="toast" class="fixed top-6 right-6 z-[60] bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button onclick="document.getElementById('toast').remove()" class="ml-2 opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div id="toast" class="fixed top-6 right-6 z-[60] bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
                <button onclick="document.getElementById('toast').remove()" class="ml-2 opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Kelola Ruangan</h2>
                <p class="text-gray-500 mt-1">Kelola semua ruangan dalam sistem</p>
            </div>
            <button onclick="openAddModal()" class="bg-brand hover:bg-brand-dark text-white px-5 py-2.5 rounded-lg font-medium shadow-md transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Ruangan
            </button>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <span class="text-gray-800 font-semibold text-sm">Total Ruangan</span>
                <span class="text-4xl font-bold text-gray-900 mt-4">{{ $ruangans->count() }}</span>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <span class="text-gray-800 font-semibold text-sm">Tersedia</span>
                <span class="text-4xl font-bold text-green-600 mt-4">{{ $ruangans->where('status', 'tersedia')->count() }}</span>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <span class="text-gray-800 font-semibold text-sm">Tidak Tersedia</span>
                <span class="text-4xl font-bold text-red-600 mt-4">{{ $ruangans->where('status', 'tidak_tersedia')->count() }}</span>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8 flex-1">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Nama</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kode</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kapasitas</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Lokasi</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ruangans as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($r->foto)
                                            <img src="{{ asset('storage/' . $r->foto) }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center"><i class="fas fa-building text-gray-400"></i></div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $r->nama }}</p>
                                            <p class="text-xs text-gray-500">{{ Str::limit($r->deskripsi, 40) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $r->kode }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $r->kapasitas }} orang</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $r->lokasi ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $r->status === 'tersedia' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick='openEditModal(@json($r))' class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium text-blue-600 hover:bg-blue-50 transition-colors">
                                        <i class="fas fa-pencil text-xs"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.ruangan.destroy', $r->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus ruangan {{ $r->nama }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-trash text-xs"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">Belum ada data ruangan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        </div>
        <footer class="mt-auto pt-4 pb-2 px-8 flex justify-between items-center text-[13px] text-gray-500 border-t border-gray-200/60">
            <p>&copy; 2026 SiPinjam</p>
            <p>Built with Laravel</p>
        </footer>
    </main>

    {{-- MODAL TAMBAH --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-[550px] overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Tambah Ruangan Baru</h3>
                    <p class="text-[13px] text-gray-500 mt-1">Lengkapi data ruangan</p>
                </div>
                <button onclick="closeModal('modalTambah')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
            </div>
            <form action="{{ route('admin.ruangan.store') }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">Foto Ruangan</label>
                    <input type="file" name="foto" accept="image/*" class="w-full text-sm border border-gray-200 rounded-lg p-2">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Nama *</label>
                        <input type="text" name="nama" required placeholder="Lab Komputer A" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Kode *</label>
                        <input type="text" name="kode" required placeholder="LAB-A" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Kapasitas *</label>
                        <input type="number" name="kapasitas" required min="1" value="1" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Status *</label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="tersedia">Tersedia</option>
                            <option value="tidak_tersedia">Tidak Tersedia</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">Lokasi</label>
                    <input type="text" name="lokasi" placeholder="Gedung A, Lantai 2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" placeholder="Deskripsi ruangan..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('modalTambah')" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm">Tambah Ruangan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="modalEdit" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-[550px] overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Edit Ruangan</h3>
                    <p class="text-[13px] text-gray-500 mt-1">Perbarui data ruangan</p>
                </div>
                <button onclick="closeModal('modalEdit')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">Foto Ruangan</label>
                    <input type="file" name="foto" accept="image/*" class="w-full text-sm border border-gray-200 rounded-lg p-2">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Nama *</label>
                        <input type="text" name="nama" id="editNama" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Kode *</label>
                        <input type="text" name="kode" id="editKode" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Kapasitas *</label>
                        <input type="number" name="kapasitas" id="editKapasitas" required min="1" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Status *</label>
                        <select name="status" id="editStatus" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="tersedia">Tersedia</option>
                            <option value="tidak_tersedia">Tidak Tersedia</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">Lokasi</label>
                    <input type="text" name="lokasi" id="editLokasi" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="editDeskripsi" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('modalEdit')" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }
    </style>

    <script>
        function openAddModal() { document.getElementById('modalTambah').classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        function openEditModal(data) {
            document.getElementById('editForm').action = '/admin/kelola-ruangan/' + data.id;
            document.getElementById('editNama').value = data.nama;
            document.getElementById('editKode').value = data.kode;
            document.getElementById('editKapasitas').value = data.kapasitas;
            document.getElementById('editLokasi').value = data.lokasi || '';
            document.getElementById('editDeskripsi').value = data.deskripsi || '';
            document.getElementById('editStatus').value = data.status;
            document.getElementById('modalEdit').classList.remove('hidden');
        }

        // Close modal on backdrop click
        ['modalTambah', 'modalEdit'].forEach(id => {
            document.getElementById(id)?.addEventListener('click', function(e) {
                if (e.target === this) closeModal(id);
            });
        });

        // Auto-dismiss toast
        setTimeout(() => { document.getElementById('toast')?.remove(); }, 4000);
    </script>
</body>
</html>