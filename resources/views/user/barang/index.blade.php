<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang - SIPINJAM</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: {
                        'fade-in': 'fadeIn 0.4s ease forwards',
                        'slide-up': 'slideUp 0.5s ease forwards',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { transform: 'translateY(20px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
                    }
                }
            }
        }
    </script>
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

        {{-- Navigation --}}
        <nav class="flex-1 space-y-1 px-3 py-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white hover:scale-105 transition-all duration-200">
                <i data-lucide="home" class="h-5 w-5 flex-shrink-0"></i>
                Dashboard
            </a>
            <a href="{{ route('bookings.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white hover:scale-105 transition-all duration-200">
                <i data-lucide="calendar" class="h-5 w-5 flex-shrink-0"></i>
                Riwayat Peminjaman
            </a>
            <a href="{{ route('ruangan.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white hover:scale-105 transition-all duration-200">
                <i data-lucide="door-open" class="h-5 w-5 flex-shrink-0"></i>
                Ruangan
            </a>
            <a href="{{ route('barang.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium bg-white/20 text-white shadow-sm scale-105 transition-all duration-200">
                <i data-lucide="package" class="h-5 w-5 flex-shrink-0"></i>
                Barang
            </a>
            <a href="{{ route('tata_tertib.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white hover:scale-105 transition-all duration-200">
                <i data-lucide="book-open" class="h-5 w-5 flex-shrink-0"></i>
                Tata Tertib
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

    {{-- MAIN --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto lg:ml-64 bg-gray-50">

        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b bg-white px-4 lg:px-6">
            <h2 class="text-base font-semibold text-gray-700">User Portal</h2>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-6 animate-fade-in">

            {{-- Page Title --}}
            <div class="animate-slide-up">
                <h1 class="text-2xl font-bold text-gray-900">Daftar Barang</h1>
                <p class="text-sm text-gray-500 mt-1">Lihat barang yang tersedia untuk dipinjam</p>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <p class="text-sm text-gray-500 mb-1">Total Barang</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $barangs->count() }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <p class="text-sm text-gray-500 mb-1">Tersedia</p>
                    <p class="text-3xl font-bold text-green-600">{{ $barangs->where('status', 'tersedia')->count() }}</p>
                </div>
            </div>

            {{-- Grid Barang --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($barangs as $barang)
                    @php
                        $tersedia = $barang->status === 'tersedia';
                    @endphp
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-green-600 to-green-500 px-4 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-white">
                                <i data-lucide="package" class="h-5 w-5"></i>
                                <span class="font-semibold">{{ $barang->kode }}</span>
                            </div>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $tersedia ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $tersedia ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </div>
                        {{-- Body --}}
                        <div class="p-4 space-y-3">
                            <h4 class="font-semibold text-gray-900 text-base">{{ $barang->nama }}</h4>
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <i data-lucide="layers" class="h-4 w-4 text-gray-400"></i>
                                    <span>Stok: <span class="font-medium text-green-600">{{ $barang->stok_tersedia }}</span> / {{ $barang->stok_total }}</span>
                                </div>
                                @if($barang->kategori)
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <i data-lucide="tag" class="h-4 w-4 text-gray-400"></i>
                                    <span>{{ $barang->kategori }}</span>
                                </div>
                                @endif
                                @if($barang->deskripsi)
                                <p class="text-sm text-gray-500 pt-1">{{ $barang->deskripsi }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-3 text-center py-16 text-gray-400">
                        <i data-lucide="package" class="h-12 w-12 mx-auto mb-3 opacity-30"></i>
                        <p class="text-base font-medium">Belum ada data barang</p>
                        <p class="text-sm mt-1">Data barang akan ditampilkan di sini</p>
                    </div>
                @endforelse
            </div>

        </div>

        <footer class="mt-auto px-6 py-4 border-t border-gray-200 text-xs text-gray-400 flex justify-between bg-white">
            <span>© 2026 SIPINJAM - Sistem Informasi Peminjaman Ruangan dan Barang</span>
            <span>Built with Laravel</span>
        </footer>
    </main>
</div>

<script>lucide.createIcons();</script>
</body>
</html>