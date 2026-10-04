<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - SiPinjam Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-sp.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { DEFAULT: '#ea580c', dark: '#c2410c', light: '#f97316' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar', ['activePage' => 'laporan'])

    {{-- MAIN CONTENT --}}
    <main class="flex-1 lg:ml-64 h-screen overflow-y-auto flex flex-col">

        @include('admin.partials.navbar')

        {{-- Header --}}
        <div class="bg-white border-b border-gray-200 px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Laporan Peminjaman</h2>
                    <p class="text-gray-500 text-sm mt-1">Ringkasan aktivitas peminjaman ruangan dan barang</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.laporan.export_pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition-colors shadow-sm">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                    <a href="{{ route('admin.laporan.export_excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm transition-colors shadow-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>

            {{-- Filter Tanggal --}}
            <form method="GET" action="{{ route('admin.laporan') }}" class="mt-5 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                           class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                           class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                </div>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition-colors">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </form>
        </div>

        <div class="p-8 space-y-6 flex-1">

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Peminjaman</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Menunggu</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Disetujui</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['approved'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Selesai</p>
                    <p class="text-3xl font-bold text-gray-600 mt-2">{{ $stats['done'] }}</p>
                </div>
            </div>

            {{-- Infrastruktur Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total User</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-building text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Ruangan</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['total_ruangan'] }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-box text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Barang</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['total_barang'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Top Utilisasi --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-building mr-2 text-purple-500"></i>Ruangan Terpopuler</h3>
                    @forelse($topRuangan as $item)
                        <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                            <span class="text-sm text-gray-700">{{ $item->ruangan->nama ?? '-' }}</span>
                            <span class="text-sm font-semibold text-gray-900 bg-gray-100 px-2.5 py-0.5 rounded-full">{{ $item->total }}x</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-box mr-2 text-orange-500"></i>Barang Terpopuler</h3>
                    @forelse($topBarang as $item)
                        <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                            <span class="text-sm text-gray-700">{{ $item->barang->nama ?? '-' }}</span>
                            <span class="text-sm font-semibold text-gray-900 bg-gray-100 px-2.5 py-0.5 rounded-full">{{ $item->total }}x</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>

            {{-- Data Table --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Riwayat Peminjaman</h3>
                    <p class="text-sm text-gray-500">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Peminjam</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($peminjamans as $index => $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-700">{{ $p->created_at?->format('d/m/Y') }}</td>
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $p->user->name ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $p->tipe === 'ruangan' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ ucfirst($p->tipe ?? '-') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-700">
                                        @if($p->tipe === 'ruangan')
                                            {{ $p->ruangan->nama ?? $p->nama_item ?? '-' }}
                                        @else
                                            {{ $p->barang->nama ?? $p->nama_item ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $statusColors = [
                                                'menunggu'       => 'bg-yellow-100 text-yellow-700',
                                                'sedang_dipinjam' => 'bg-green-100 text-green-700',
                                                'ditolak'        => 'bg-red-100 text-red-700',
                                                'selesai'        => 'bg-gray-100 text-gray-600',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$p->status] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst(str_replace('_', ' ', $p->status ?? '-')) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                        <i class="fas fa-inbox text-3xl mb-2"></i>
                                        <p>Tidak ada data peminjaman untuk periode ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <footer class="mt-auto px-8 py-4 border-t border-gray-200 text-xs text-gray-500 flex justify-between bg-white">
            <span>&copy; 2026 SiPinjam - Sistem Informasi Peminjaman</span>
            <span>Built with Laravel</span>
        </footer>
    </main>
</body>
</html>
