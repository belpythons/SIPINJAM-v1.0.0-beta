<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruangan - SIPINJAM</title>

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

    @include('user.partials.sidebar', ['activePage' => 'ruangan'])

    {{-- MAIN --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto lg:ml-64 bg-gray-50">
        @include('user.partials.navbar')

        <div class="p-4 sm:p-6 lg:p-8 space-y-6 animate-fade-in">

            {{-- Page Title --}}
            <div class="animate-slide-up">
                <h1 class="text-2xl font-bold text-gray-900">Daftar Ruangan</h1>
                <p class="text-sm text-gray-500 mt-1">Lihat ruangan yang tersedia untuk dipinjam</p>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <p class="text-sm text-gray-500 mb-1">Total Ruangan</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $ruangans->count() }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <p class="text-sm text-gray-500 mb-1">Tersedia</p>
                    <p class="text-3xl font-bold text-green-600">{{ $ruangans->where('status', 'tersedia')->count() }}</p>
                </div>
            </div>

            {{-- Grid Ruangan --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($ruangans as $ruangan)
                    @php
                        $tersedia = $ruangan->status === 'tersedia';
                    @endphp
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-white">
                                <i data-lucide="door-open" class="h-5 w-5"></i>
                                <span class="font-semibold">{{ $ruangan->kode }}</span>
                            </div>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $tersedia ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $tersedia ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </div>
                        {{-- Body --}}
                        <div class="p-4 space-y-3">
                            <h4 class="font-semibold text-gray-900 text-base">{{ $ruangan->nama }}</h4>
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <i data-lucide="users" class="h-4 w-4 text-gray-400"></i>
                                    <span>Kapasitas: <span class="font-medium">{{ $ruangan->kapasitas }} orang</span></span>
                                </div>
                                @if($ruangan->lokasi)
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <i data-lucide="map-pin" class="h-4 w-4 text-gray-400"></i>
                                    <span>{{ $ruangan->lokasi }}</span>
                                </div>
                                @endif
                                @if($ruangan->deskripsi)
                                <p class="text-sm text-gray-500 pt-1">{{ $ruangan->deskripsi }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-3 text-center py-16 text-gray-400">
                        <i data-lucide="door-open" class="h-12 w-12 mx-auto mb-3 opacity-30"></i>
                        <p class="text-base font-medium">Belum ada data ruangan</p>
                        <p class="text-sm mt-1">Data ruangan akan ditampilkan di sini</p>
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