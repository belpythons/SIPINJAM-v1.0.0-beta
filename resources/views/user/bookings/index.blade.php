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
    <aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-gradient-to-b from-[#1e3a8a] to-[#3b82f6] text-white transition-all duration-300 flex flex-col hidden lg:flex">

        {{-- Logo --}}
        <div class="flex h-16 items-center gap-3 px-6 flex-shrink-0">
            <div class="bg-orange-500 p-2 rounded-lg">
                <i data-lucide="box" class="w-5 h-5 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">SIPINJAM</h1>
        </div>

        {{-- User Info --}}
        <div class="px-4 pb-4 flex-shrink-0">
            <div class="flex items-center gap-3 rounded-lg px-3 py-2.5">
                <div class="h-10 w-10 rounded-full bg-blue-500 border-2 border-white/30 flex items-center justify-center font-semibold text-white text-sm">
                    {{ strtoupper(substr($user->name ?? 'JD', 0, 2)) }}
                </div>
                <div class="flex-1 text-left">
                    <p class="text-sm font-semibold text-white">{{ $user->name ?? 'John Doe' }}</p>
                    <p class="text-xs text-white/70 truncate">{{ $user->email ?? 'user@sipinjam.ac.id' }}</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 space-y-1 px-3 py-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white hover:scale-105 transition-all duration-200">
                <i data-lucide="home" class="h-5 w-5 flex-shrink-0"></i>
                Dashboard
            </a>
            <a href="{{ route('bookings.index') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium bg-white/20 text-white shadow-sm scale-105 transition-all duration-200">
                <i data-lucide="calendar" class="h-5 w-5 flex-shrink-0"></i>
                Riwayat Peminjaman
            </a>
            <a href="#"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white hover:scale-105 transition-all duration-200">
                <i data-lucide="door-open" class="h-5 w-5 flex-shrink-0"></i>
                Ruangan
            </a>
            <a href="#"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white hover:scale-105 transition-all duration-200">
                <i data-lucide="package" class="h-5 w-5 flex-shrink-0"></i>
                Barang
            </a>
            <a href="#"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white hover:scale-105 transition-all duration-200">
                <i data-lucide="book-open" class="h-5 w-5 flex-shrink-0"></i>
                Tata Tertib
            </a>
        </nav>

        {{-- Collapse hint --}}
        <div class="px-4 py-2 flex-shrink-0">
            <button class="flex items-center gap-2 text-white/40 hover:text-white/80 text-xs py-1 transition-colors">
                <i data-lucide="chevron-left" class="h-4 w-4"></i>
            </button>
        </div>

        {{-- Logout --}}
        <div class="border-t border-white/10 p-4 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white transition-colors">
                    <i data-lucide="log-out" class="h-5 w-5"></i>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto lg:ml-64 bg-gray-50 transition-all duration-300">

        {{-- Header --}}
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b bg-white px-4 lg:px-6">
            <h2 class="text-base font-semibold text-gray-700">User Portal</h2>
            <button class="lg:hidden p-2 rounded-md hover:bg-gray-100">
                <i data-lucide="menu" class="h-6 w-6"></i>
            </button>
        </header>

        {{-- Page Content --}}
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 animate-fade-in">

            {{-- Page Title + CTA --}}
            <div class="flex items-start justify-between animate-slide-up">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Riwayat Peminjaman</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola dan lihat riwayat peminjaman Anda</p>
                </div>
                <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg inline-flex items-center gap-2 shadow-sm transition-colors">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Peminjaman Baru
                </button>
            </div>

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-slide-up animate-delay-100">
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-500">Total Peminjaman</p>
                        <i data-lucide="calendar" class="h-5 w-5 text-gray-400"></i>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalPeminjaman ?? 0 }}</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-500">Menunggu</p>
                        <i data-lucide="clock" class="h-5 w-5 text-yellow-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-yellow-500">{{ $menunggu ?? 0 }}</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-500">Disetujui</p>
                        <i data-lucide="trending-up" class="h-5 w-5 text-green-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-green-500">{{ $sedangDipinjam ?? 0 }}</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-500">Selesai</p>
                        <i data-lucide="calendar-check" class="h-5 w-5 text-blue-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-blue-500">{{ $selesai ?? 0 }}</p>
                </div>
            </div>

            {{-- Daftar Peminjaman --}}
            <div class="animate-slide-up animate-delay-200">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">

                    {{-- Section Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Peminjaman</h3>
                        <div class="flex items-center gap-1 border border-gray-200 rounded-lg p-1">
                            <button id="btn-grid" onclick="setView('grid')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium bg-gray-100 text-gray-700 transition-colors">
                                <i data-lucide="layout-grid" class="h-4 w-4"></i> Grid
                            </button>
                            <button id="btn-table" onclick="setView('table')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
                                <i data-lucide="list" class="h-4 w-4"></i> Tabel
                            </button>
                        </div>
                    </div>

                    {{-- GRID VIEW --}}
                    <div id="view-grid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                        @forelse($peminjamans ?? [] as $item)
                            @php
                                $isRuangan = $item->type === 'ruangan';
                                $bgColor   = $isRuangan ? 'bg-blue-600' : 'bg-green-500';
                                $badgeColor = match($item->status) {
                                    'menunggu' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                                    'disetujui' => 'bg-green-100 text-green-700 border border-green-200',
                                    'selesai'  => 'bg-gray-100 text-gray-600 border border-gray-200',
                                    'ditolak'  => 'bg-red-100 text-red-600 border border-red-200',
                                    default    => 'bg-gray-100 text-gray-600 border border-gray-200',
                                };
                                $statusLabel = match($item->status) {
                                    'menunggu' => 'Menunggu',
                                    'disetujui' => 'Disetujui',
                                    'selesai'  => 'Selesai',
                                    'ditolak'  => 'Ditolak',
                                    default    => ucfirst($item->status),
                                };
                            @endphp
                            <div class="rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                {{-- Card Header --}}
                                <div class="{{ $bgColor }} px-4 py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-white/90 text-sm font-medium">
                                        @if($isRuangan)
                                            <i data-lucide="map-pin" class="h-4 w-4"></i>
                                            Ruangan
                                        @else
                                            <i data-lucide="package" class="h-4 w-4"></i>
                                            Barang
                                        @endif
                                    </div>
                                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badgeColor }}">
                                        @if($item->status === 'menunggu')
                                            <i data-lucide="clock" class="h-3 w-3 inline-block mr-1"></i>
                                        @elseif($item->status === 'disetujui')
                                            <i data-lucide="check-circle" class="h-3 w-3 inline-block mr-1"></i>
                                        @elseif($item->status === 'selesai')
                                            <i data-lucide="check-circle-2" class="h-3 w-3 inline-block mr-1"></i>
                                        @elseif($item->status === 'ditolak')
                                            <i data-lucide="x-circle" class="h-3 w-3 inline-block mr-1"></i>
                                        @endif
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                {{-- Card Title --}}
                                <div class="{{ $bgColor }} px-4 pb-4">
                                    <h4 class="text-xl font-bold text-white">{{ $item->nama ?? '-' }}</h4>
                                </div>
                                {{-- Card Body --}}
                                <div class="p-4 bg-white space-y-2">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i data-lucide="calendar" class="h-4 w-4 text-gray-400 flex-shrink-0"></i>
                                        <div>
                                            <p class="text-xs text-gray-400">Tanggal</p>
                                            <p class="font-medium">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i data-lucide="clock" class="h-4 w-4 text-gray-400 flex-shrink-0"></i>
                                        <div>
                                            <p class="text-xs text-gray-400">Durasi</p>
                                            <p class="font-medium">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</p>
                                        </div>
                                    </div>
                                    @if($item->keperluan)
                                    <p class="text-sm text-gray-500 pt-1">{{ $item->keperluan }}</p>
                                    @endif
                                    {{-- Actions --}}
                                    <div class="flex items-center gap-2 pt-2">
                                        <a href="{{ route('bookings.show', $item->id) }}"
                                           class="flex-1 flex items-center justify-center gap-1.5 border border-gray-200 rounded-lg py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i data-lucide="eye" class="h-4 w-4"></i> Detail
                                        </a>
                                        <a href="{{ route('bookings.pdf', $item->id) }}"
                                           class="flex-1 flex items-center justify-center gap-1.5 border border-gray-200 rounded-lg py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i data-lucide="download" class="h-4 w-4"></i> PDF
                                        </a>
                                    </div>
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

                    {{-- TABLE VIEW (hidden by default) --}}
                    <div id="view-table" class="hidden overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama</th>
                                    <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                                    <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                                    <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Durasi</th>
                                    <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($peminjamans ?? [] as $item)
                                @php
                                    $badgeColor = match($item->status) {
                                        'menunggu'  => 'bg-yellow-100 text-yellow-700',
                                        'disetujui' => 'bg-green-100 text-green-700',
                                        'selesai'   => 'bg-gray-100 text-gray-600',
                                        'ditolak'   => 'bg-red-100 text-red-600',
                                        default     => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 pr-4 font-medium text-gray-900">{{ $item->nama ?? '-' }}</td>
                                    <td class="py-3 pr-4 text-gray-500">{{ ucfirst($item->type ?? '-') }}</td>
                                    <td class="py-3 pr-4 text-gray-600">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                    <td class="py-3 pr-4 text-gray-600">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                                    <td class="py-3 pr-4">
                                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badgeColor }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('bookings.show', $item->id) }}" class="text-blue-600 hover:underline text-xs font-medium">Detail</a>
                                            <a href="{{ route('bookings.pdf', $item->id) }}" class="text-gray-500 hover:underline text-xs font-medium">PDF</a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400">Belum ada peminjaman</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>

        {{-- Footer --}}
        <footer class="mt-auto px-6 py-4 border-t border-gray-200 text-xs text-gray-400 flex justify-between bg-white">
            <span>© 2026 SIPINJAM - Sistem Informasi Peminjaman Ruangan dan Barang</span>
            <span>Built with Laravel</span>
        </footer>
    </main>
</div>

<script>
    lucide.createIcons();

    function setView(type) {
        const grid     = document.getElementById('view-grid');
        const table    = document.getElementById('view-table');
        const btnGrid  = document.getElementById('btn-grid');
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
</script>
</body>
</html>