<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - SIPINJAM</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { user: { primary: '#3b82f6', dark: '#1e3a8a' } },
                    animation: {
                        'slide-up': 'slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards',
                        'fade-in': 'fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards',
                    },
                    keyframes: {
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .animate-delay-100 { animation-delay: 100ms; opacity: 0; }
        .animate-delay-200 { animation-delay: 200ms; opacity: 0; }
        .animate-delay-300 { animation-delay: 300ms; opacity: 0; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    
 {{-- SIDEBAR --}}
        <aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-gradient-to-b from-[#3b82f6] to-[#1e3a8a] text-white transition-all duration-300 flex flex-col hidden lg:flex">
            {{-- Logo --}}
            <div class="flex h-16 items-center gap-3 px-6 flex-shrink-0">
                {{-- Memanggil logo gambar SP --}}
                <img src="{{ asset('image/logo-sp.png') }}" alt="Logo SIPINJAM" class="w-10 h-auto drop-shadow-md">
                <h1 class="text-2xl font-bold tracking-tight">SIPINJAM</h1>
            </div>

        <div class="px-4 pb-4 flex-shrink-0">
            <div class="flex items-center gap-3 rounded-lg px-3 py-2.5">
                <div class="h-10 w-10 rounded-full bg-blue-500 border-2 border-white/30 flex items-center justify-center font-semibold text-white text-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'JD', 0, 2)) }}
                </div>
                <div class="flex-1 text-left">
                    <p class="text-sm font-semibold text-white">{{ auth()->user()->name ?? 'John Doe' }}</p>
                    <p class="text-xs text-white/70 truncate">{{ auth()->user()->email ?? 'user@sipinjam.ac.id' }}</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 space-y-1 px-3 py-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white shadow-sm scale-105' : 'text-white/80 hover:bg-white/10 hover:text-white hover:scale-105' }}">
                <i data-lucide="home" class="h-5 w-5 flex-shrink-0"></i> Dashboard
            </a>
            <a href="{{ route('bookings.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('bookings.*') ? 'bg-white/20 text-white shadow-sm scale-105' : 'text-white/80 hover:bg-white/10 hover:text-white hover:scale-105' }}">
                <i data-lucide="calendar" class="h-5 w-5 flex-shrink-0"></i> Riwayat Peminjaman
            </a>
            <a href="{{ route('ruangan.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('ruangan.*') ? 'bg-white/20 text-white shadow-sm scale-105' : 'text-white/80 hover:bg-white/10 hover:text-white hover:scale-105' }}">
                <i data-lucide="door-open" class="h-5 w-5 flex-shrink-0"></i> Ruangan
            </a>
            <a href="{{ route('barang.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('barang.*') ? 'bg-white/20 text-white shadow-sm scale-105' : 'text-white/80 hover:bg-white/10 hover:text-white hover:scale-105' }}">
                <i data-lucide="package" class="h-5 w-5 flex-shrink-0"></i> Barang
            </a>
            <a href="{{ Route::has('tata_tertib.index') ? route('tata_tertib.index') : '#' }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tata_tertib.*') ? 'bg-white/20 text-white shadow-sm scale-105' : 'text-white/80 hover:bg-white/10 hover:text-white hover:scale-105' }}">
                <i data-lucide="book-open" class="h-5 w-5 flex-shrink-0"></i> Tata Tertib
            </a>
        </nav>

        <div class="border-t border-white/10 p-4 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white transition-colors">
                    <i data-lucide="log-out" class="h-5 w-5"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto lg:ml-64 bg-gray-50 transition-all duration-300">
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b bg-white px-4 lg:px-6">
            <h2 class="text-base font-semibold text-gray-700">User Portal</h2>
            <button class="lg:hidden p-2 rounded-md hover:bg-gray-100"><i data-lucide="menu" class="h-6 w-6"></i></button>
        </header>

        {{-- TAMPILAN PESAN SUKSES & ERROR --}}
        @if(session('success'))
            <div class="m-4 lg:mx-8 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative flex items-center gap-3 animate-fade-in shadow-sm">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                <span class="block sm:inline font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="m-4 lg:mx-8 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl animate-fade-in shadow-sm">
                <div class="flex items-center gap-2 font-semibold text-sm mb-2">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i> Gagal menyimpan peminjaman:
                </div>
                <ul class="list-disc list-inside text-sm space-y-1 ml-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- AKHIR TAMPILAN PESAN --}}

        <div class="p-4 sm:p-6 lg:p-8 space-y-6 animate-fade-in">
            <div class="flex items-start justify-between animate-slide-up">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Riwayat Peminjaman</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola dan lihat riwayat peminjaman Anda</p>
                </div>
                <button onclick="toggleModal('bookingModal')" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg inline-flex items-center gap-2 shadow-sm transition-colors cursor-pointer">
                    <i data-lucide="plus" class="h-4 w-4"></i> Peminjaman Baru
                </button>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-slide-up animate-delay-100">
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-500">Total Peminjaman</p>
                        <i data-lucide="calendar" class="h-5 w-5 text-gray-400"></i>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-500">Menunggu</p>
                        <i data-lucide="clock" class="h-5 w-5 text-yellow-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-yellow-500">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-500">Sedang Dipinjam</p>
                        <i data-lucide="trending-up" class="h-5 w-5 text-green-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-green-500">{{ $stats['approved'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-500">Selesai</p>
                        <i data-lucide="calendar-check" class="h-5 w-5 text-blue-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-blue-500">{{ $stats['completed'] ?? 0 }}</p>
                </div>
            </div>

            <div class="animate-slide-up animate-delay-200">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Peminjaman</h3>
                        <div class="flex items-center gap-1 border border-gray-200 rounded-lg p-1">
                            <button id="btn-grid" onclick="setView('grid')" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium bg-gray-100 text-gray-700 transition-colors">
                                <i data-lucide="layout-grid" class="h-4 w-4"></i> Grid
                            </button>
                            <button id="btn-table" onclick="setView('table')" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
                                <i data-lucide="list" class="h-4 w-4"></i> Tabel
                            </button>
                        </div>
                    </div>

                    <div id="view-grid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse($bookings ?? [] as $item)
                            @php
                                $badgeColor = match($item->status) {
                                    'menunggu'        => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                                    'sedang_dipinjam' => 'bg-green-100 text-green-700 border border-green-200',
                                    'selesai'         => 'bg-gray-100 text-gray-600 border border-gray-200',
                                    'ditolak'         => 'bg-red-100 text-red-600 border border-red-200',
                                    default           => 'bg-gray-100 text-gray-600 border border-gray-200',
                                };
                                $statusLabel = match($item->status) {
                                    'menunggu'        => 'Menunggu',
                                    'sedang_dipinjam' => 'Sedang Dipinjam',
                                    'selesai'         => 'Selesai',
                                    'ditolak'         => 'Ditolak',
                                    default           => ucfirst($item->status),
                                };
                                $statusIcon = match($item->status) {
                                    'menunggu'        => 'clock',
                                    'sedang_dipinjam' => 'check-circle',
                                    'selesai'         => 'check-circle-2',
                                    'ditolak'         => 'x-circle',
                                    default           => 'circle',
                                };
                            @endphp
                            <div class="rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col bg-white">
                                <div class="{{ in_array($item->status, ['disetujui', 'sedang_dipinjam', 'selesai']) ? 'bg-green-500' : 'bg-blue-600' }} px-4 py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-white/90 text-sm font-medium">
                                        <i data-lucide="{{ $item->tipe_peminjaman == 'ruangan' ? 'door-open' : 'package' }}" class="h-4 w-4"></i> 
                                        {{ ucfirst($item->tipe_peminjaman ?? 'Ruangan') }}
                                    </div>
                                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badgeColor }} bg-white">
                                        <i data-lucide="{{ $statusIcon }}" class="h-3 w-3 inline-block mr-1"></i> {{ $statusLabel }}
                                    </span>
                                </div>
                                
                                <div class="{{ in_array($item->status, ['disetujui', 'sedang_dipinjam', 'selesai']) ? 'bg-green-500' : 'bg-blue-600' }} px-4 pb-4">
                                    <h4 class="text-lg font-bold text-white truncate">
                                        {{ $item->nama_item ?? 'Item Peminjaman' }}
                                    </h4>
                                </div>
                                
                                <div class="p-4 bg-white space-y-3 flex-1">
                                    <div class="flex items-start gap-2 text-sm text-gray-600">
                                        <i data-lucide="calendar" class="h-4 w-4 text-gray-400 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <p class="text-xs text-gray-400">Tanggal</p>
                                            <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-2 text-sm text-gray-600">
                                        <i data-lucide="clock" class="h-4 w-4 text-gray-400 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <p class="text-xs text-gray-400">Durasi</p>
                                            <p class="font-medium text-gray-900">
                                                {{ $item->jam_mulai ? \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') : '-' }} - 
                                                {{ $item->jam_selesai ? \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') : '-' }} 
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <hr class="border-gray-100 my-2">
                                    <p class="text-sm text-gray-500 truncate">{{ $item->keterangan ?? '-' }}</p>
                                </div>

                                <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex gap-3">
                                    <button onclick="openDetailModal({{ json_encode($item) }})" class="flex-1 flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                                        <i data-lucide="eye" class="w-4 h-4"></i> Detail
                                    </button>

                                    @if(in_array($item->status, ['disetujui', 'sedang_dipinjam', 'selesai']))
                                        <a href="{{ route('bookings.pdf', $item->id) }}" target="_blank" class="flex-1 flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                                            <i data-lucide="download" class="w-4 h-4"></i> PDF
                                        </a>
                                    @else
                                        <button disabled class="flex-1 flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                            <i data-lucide="download" class="w-4 h-4"></i> PDF
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="lg:col-span-3 text-center py-16 text-gray-400">
                                <i data-lucide="calendar" class="h-12 w-12 mx-auto mb-3 opacity-30"></i>
                                <p class="text-base font-medium">Belum ada peminjaman</p>
                                <p class="text-sm mt-1">Ajukan peminjaman pertama Anda</p>
                            </div>
                        @endforelse
                    </div>

                    <div id="view-table" class="hidden overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Mulai</th>
                                    <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Selesai</th>
                                    <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Keterangan</th>
                                    <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Diajukan</th>
                                    <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($bookings ?? [] as $item)
                                @php
                                    $badgeColor = match($item->status) {
                                        'menunggu'        => 'bg-yellow-100 text-yellow-700',
                                        'sedang_dipinjam' => 'bg-green-100 text-green-700',
                                        'selesai'         => 'bg-gray-100 text-gray-600',
                                        'ditolak'         => 'bg-red-100 text-red-600',
                                        default           => 'bg-gray-100 text-gray-600',
                                    };
                                    $statusLabel = match($item->status) {
                                        'menunggu'        => 'Menunggu',
                                        'sedang_dipinjam' => 'Sedang Dipinjam',
                                        'selesai'         => 'Selesai',
                                        'ditolak'         => 'Ditolak',
                                        default           => ucfirst($item->status),
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 pr-4 font-medium text-gray-900">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</td>
                                    <td class="py-3 pr-4 text-gray-600">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</td>
                                    <td class="py-3 pr-4 text-gray-600 max-w-xs truncate">{{ $item->keterangan ?? '-' }}</td>
                                    <td class="py-3 pr-4 text-gray-500 text-xs">{{ $item->created_at->diffForHumans() }}</td>
                                    <td class="py-3 pr-4"><span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badgeColor }}">{{ $statusLabel }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="py-12 text-center text-gray-400">Belum ada peminjaman</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <footer class="mt-auto px-6 py-4 border-t border-gray-200 text-xs text-gray-400 flex justify-between bg-white">
            <span>© 2026 SIPINJAM - Sistem Informasi Peminjaman Ruangan dan Barang</span>
            <span>Built with Laravel</span>
        </footer>
    </main>
</div>

{{-- MODAL PEMINJAMAN BARU (Multi-Step dengan Form Submit) --}}
<div id="bookingModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/40 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 sm:p-8 transform transition-all relative">
        <button type="button" onclick="toggleModal('bookingModal')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <h2 class="text-xl font-bold text-gray-900">Ajukan Peminjaman Baru</h2>
        <p class="text-sm text-gray-500 mt-1 mb-8">Lengkapi form berikut untuk mengajukan peminjaman</p>

        <div class="flex items-center justify-center gap-3 mb-8">
            <div id="step_1_circle" class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-sm shadow-sm transition-colors">1</div>
            <div id="step_2_circle" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center font-medium text-sm transition-colors">2</div>
            <div id="step_3_circle" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center font-medium text-sm transition-colors">3</div>
        </div>

        {{-- FORM UTAMA DIMULAI DI SINI --}}
        {{-- Pastikan route('bookings.store') sudah ada di web.php milikmu --}}
        <form action="{{ route('bookings.store') }}" method="POST" id="formPeminjaman">
            @csrf
            
            {{-- Input Hidden untuk menyimpan nilai dari Custom Dropdown --}}
            <input type="hidden" name="tipe_peminjaman" id="input_tipe_peminjaman" value="barang">
            <input type="hidden" name="nama_item" id="input_nama_item" value="">

            {{-- ======================== STEP 1: PILIH ITEM ======================== --}}
            <div id="step_1_form" class="space-y-6">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-900 mb-2">Tipe Peminjaman</label>
                    <button type="button" id="btn_tipe" onclick="toggleCustomDropdown('menu_tipe')" class="bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none w-48 px-4 py-2.5 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-colors shadow-sm">
                        <div class="flex items-center gap-2">
                            <span id="tipe_icon_container"><i data-lucide="package" class="w-4 h-4 text-gray-500"></i></span>
                            <span id="text_tipe" class="font-medium text-gray-900">Barang</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div id="menu_tipe" class="hidden absolute z-50 w-48 mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl py-2 p-1">
                        <ul class="text-sm text-gray-700 space-y-0.5">
                            <li><button type="button" id="option_tipe_ruangan" onclick="pilihTipe('ruangan')" class="w-full px-3 py-2 rounded-xl hover:bg-gray-50 transition-colors flex items-center justify-between"><div class="flex items-center gap-2"><i data-lucide="door-open" class="w-4 h-4 text-gray-500"></i><span class="font-medium text-gray-700">Ruangan</span></div><i id="check_tipe_ruangan" data-lucide="check" class="w-4 h-4 text-gray-900 hidden"></i></button></li>
                            <li><button type="button" id="option_tipe_barang" onclick="pilihTipe('barang')" class="w-full px-3 py-2 rounded-xl bg-gray-50 hover:bg-gray-50 transition-colors flex items-center justify-between"><div class="flex items-center gap-2"><i data-lucide="package" class="w-4 h-4 text-gray-500"></i><span class="font-medium text-gray-700">Barang</span></div><i id="check_tipe_barang" data-lucide="check" class="w-4 h-4 text-gray-900"></i></button></li>
                        </ul>
                    </div>
                </div>

                <div id="div_ruangan" class="relative hidden">
                    <label class="block text-sm font-medium text-gray-900 mb-2">Pilih Ruangan</label>
                    <button type="button" id="btn_ruangan" onclick="toggleCustomDropdown('menu_ruangan')" class="bg-white border border-gray-200 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none w-48 sm:w-64 px-4 py-2.5 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-colors shadow-sm">
                        <span id="text_ruangan" class="text-gray-500">Pilih item</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div id="menu_ruangan" class="hidden absolute z-50 w-full sm:w-80 mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl py-2">
                        <ul class="text-sm text-gray-700">
                            <li><button type="button" onclick="pilihItem('ruangan', 'Ruang Kuliah 301', 'Kapasitas: 50 orang', 'Lokasi: Gedung A, Lantai 3')" class="w-full text-left px-5 py-2.5 hover:bg-gray-50 transition-colors">Ruang Kuliah 301 (50 orang)</button></li>
                            <li><button type="button" onclick="pilihItem('ruangan', 'Ruang Seminar', 'Kapasitas: 100 orang', 'Lokasi: Gedung B, Lantai 1')" class="w-full text-left px-5 py-2.5 hover:bg-gray-50 transition-colors">Ruang Seminar (100 orang)</button></li>
                        </ul>
                    </div>
                </div>

                <div id="div_barang" class="relative">
                    <label class="block text-sm font-medium text-gray-900 mb-2">Pilih Barang</label>
                    <button type="button" id="btn_barang" onclick="toggleCustomDropdown('menu_barang')" class="bg-white border border-gray-200 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none w-48 sm:w-64 px-4 py-2.5 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-colors shadow-sm">
                        <span id="text_barang" class="text-gray-500">Pilih item</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div id="menu_barang" class="hidden absolute z-50 w-full sm:w-80 mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl py-2">
                        <ul class="text-sm text-gray-700">
                            <li><button type="button" onclick="pilihItem('barang', 'Proyektor', 'Kategori: Elektronik', 'Tersedia: 10/10')" class="w-full text-left px-5 py-2.5 hover:bg-gray-50 transition-colors">Proyektor (10/10 tersedia)</button></li>
                            <li><button type="button" onclick="pilihItem('barang', 'Laptop', 'Kategori: Komputer', 'Tersedia: 5/5')" class="w-full text-left px-5 py-2.5 hover:bg-gray-50 transition-colors">Laptop (5/5 tersedia)</button></li>
                        </ul>
                    </div>
                </div>

                <div id="detail_item_box" class="hidden border border-gray-200 rounded-2xl p-5 mt-4 transition-all duration-300">
                    <h4 class="text-base font-bold text-gray-900 mb-3">Detail Item</h4>
                    <div class="space-y-1.5 text-sm text-gray-500">
                        <p id="detail_line_1"></p>
                        <p id="detail_line_2"></p>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="button" onclick="nextStep(2)" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm px-6 py-2.5 flex items-center gap-2 transition-colors">
                        Selanjutnya <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            {{-- ======================== STEP 2: PILIH WAKTU ======================== --}}
            <div id="step_2_form" class="space-y-6 hidden animate-fade-in">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Tanggal Mulai</label>
                        {{-- Ditambahkan name="tanggal_mulai" --}}
                        <input type="date" name="tanggal_mulai" required class="bg-white border border-gray-200 text-sm rounded-xl w-full px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer text-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Waktu Mulai</label>
                        {{-- Ditambahkan name="waktu_mulai" --}}
                        <input type="time" name="waktu_mulai" required class="bg-white border border-gray-200 text-sm rounded-xl w-full px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer text-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Tanggal Selesai</label>
                        {{-- Ditambahkan name="tanggal_selesai" --}}
                        <input type="date" name="tanggal_selesai" required class="bg-white border border-gray-200 text-sm rounded-xl w-full px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer text-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Waktu Selesai</label>
                        {{-- Ditambahkan name="waktu_selesai" --}}
                        <input type="time" name="waktu_selesai" required class="bg-white border border-gray-200 text-sm rounded-xl w-full px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer text-gray-700">
                    </div>
                </div>

                <div class="mt-8 pt-2 flex justify-between">
                    <button type="button" onclick="nextStep(1)" class="bg-white border border-gray-200 text-gray-700 font-medium rounded-xl text-sm px-6 py-2.5 flex items-center gap-2 hover:bg-gray-50 transition-colors">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i> Sebelumnya
                    </button>
                    <button type="button" onclick="nextStep(3)" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm px-6 py-2.5 flex items-center gap-2 transition-colors">
                        Selanjutnya <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            {{-- ======================== STEP 3: TUJUAN & CATATAN ======================== --}}
            <div id="step_3_form" class="space-y-6 hidden animate-fade-in">
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-2">Tujuan Peminjaman</label>
                    {{-- Ditambahkan name="keterangan" --}}
                    <textarea name="keterangan" rows="4" required placeholder="Jelaskan tujuan peminjaman..." class="bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none block w-full p-4 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-2">Catatan (Opsional)</label>
                    {{-- Ditambahkan name="catatan" --}}
                    <textarea name="catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..." class="bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none block w-full p-4 resize-none"></textarea>
                </div>

                <div class="mt-8 pt-2 flex justify-between">
                    <button type="button" onclick="nextStep(2)" class="bg-white border border-gray-200 text-gray-700 font-medium rounded-xl text-sm px-6 py-2.5 flex items-center gap-2 hover:bg-gray-50 transition-colors">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i> Sebelumnya
                    </button>
                    {{-- Tombol submit form yang sebenarnya --}}
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm px-8 py-2.5 transition-colors shadow-sm">
                        Ajukan Peminjaman
                    </button>
                </div>
            </div>
        </form> {{-- AKHIR DARI FORM --}}
    </div>
</div>

{{-- MODAL DETAIL PEMINJAMAN --}}
<div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/40 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden flex flex-col max-h-[90vh]">
        
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-white relative z-10">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5 text-yellow-500" id="detail_icon_status"></i>
                <h2 class="text-lg font-bold text-gray-900">Detail Peminjaman</h2>
            </div>
            <button type="button" onclick="toggleModal('detailModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="p-5 overflow-y-auto bg-gray-50/50 space-y-4">
            <p class="text-xs text-gray-400 mb-2"># ID: booking-<span id="detail_id"></span></p>

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center justify-between" id="detail_status_card">
                <div>
                    <p class="text-xs font-medium text-yellow-800 mb-1">Status Peminjaman</p>
                    <h3 class="text-lg font-bold text-yellow-900" id="detail_status_text">Menunggu Persetujuan</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600" id="detail_status_icon_bg">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-4 text-blue-600 font-semibold">
                    <i data-lucide="door-open" class="w-5 h-5" id="detail_tipe_icon"></i>
                    <span id="detail_tipe_title">Informasi Ruangan</span>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg">
                        <span class="text-xs text-gray-500">Nama <span id="detail_label_item">Ruangan</span></span>
                        <span class="text-sm font-semibold text-gray-900" id="detail_nama_item">Ruang Seminar</span>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg">
                        <span class="text-xs text-gray-500">Tipe Peminjaman</span>
                        <span class="text-sm font-semibold text-gray-900" id="detail_tipe_value">Ruangan</span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-4 text-blue-600 font-semibold">
                    <i data-lucide="clock" class="w-5 h-5"></i> Jadwal Peminjaman
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="border border-gray-100 rounded-lg p-3">
                        <p class="text-xs text-gray-500 flex items-center gap-1 mb-1"><i data-lucide="calendar" class="w-3 h-3"></i> Waktu Mulai</p>
                        <p class="text-sm font-bold text-gray-900" id="detail_tgl_mulai">29 April 2026</p>
                        <p class="text-xs text-gray-400 mt-0.5" id="detail_jam_mulai">21:27 WIB</p>
                    </div>
                    <div class="border border-gray-100 rounded-lg p-3">
                        <p class="text-xs text-gray-500 flex items-center gap-1 mb-1"><i data-lucide="calendar" class="w-3 h-3"></i> Waktu Selesai</p>
                        <p class="text-sm font-bold text-gray-900" id="detail_tgl_selesai">29 April 2026</p>
                        <p class="text-xs text-gray-400 mt-0.5" id="detail_jam_selesai">22:28 WIB</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-3 text-blue-600 font-semibold">
                    <i data-lucide="file-text" class="w-5 h-5"></i> Tujuan Peminjaman
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-700" id="detail_tujuan">
                    test 456789
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
function openDetailModal(item) {
    // 1. Tampilkan ID unik (bisa pakai timestamp atau ID asli)
    document.getElementById('detail_id').innerText = item.id + Math.floor(Math.random() * 1000000000);

    // 2. Set Status Warna & Teks
    const statusCard = document.getElementById('detail_status_card');
    const statusText = document.getElementById('detail_status_text');
    const statusIconBg = document.getElementById('detail_status_icon_bg');
    
    if (item.status === 'menunggu') {
        statusCard.className = "bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center justify-between";
        statusText.className = "text-lg font-bold text-yellow-900";
        statusText.innerText = "Menunggu Persetujuan";
        statusIconBg.className = "w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600";
        statusIconBg.innerHTML = '<i data-lucide="clock" class="w-5 h-5"></i>';
    } else if (item.status === 'disetujui' || item.status === 'sedang_dipinjam' || item.status === 'selesai') {
        statusCard.className = "bg-green-50 border border-green-200 rounded-xl p-4 flex items-center justify-between";
        statusText.className = "text-lg font-bold text-green-900";
        statusText.innerText = "Disetujui";
        statusIconBg.className = "w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600";
        statusIconBg.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5"></i>';
    } else {
        statusCard.className = "bg-red-50 border border-red-200 rounded-xl p-4 flex items-center justify-between";
        statusText.className = "text-lg font-bold text-red-900";
        statusText.innerText = "Ditolak";
        statusIconBg.className = "w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600";
        statusIconBg.innerHTML = '<i data-lucide="x-circle" class="w-5 h-5"></i>';
    }

    // 3. Set Tipe (Ruangan/Barang)
    const isRuangan = item.tipe_peminjaman === 'ruangan';
    document.getElementById('detail_tipe_title').innerText = isRuangan ? 'Informasi Ruangan' : 'Informasi Barang';
    document.getElementById('detail_label_item').innerText = isRuangan ? 'Ruangan' : 'Barang';
    document.getElementById('detail_tipe_value').innerText = isRuangan ? 'Ruangan' : 'Barang';
    document.getElementById('detail_tipe_icon').setAttribute('data-lucide', isRuangan ? 'door-open' : 'package');
    document.getElementById('detail_nama_item').innerText = item.nama_item || '-';

    // 4. Set Jadwal (Gunakan format JS sederhana atau biarkan format mentah sementara)
    document.getElementById('detail_tgl_mulai').innerText = item.tanggal_mulai || '-';
    document.getElementById('detail_jam_mulai').innerText = (item.waktu_mulai || '-') + ' WIB';
    document.getElementById('detail_tgl_selesai').innerText = item.tanggal_selesai || '-';
    document.getElementById('detail_jam_selesai').innerText = (item.waktu_selesai || '-') + ' WIB';

    // 5. Set Tujuan
    document.getElementById('detail_tujuan').innerText = item.keterangan || '-';

    // Refresh icon & buka modal
    lucide.createIcons();
    toggleModal('detailModal');
}

    lucide.createIcons();

    function setView(type) {
        // ... [Kode setView tetap sama] ...
        const grid = document.getElementById('view-grid');
        const table = document.getElementById('view-table');
        const btnGrid = document.getElementById('btn-grid');
        const btnTable = document.getElementById('btn-table');

        if (type === 'grid') {
            grid.classList.remove('hidden');
            table.classList.add('hidden');
            btnGrid.classList.add('bg-gray-100', 'text-gray-700');
            btnGrid.classList.remove('text-gray-500');
            btnTable.classList.remove('bg-gray-100', 'text-gray-700');
            btnTable.classList.add('text-gray-500');
        } else {
            table.classList.remove('hidden');
            grid.classList.add('hidden');
            btnTable.classList.add('bg-gray-100', 'text-gray-700');
            btnTable.classList.remove('text-gray-500');
            btnGrid.classList.remove('bg-gray-100', 'text-gray-700');
            btnGrid.classList.add('text-gray-500');
        }
    }

    function toggleModal(modalID) {
        document.getElementById(modalID).classList.toggle('hidden');
        if(document.getElementById(modalID).classList.contains('hidden')){
            nextStep(1); 
            document.getElementById('detail_item_box').classList.add('hidden');
            // Reset form input saat modal ditutup
            document.getElementById('formPeminjaman').reset();
        }
    }

    function nextStep(step) {
        document.getElementById('step_1_form').classList.add('hidden');
        document.getElementById('step_2_form').classList.add('hidden');
        document.getElementById('step_3_form').classList.add('hidden');
        
        const targetForm = document.getElementById(`step_${step}_form`);
        if (targetForm) targetForm.classList.remove('hidden');

        for(let i=1; i<=3; i++) {
            let circle = document.getElementById(`step_${i}_circle`);
            if(circle) {
                if(i === step) {
                    circle.className = "w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-sm shadow-sm transition-colors";
                } else if(i < step) {
                    circle.className = "w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm transition-colors";
                } else {
                    circle.className = "w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center font-medium text-sm transition-colors";
                }
            }
        }
    }

    function pilihTipe(tipe) {
        // SET NILAI INPUT HIDDEN KE FORM
        document.getElementById('input_tipe_peminjaman').value = tipe;
        document.getElementById('input_nama_item').value = ""; // Reset nama item saat tipe ganti

        const textTipe = document.getElementById('text_tipe');
        const iconContainer = document.getElementById('tipe_icon_container');
        const divRuangan = document.getElementById('div_ruangan');
        const divBarang = document.getElementById('div_barang');
        const optionRuangan = document.getElementById('option_tipe_ruangan');
        const optionBarang = document.getElementById('option_tipe_barang');
        const checkRuangan = document.getElementById('check_tipe_ruangan');
        const checkBarang = document.getElementById('check_tipe_barang');

        document.getElementById('detail_item_box').classList.add('hidden');

        if(optionRuangan) optionRuangan.classList.remove('bg-gray-50');
        if(optionBarang) optionBarang.classList.remove('bg-gray-50');
        if(checkRuangan) checkRuangan.classList.add('hidden');
        if(checkBarang) checkBarang.classList.add('hidden');

        if (tipe === 'ruangan') {
            textTipe.innerText = 'Ruangan';
            iconContainer.innerHTML = '<i data-lucide="door-open" class="w-4 h-4 text-gray-500"></i>';
            divRuangan.classList.remove('hidden');
            divBarang.classList.add('hidden');
            if(optionRuangan) optionRuangan.classList.add('bg-gray-50');
            if(checkRuangan) checkRuangan.classList.remove('hidden');
            document.getElementById('text_ruangan').innerText = 'Pilih item';
            document.getElementById('text_ruangan').classList.add('text-gray-500');
            document.getElementById('text_ruangan').classList.remove('text-gray-900');
        } else {
            textTipe.innerText = 'Barang';
            iconContainer.innerHTML = '<i data-lucide="package" class="w-4 h-4 text-gray-500"></i>';
            divRuangan.classList.add('hidden');
            divBarang.classList.remove('hidden');
            if(optionBarang) optionBarang.classList.add('bg-gray-50');
            if(checkBarang) checkBarang.classList.remove('hidden');
            document.getElementById('text_barang').innerText = 'Pilih item';
            document.getElementById('text_barang').classList.add('text-gray-500');
            document.getElementById('text_barang').classList.remove('text-gray-900');
        }
        document.getElementById('menu_tipe').classList.add('hidden');
        lucide.createIcons();
    }

    function toggleCustomDropdown(menuId) {
        const allMenus = ['menu_tipe', 'menu_ruangan', 'menu_barang'];
        allMenus.forEach(id => {
            if (id !== menuId && document.getElementById(id)) {
                document.getElementById(id).classList.add('hidden');
            }
        });
        document.getElementById(menuId).classList.toggle('hidden');
    }

    function pilihItem(tipe, nilai, detail1, detail2) {
        // SET NILAI INPUT HIDDEN KE FORM
        document.getElementById('input_nama_item').value = nilai;

        const textElement = document.getElementById(`text_${tipe}`);
        textElement.innerText = nilai;
        textElement.classList.remove('text-gray-500');
        textElement.classList.add('text-gray-900');
        
        document.getElementById('detail_item_box').classList.remove('hidden');
        document.getElementById('detail_line_1').innerText = detail1;
        document.getElementById('detail_line_2').innerText = detail2;
        document.getElementById(`menu_${tipe}`).classList.add('hidden');
    }

    document.addEventListener('click', function(event) {
        ['tipe', 'ruangan', 'barang'].forEach(jenis => {
            const btn = document.getElementById(`btn_${jenis}`);
            const menu = document.getElementById(`menu_${jenis}`);
            if (btn && menu) {
                if (!btn.contains(event.target) && !menu.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            }
        });
    });
</script>
</body>
</html>