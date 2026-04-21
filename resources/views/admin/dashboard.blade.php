@extends('layouts.admin') {{-- Sesuaikan dengan nama layout utamamu --}}

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6 lg:space-y-8 bg-gray-50 min-h-screen">
    
    <div class="bg-gradient-to-br from-orange-500 to-orange-700 text-white rounded-xl shadow-lg overflow-hidden relative animate-fade-in-up">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full blur-3xl -ml-20 -mb-20"></div>
        <div class="relative p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <h1 class="text-2xl sm:text-3xl font-bold">
                            {{ $greeting }}, Admin {{ $user->name ?? 'User' }}!
                        </h1>
                    </div>
                    <p class="text-orange-100 text-base sm:text-lg">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    <p class="text-white/90 max-w-2xl mt-2 sm:mt-4 text-sm sm:text-base">
                        Kelola sistem peminjaman dengan efisien. {{ $stats['pendingBookings'] }} peminjaman menunggu persetujuan Anda.
                    </p>
                </div>
                <a href="{{ url('/admin/bookings') }}" class="w-full lg:w-auto bg-white text-orange-600 hover:bg-orange-50 font-medium px-6 py-3 rounded-md flex items-center justify-center transition-colors">
                    <svg class="mr-2 h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Kelola Peminjaman
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:gap-6 lg:grid-cols-4 animate-fade-in-up" style="animation-delay: 0.1s;">
        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-xl transition-all duration-300 cursor-pointer group">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Total User</p>
                    <p class="text-3xl font-bold">{{ $stats['totalUsers'] }}</p>
                </div>
                <div class="p-3 rounded-xl bg-blue-100 group-hover:scale-110 transition-transform">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-xl transition-all duration-300 cursor-pointer group">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Total Peminjaman</p>
                    <p class="text-3xl font-bold">{{ $stats['totalBookings'] }}</p>
                </div>
                <div class="p-3 rounded-xl bg-orange-100 group-hover:scale-110 transition-transform">
                    <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-xl transition-all duration-300 cursor-pointer group">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Menunggu</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pendingBookings'] }}</p>
                </div>
                <div class="p-3 rounded-xl bg-yellow-100 group-hover:scale-110 transition-transform">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-xl transition-all duration-300 cursor-pointer group">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Disetujui</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['approvedBookings'] }}</p>
                </div>
                <div class="p-3 rounded-xl bg-green-100 group-hover:scale-110 transition-transform">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 lg:col-span-3 lg:row-span-2 h-full">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold">Tren Peminjaman</h3>
                    <p class="text-sm text-gray-500">6 bulan terakhir</p>
                </div>
                <svg class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            </div>
            <div id="trendChart" class="w-full h-[300px]"></div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 lg:row-span-2 h-full">
            <h3 class="text-lg font-semibold mb-4">Status Peminjaman</h3>
            <div id="statusChart" class="w-full h-[250px] flex justify-center"></div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold mb-4">Aksi Cepat</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <a href="{{ url('/admin/users') }}" class="flex flex-col gap-2 items-center justify-center border border-gray-200 rounded-md p-6 h-24 hover:bg-orange-50 hover:border-orange-500 hover:scale-105 transition-all text-gray-700">
                    <svg class="h-8 w-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span class="font-medium">Kelola User</span>
                </a>
                <a href="{{ url('/admin/rooms') }}" class="flex flex-col gap-2 items-center justify-center border border-gray-200 rounded-md p-6 h-24 hover:bg-blue-50 hover:border-blue-500 hover:scale-105 transition-all text-gray-700">
                    <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                    <span class="font-medium">Kelola Ruangan</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold mb-4">Sumber Daya</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 rounded-lg bg-blue-50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-blue-100">
                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                        </div>
                        <div>
                            <p class="font-medium">Ruangan</p>
                            <p class="text-sm text-gray-500">{{ $stats['availableRooms'] }} / {{ $stats['totalRooms'] }} tersedia</p>
                        </div>
                    </div>
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </div>
                <div class="flex items-center justify-between p-4 rounded-lg bg-green-50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-green-100">
                            <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                        <div>
                            <p class="font-medium">Barang</p>
                            <p class="text-sm text-gray-500">{{ $stats['availableEquipment'] }} / {{ $stats['totalEquipment'] }} tersedia</p>
                        </div>
                    </div>
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Script untuk Rendering Chart --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Data dari Controller
        const trendData = @json($bookingTrendData);
        const stats = @json($stats);

        // 1. Setup Area Chart (Tren Peminjaman)
        const trendOptions = {
            series: [{
                name: 'Peminjaman',
                data: trendData.map(item => item.bookings)
            }],
            chart: {
                height: 300,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            colors: ['#f97316'], // Orange 500
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: trendData.map(item => item.month),
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4,
            }
        };
        new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

        // 2. Setup Donut Chart (Status Peminjaman)
        const statusOptions = {
            series: [
                stats.pendingBookings, 
                stats.approvedBookings, 
                stats.rejectedBookings, 
                stats.completedBookings
            ],
            labels: ['Pending', 'Disetujui', 'Ditolak', 'Selesai'],
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'inherit'
            },
            colors: ['#f97316', '#fb923c', '#fdba74', '#fed7aa'],
            plotOptions: {
                pie: {
                    donut: { size: '75%' }
                }
            },
            dataLabels: { enabled: false },
            legend: {
                position: 'bottom',
                markers: { radius: 12 }
            },
            stroke: { show: false }
        };
        new ApexCharts(document.querySelector("#statusChart"), statusOptions).render();
    });
</script>

{{-- Tambahan CSS kecil untuk animasi masuk yang ada di framer-motion --}}
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }
</style>
@endsection