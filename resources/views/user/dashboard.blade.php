<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIPINJAM</title>
    
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
                    {{ strtoupper(substr($user->name ?? 'Us', 0, 2)) }}
                </div>
                <div class="flex-1 text-left">
                    <p class="text-sm font-semibold text-white">{{ $user->name ?? 'User Name' }}</p>
                    <p class="text-xs text-white/70 truncate">{{ $user->email ?? 'user@sipinjam.ac.id' }}</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 space-y-1 px-3 py-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium bg-white/20 text-white shadow-sm scale-105 transition-all duration-200">
                <i data-lucide="home" class="h-5 w-5 flex-shrink-0"></i>
                Dashboard
            </a>
            <a href="{{ route('bookings.index') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white hover:scale-105 transition-all duration-200">
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

    {{-- MAIN --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto lg:ml-64 bg-gray-50 transition-all duration-300">

        {{-- Header --}}
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b bg-white/95 backdrop-blur px-4 lg:px-6">
            <h2 class="text-xl font-semibold">User Portal</h2>
            <button class="lg:hidden p-2 rounded-md hover:bg-gray-100">
                <i data-lucide="menu" class="h-6 w-6"></i>
            </button>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-6 lg:space-y-8 animate-fade-in">

            {{-- Welcome Banner --}}
            <div class="animate-slide-up">
                <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl overflow-hidden relative shadow-sm">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full blur-3xl -ml-20 -mb-20"></div>
                    <div class="relative p-6 sm:p-8 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <i data-lucide="sparkles" class="h-5 w-5 sm:h-6 sm:w-6"></i>
                                <h1 class="text-2xl sm:text-3xl font-bold">
                                    Selamat {{ date('H') < 12 ? 'Pagi' : (date('H') < 15 ? 'Siang' : (date('H') < 18 ? 'Sore' : 'Malam')) }}, {{ $user->name ?? 'User' }}!
                                </h1>
                            </div>
                            <p class="text-blue-100 text-base sm:text-lg">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                            <p class="text-white/90 max-w-2xl mt-2 sm:mt-4 text-sm sm:text-base">
                                Selamat datang di SIPINJAM. Kelola peminjaman ruangan dan barang dengan mudah dan efisien.
                            </p>
                        </div>
                        <a href="{{ route('bookings.index') }}"
                           class="w-full lg:w-auto bg-white text-blue-600 hover:bg-blue-50 shadow-lg px-6 py-3 rounded-lg font-medium inline-flex items-center justify-center transition-colors">
                            <i data-lucide="calendar" class="mr-2 h-4 w-4 sm:h-5 sm:w-5"></i>
                            Ajukan Peminjaman
                        </a>
                    </div>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-4">

                <div class="animate-slide-up animate-delay-100 bg-white rounded-xl border border-gray-200 p-4 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer group">
                    <div class="flex items-start justify-between">
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Total Peminjaman</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalPeminjaman ?? 0 }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-100 group-hover:scale-110 transition-transform">
                            <i data-lucide="calendar" class="h-6 w-6 text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="animate-slide-up animate-delay-100 bg-white rounded-xl border border-gray-200 p-4 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer group">
                    <div class="flex items-start justify-between">
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Sedang Dipinjam</p>
                            <p class="text-3xl font-bold text-green-600">{{ $sedangDipinjam ?? 0 }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-green-100 group-hover:scale-110 transition-transform">
                            <i data-lucide="clock" class="h-6 w-6 text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="animate-slide-up animate-delay-100 bg-white rounded-xl border border-gray-200 p-4 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer group">
                    <div class="flex items-start justify-between">
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Menunggu</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $menunggu ?? 0 }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-yellow-100 group-hover:scale-110 transition-transform">
                            <i data-lucide="calendar-check" class="h-6 w-6 text-yellow-600"></i>
                        </div>
                    </div>
                </div>

                <div class="animate-slide-up animate-delay-100 bg-white rounded-xl border border-gray-200 p-4 sm:p-6 shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer group">
                    <div class="flex items-start justify-between">
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Selesai</p>
                            <p class="text-3xl font-bold text-gray-600">{{ $selesai ?? 0 }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-100 group-hover:scale-110 transition-transform">
                            <i data-lucide="check-circle-2" class="h-6 w-6 text-gray-600"></i>
                        </div>
                    </div>
                </div>

                {{-- Tren Peminjaman --}}
                <div class="animate-slide-up animate-delay-200 lg:col-span-2 lg:row-span-2">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6 h-full shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Tren Peminjaman</h3>
                                <p class="text-sm text-gray-500">6 bulan terakhir</p>
                            </div>
                            <i data-lucide="trending-up" class="h-5 w-5 text-blue-600"></i>
                        </div>
                        <div class="h-[250px] flex items-end justify-between space-x-2 pt-4 relative w-full">
                            <div class="w-full bg-blue-100 hover:bg-blue-200 rounded-t-md transition-colors" style="height: 30%"></div>
                            <div class="w-full bg-blue-200 hover:bg-blue-300 rounded-t-md transition-colors" style="height: 45%"></div>
                            <div class="w-full bg-blue-300 hover:bg-blue-400 rounded-t-md transition-colors" style="height: 35%"></div>
                            <div class="w-full bg-blue-400 hover:bg-blue-500 rounded-t-md transition-colors" style="height: 60%"></div>
                            <div class="w-full bg-blue-500 hover:bg-blue-600 rounded-t-md transition-colors" style="height: 50%"></div>
                            <div class="w-full bg-blue-600 hover:bg-blue-700 rounded-t-md transition-colors" style="height: 80%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mt-2">
                            <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span>
                        </div>
                    </div>
                </div>

                {{-- Aksi Cepat --}}
                <div class="animate-slide-up animate-delay-200 lg:col-span-2">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <a href="#" class="h-24 flex flex-col items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white hover:bg-blue-50 hover:border-blue-500 hover:scale-105 transition-all shadow-sm">
                                <i data-lucide="door-open" class="h-8 w-8 text-blue-600"></i>
                                <span class="text-sm font-medium">Lihat Ruangan</span>
                            </a>
                            <a href="#" class="h-24 flex flex-col items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white hover:bg-green-50 hover:border-green-500 hover:scale-105 transition-all shadow-sm">
                                <i data-lucide="package" class="h-8 w-8 text-green-600"></i>
                                <span class="text-sm font-medium">Lihat Barang</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Aktivitas Terbaru --}}
                <div class="animate-slide-up animate-delay-300 lg:col-span-2">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6 shadow-sm h-full">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                            <a href="{{ route('bookings.index') }}"
                               class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors">
                                Lihat Semua <i data-lucide="arrow-right" class="ml-1 h-4 w-4"></i>
                            </a>
                        </div>
                        <div class="space-y-3">

                            @if(($menunggu ?? 0) > 0)
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-yellow-50 border border-yellow-200">
                                <div class="p-2 rounded-lg bg-yellow-100">
                                    <i data-lucide="clock" class="h-4 w-4 text-yellow-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Menunggu Persetujuan</p>
                                    <p class="text-xs text-gray-500">{{ $menunggu }} peminjaman</p>
                                </div>
                            </div>
                            @endif

                            @if(($sedangDipinjam ?? 0) > 0)
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-green-50 border border-green-200">
                                <div class="p-2 rounded-lg bg-green-100">
                                    <i data-lucide="check-circle-2" class="h-4 w-4 text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Sedang Dipinjam</p>
                                    <p class="text-xs text-gray-500">{{ $sedangDipinjam }} item aktif</p>
                                </div>
                            </div>
                            @endif

                            @if(($totalPeminjaman ?? 0) == 0)
                            <div class="text-center py-8 text-gray-500">
                                <i data-lucide="calendar" class="h-12 w-12 mx-auto mb-2 opacity-50"></i>
                                <p class="text-sm">Belum ada peminjaman</p>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <footer class="mt-auto px-6 py-4 border-t border-gray-200 text-xs text-gray-500 flex justify-between bg-white">
            <span>© 2026 SIPINJAM - Sistem Informasi Peminjaman</span>
        </footer>
    </main>
</div>

<script>lucide.createIcons();</script>
</body>
</html>