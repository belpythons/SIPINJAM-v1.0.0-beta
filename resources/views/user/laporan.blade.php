<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - SiPinjam</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-sp.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    @include('user.partials.sidebar', ['activePage' => 'laporan'])

    {{-- MAIN --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto lg:ml-64 bg-gray-50 transition-all duration-300">
        @include('user.partials.navbar')

        <div class="bg-white border-b border-gray-200 px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Laporan Peminjaman Saya</h2>
                    <p class="text-gray-500 text-sm mt-1">Riwayat lengkap aktivitas peminjaman Anda</p>
                </div>
                <a href="{{ route('laporan.export_pdf') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition-colors shadow-sm">
                    <i data-lucide="file-text" class="h-4 w-4"></i> Cetak Riwayat (PDF)
                </a>
            </div>
        </div>

        <div class="p-6 lg:p-8 space-y-6 flex-1">
            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-yellow-600 uppercase">Menunggu</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-green-600 uppercase">Aktif</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['approved'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Selesai</p>
                    <p class="text-3xl font-bold text-gray-600 mt-2">{{ $stats['done'] }}</p>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">No</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Item</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Periode</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($peminjamans as $index => $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-700">{{ $p->created_at?->format('d/m/Y') }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $p->tipe === 'ruangan' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ ucfirst($p->tipe ?? '-') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                        @if($p->tipe === 'ruangan')
                                            {{ $p->ruangan->nama ?? $p->nama_item ?? '-' }}
                                        @else
                                            {{ $p->barang->nama ?? $p->nama_item ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-600">
                                        {{ $p->tanggal_mulai?->format('d/m') }} — {{ $p->tanggal_selesai?->format('d/m') }}
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $sc = [
                                                'menunggu'        => 'bg-yellow-100 text-yellow-700',
                                                'sedang_dipinjam' => 'bg-green-100 text-green-700',
                                                'ditolak'         => 'bg-red-100 text-red-700',
                                                'selesai'         => 'bg-gray-100 text-gray-600',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc[$p->status] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst(str_replace('_', ' ', $p->status ?? '-')) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                        <p class="text-lg mb-1">Belum ada riwayat peminjaman</p>
                                        <p class="text-sm">Data peminjaman Anda akan tampil di sini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer class="mt-auto px-6 py-4 border-t border-gray-200 text-xs text-gray-500 flex justify-between bg-white">
            <span>&copy; 2026 SiPinjam</span>
        </footer>
    </main>
</div>

<script>lucide.createIcons();</script>
</body>
</html>
