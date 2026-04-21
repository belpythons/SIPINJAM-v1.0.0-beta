@extends('layouts.user')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6 lg:space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Riwayat Peminjaman</h1>
            <p class="text-sm sm:text-base text-muted-foreground">Kelola dan lihat riwayat peminjaman Anda</p>
        </div>
        <button class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-medium text-white shadow hover:bg-blue-700 transition-all">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Peminjaman Baru
        </button>
    </div>

    <div class="grid gap-4 sm:gap-6 grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border-2 bg-card p-6 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex flex-row items-center justify-between pb-3">
                <span class="text-xs sm:text-sm font-medium">Total Peminjaman</span>
                <i class="lucide-calendar h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-xl sm:text-2xl font-bold">{{ $stats['total'] }}</div>
        </div>

        <div class="rounded-xl border-2 bg-card p-6 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex flex-row items-center justify-between pb-3">
                <span class="text-xs sm:text-sm font-medium">Menunggu</span>
                <i class="lucide-clock h-4 w-4 text-yellow-600"></i>
            </div>
            <div class="text-xl sm:text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
        </div>
        </div>

    <div class="rounded-xl border-2 bg-card shadow-sm">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Daftar Peminjaman</h3>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-muted-foreground">
                        <th class="py-3 px-4 text-left">Item</th>
                        <th class="py-3 px-4 text-left">Tanggal</th>
                        <th class="py-3 px-4 text-left">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="border-b hover:bg-muted/50 transition-colors">
                        <td class="py-4 px-4 font-medium">{{ $booking->item_name }}</td>
                        <td class="py-4 px-4">
                            {{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                {{ $booking->status == 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <button class="text-blue-600 hover:underline">Detail</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-muted-foreground">Belum ada riwayat peminjaman.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection