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

        /* Custom Scrollbar untuk Time Picker */
        .time-scroll::-webkit-scrollbar { width: 4px; }
        .time-scroll::-webkit-scrollbar-track { background: transparent; }
        .time-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }
        .time-scroll::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

<div class="flex h-screen overflow-hidden">
        @include('user.partials.sidebar', ['activePage' => 'dashboard'])

    {{-- MAIN --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto lg:ml-64 bg-gray-50 transition-all duration-300">
        @include('user.partials.navbar')

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
                        <button onclick="openModal()" class="w-full lg:w-auto bg-white text-blue-600 hover:bg-blue-50 shadow-lg px-6 py-3 rounded-lg font-medium inline-flex items-center justify-center transition-colors">
                            <i data-lucide="calendar" class="mr-2 h-4 w-4 sm:h-5 sm:w-5"></i>
                            Ajukan Peminjaman
                        </button>
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
                            <a href="{{ route('ruangan.index') }}" class="h-24 flex flex-col items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white hover:bg-blue-50 hover:border-blue-500 hover:scale-105 transition-all shadow-sm">
                                <i data-lucide="door-open" class="h-8 w-8 text-blue-600"></i>
                                <span class="text-sm font-medium">Lihat Ruangan</span>
                            </a>
                            <a href="{{ route('barang.index') }}" class="h-24 flex flex-col items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white hover:bg-green-50 hover:border-green-500 hover:scale-105 transition-all shadow-sm">
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
                            <a href="{{ route('bookings.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors">
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

        {{-- MODAL AJUKAN PEMINJAMAN --}}
        <div id="bookingModal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal()"></div>
            
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div id="modalPanel" class="relative transform overflow-visible rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-[500px] scale-95 opacity-0 duration-300">
                        
                        <div class="absolute right-4 top-4 z-50">
                            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-md p-1 transition-colors">
                                <i data-lucide="x" class="h-5 w-5"></i>
                            </button>
                        </div>

                        <div class="px-6 pt-6 pb-8">
                            <h3 class="text-[22px] font-semibold text-gray-900">Ajukan Peminjaman Baru</h3>
                            <p class="mt-1 text-sm text-gray-500">Lengkapi form berikut untuk mengajukan peminjaman</p>

                            <div class="mt-6 flex justify-center gap-3" id="stepperContainer">
                                <div id="step-circle-1" class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white font-medium text-sm shadow-sm transition-colors">1</div>
                                <div id="step-circle-2" class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 font-medium text-sm transition-colors">2</div>
                                <div id="step-circle-3" class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 font-medium text-sm transition-colors">3</div>
                            </div>

                            <div id="formContainer" class="mt-8 relative overflow-hidden min-h-[260px]">
                                
                                <div id="step1Content" class="space-y-6 transition-all duration-300 absolute w-full">
                                    <div class="relative" id="dropdownTipeContainer">
                                        <label class="block text-sm font-medium text-gray-900 mb-2">Tipe Peminjaman</label>
                                        <button type="button" onclick="toggleDropdown('dropdownTipe')" id="btnTipe" class="w-[180px] bg-white border border-gray-200 text-gray-700 rounded-lg px-3 py-2.5 text-sm flex items-center justify-between hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="door-open" class="h-4 w-4 text-gray-500" id="iconTipe"></i>
                                                <span id="textTipe">Ruangan</span>
                                            </div>
                                            <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
                                        </button>
                                        
                                        <div id="dropdownTipe" class="hidden absolute left-0 top-full mt-1 w-[180px] bg-white border border-gray-100 rounded-lg shadow-lg py-1 z-50">
                                            <div onclick="selectTipe('ruangan', 'Ruangan', 'door-open')" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <i data-lucide="door-open" class="h-4 w-4 text-gray-500"></i>
                                                    <span>Ruangan</span>
                                                </div>
                                                <i data-lucide="check" class="h-4 w-4 text-gray-900 check-tipe" id="check-ruangan"></i>
                                            </div>
                                            <div onclick="selectTipe('barang', 'Barang', 'package')" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <i data-lucide="package" class="h-4 w-4 text-gray-500"></i>
                                                    <span>Barang</span>
                                                </div>
                                                <i data-lucide="check" class="h-4 w-4 text-gray-900 check-tipe hidden" id="check-barang"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="relative" id="dropdownItemContainer">
                                        <label class="block text-sm font-medium text-gray-900 mb-2" id="labelItem">Pilih Ruangan</label>
                                        <button type="button" onclick="toggleDropdown('dropdownItem')" id="btnItem" class="w-full sm:w-[250px] bg-white border border-gray-200 text-gray-500 rounded-lg px-3 py-2.5 text-sm flex items-center justify-between hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                            <span id="textItem" class="truncate">Pilih item</span>
                                            <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400 flex-shrink-0"></i>
                                        </button>
                                        
                                        <div id="dropdownItem" class="hidden absolute left-0 top-full mt-1 w-full max-w-[300px] bg-white border border-gray-100 rounded-lg shadow-lg py-1 z-50 max-h-60 overflow-y-auto">
                                        </div>
                                    </div>
                                </div>

                                <div id="step2Content" class="space-y-4 transition-all duration-300 absolute w-full translate-x-full opacity-0 pointer-events-none">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Tanggal Mulai</label>
                                            <div class="relative">
                                                <input type="date" class="w-full bg-white border border-gray-200 text-gray-700 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                            </div>
                                        </div>

                                        <div class="relative" id="dropdownWaktuMulaiContainer">
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Waktu Mulai</label>
                                            <button type="button" onclick="toggleDropdown('dropdownWaktuMulai')" class="w-full bg-white border border-gray-200 text-gray-500 rounded-lg px-3 py-2.5 text-sm flex items-center justify-between hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                                <span id="textWaktuMulai">-- : --</span>
                                                <i data-lucide="clock" class="h-4 w-4 text-gray-400"></i>
                                            </button>
                                            
                                            <div id="dropdownWaktuMulai" class="hidden absolute left-0 sm:right-0 top-full mt-1 w-[200px] bg-white border border-gray-100 rounded-lg shadow-lg z-50 p-2">
                                                <div class="flex justify-between border-b pb-2 mb-2 px-4 text-sm font-semibold text-gray-900">
                                                    <div class="w-1/2 text-center">HH</div>
                                                    <div class="w-1/2 text-center">MM</div>
                                                </div>
                                                <div class="flex h-48">
                                                    <div class="w-1/2 time-scroll overflow-y-auto px-1 border-r border-gray-100" id="hhMulai"></div>
                                                    <div class="w-1/2 time-scroll overflow-y-auto px-1" id="mmMulai"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2 mt-2 sm:mt-0">Tanggal Selesai</label>
                                            <div class="relative">
                                                <input type="date" class="w-full bg-white border border-gray-200 text-gray-700 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                            </div>
                                        </div>

                                        <div class="relative" id="dropdownWaktuSelesaiContainer">
                                            <label class="block text-sm font-medium text-gray-900 mb-2 mt-2 sm:mt-0">Waktu Selesai</label>
                                            <button type="button" onclick="toggleDropdown('dropdownWaktuSelesai')" class="w-full bg-white border border-gray-200 text-gray-500 rounded-lg px-3 py-2.5 text-sm flex items-center justify-between hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                                <span id="textWaktuSelesai">-- : --</span>
                                                <i data-lucide="clock" class="h-4 w-4 text-gray-400"></i>
                                            </button>

                                            <div id="dropdownWaktuSelesai" class="hidden absolute left-0 sm:right-0 top-full mt-1 w-[200px] bg-white border border-gray-100 rounded-lg shadow-lg z-50 p-2">
                                                <div class="flex justify-between border-b pb-2 mb-2 px-4 text-sm font-semibold text-gray-900">
                                                    <div class="w-1/2 text-center">HH</div>
                                                    <div class="w-1/2 text-center">MM</div>
                                                </div>
                                                <div class="flex h-48">
                                                    <div class="w-1/2 time-scroll overflow-y-auto px-1 border-r border-gray-100" id="hhSelesai"></div>
                                                    <div class="w-1/2 time-scroll overflow-y-auto px-1" id="mmSelesai"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="step3Content" class="space-y-4 transition-all duration-300 absolute w-full translate-x-full opacity-0 pointer-events-none">
                                    <div>
                                        <label id="labelTujuan" class="block text-sm font-medium text-gray-900 mb-2">Tujuan Peminjaman</label>
                                        <textarea id="inputTujuan" rows="3" class="w-full bg-white border border-gray-200 text-gray-700 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none" placeholder="Jelaskan tujuan peminjaman..." oninput="validateTujuan()"></textarea>
                                        <p id="errorTujuan" class="text-red-500 text-xs mt-1 hidden">Tujuan minimal 10 karakter</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-2">Catatan (Opsional)</label>
                                        <textarea id="inputCatatan" rows="2" class="w-full bg-white border border-gray-200 text-gray-700 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                    </div>
                                </div>

                            </div>

                            <div class="mt-8 flex justify-between items-center">
                                <button type="button" id="btnPrev" onclick="changeStep(1)" class="hidden bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
                                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                                    Sebelumnya
                                </button>
                                
                                <div id="spacerBtn"></div>

                                <button type="button" id="btnNext" onclick="changeStep(2)" class="bg-blue-600 text-white hover:bg-blue-700 px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
                                    <span id="textNextBtn">Selanjutnya</span>
                                    <i id="iconNextBtn" data-lucide="chevron-right" class="h-4 w-4 text-white"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>lucide.createIcons();</script>
<script>
    // --- Data Dummy ---
    const dataOptions = {
        ruangan: ["Ruang Kuliah 301 (50 orang)", "Ruang Seminar (100 orang)", "Lab Komputer 1 (40 orang)", "Ruang Meeting (20 orang)", "Aula Besar (300 orang)"],
        barang: ["Proyektor (10/10 tersedia)", "Laptop (5/5 tersedia)", "Microphone Wireless (8/8 tersedia)", "Kamera DSLR (3/3 tersedia)", "Sound System Portable (4/4 tersedia)"]
    };

    let currentTipe = 'ruangan';
    let currentStep = 1;
    
    // State Penyimpanan Waktu
    let timeSelection = {
        Mulai: { hh: '--', mm: '--' },
        Selesai: { hh: '--', mm: '--' }
    };

    const modal = document.getElementById('bookingModal');
    const modalPanel = document.getElementById('modalPanel');

    // --- Modal Config ---
    function openModal() {
        modal.classList.remove('hidden');
        void modal.offsetWidth; // Trigger reflow
        modal.classList.remove('opacity-0');
        modalPanel.classList.remove('scale-95', 'opacity-0');
        modalPanel.classList.add('scale-100', 'opacity-100');
        
        changeStep(1); // Reset ke step 1
        
        // Reset Inputs
        document.getElementById('inputTujuan').value = '';
        document.getElementById('inputTujuan').classList.remove('border-red-500', 'focus:ring-red-500/20', 'focus:border-red-500');
        document.getElementById('labelTujuan').classList.remove('text-red-500');
        document.getElementById('errorTujuan').classList.add('hidden');
        document.getElementById('inputCatatan').value = '';

        renderItemOptions();
        renderTimePicker('Mulai');
        renderTimePicker('Selesai');
    }

    function closeModal() {
        modal.classList.add('opacity-0');
        modalPanel.classList.remove('scale-100', 'opacity-100');
        modalPanel.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            hideAllDropdowns();
        }, 300);
    }

    // --- Step Navigation Logic ---
    function changeStep(targetStep) {
        currentStep = targetStep;
        
        const s1 = document.getElementById('step1Content');
        const s2 = document.getElementById('step2Content');
        const s3 = document.getElementById('step3Content');
        
        const btnPrev = document.getElementById('btnPrev');
        const spacerBtn = document.getElementById('spacerBtn');
        const btnNext = document.getElementById('btnNext');
        const textNextBtn = document.getElementById('textNextBtn');
        const iconNextBtn = document.getElementById('iconNextBtn');
        
        const c1 = document.getElementById('step-circle-1');
        const c2 = document.getElementById('step-circle-2');
        const c3 = document.getElementById('step-circle-3');
        
        if (targetStep === 1) {
            // Animasi Step 1
            s1.className = "space-y-6 transition-all duration-300 absolute w-full translate-x-0 opacity-100 pointer-events-auto";
            s2.className = "space-y-4 transition-all duration-300 absolute w-full translate-x-full opacity-0 pointer-events-none";
            s3.className = "space-y-4 transition-all duration-300 absolute w-full translate-x-full opacity-0 pointer-events-none";
            
            c1.className = "flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white font-medium text-sm shadow-sm transition-colors";
            c2.className = "flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 font-medium text-sm transition-colors";
            c3.className = "flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 font-medium text-sm transition-colors";
            
            btnPrev.classList.add('hidden');
            spacerBtn.classList.remove('hidden');
            textNextBtn.innerText = 'Selanjutnya';
            iconNextBtn.classList.remove('hidden');
            btnNext.setAttribute('onclick', 'changeStep(2)');
            
        } else if (targetStep === 2) {
            // Animasi Step 2
            s1.className = "space-y-6 transition-all duration-300 absolute w-full -translate-x-full opacity-0 pointer-events-none";
            s2.className = "space-y-4 transition-all duration-300 absolute w-full translate-x-0 opacity-100 pointer-events-auto";
            s3.className = "space-y-4 transition-all duration-300 absolute w-full translate-x-full opacity-0 pointer-events-none";
            
            c1.className = "flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-medium text-sm transition-colors";
            c2.className = "flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white font-medium text-sm shadow-sm transition-colors";
            c3.className = "flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 font-medium text-sm transition-colors";
            
            btnPrev.classList.remove('hidden');
            spacerBtn.classList.add('hidden');
            btnPrev.setAttribute('onclick', 'changeStep(1)');
            textNextBtn.innerText = 'Selanjutnya';
            iconNextBtn.classList.remove('hidden');
            btnNext.setAttribute('onclick', 'changeStep(3)');
            
        } else if (targetStep === 3) {
            // Animasi Step 3
            s1.className = "space-y-6 transition-all duration-300 absolute w-full -translate-x-full opacity-0 pointer-events-none";
            s2.className = "space-y-4 transition-all duration-300 absolute w-full -translate-x-full opacity-0 pointer-events-none";
            s3.className = "space-y-4 transition-all duration-300 absolute w-full translate-x-0 opacity-100 pointer-events-auto";
            
            c1.className = "flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-medium text-sm transition-colors";
            c2.className = "flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-medium text-sm transition-colors";
            c3.className = "flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white font-medium text-sm shadow-sm transition-colors";
            
            btnPrev.classList.remove('hidden');
            spacerBtn.classList.add('hidden');
            btnPrev.setAttribute('onclick', 'changeStep(2)');
            textNextBtn.innerText = 'Ajukan Peminjaman';
            iconNextBtn.classList.add('hidden'); // Sembunyikan panah > di step akhir
            btnNext.setAttribute('onclick', 'submitBooking()');
        }
    }

    // --- Validation Step 3 ---
    function validateTujuan() {
        const input = document.getElementById('inputTujuan');
        const error = document.getElementById('errorTujuan');
        const label = document.getElementById('labelTujuan');
        
        // Cek minimal 10 karakter jika ada isinya
        if (input.value.length > 0 && input.value.length < 10) {
            input.classList.remove('border-gray-200', 'focus:ring-blue-500/20', 'focus:border-blue-500');
            input.classList.add('border-red-500', 'focus:ring-red-500/20', 'focus:border-red-500');
            label.classList.add('text-red-500');
            error.classList.remove('hidden');
            return false;
        } else {
            // Normal state
            input.classList.add('border-gray-200', 'focus:ring-blue-500/20', 'focus:border-blue-500');
            input.classList.remove('border-red-500', 'focus:ring-red-500/20', 'focus:border-red-500');
            label.classList.remove('text-red-500');
            error.classList.add('hidden');
            return input.value.length >= 10;
        }
    }

    // --- Submit Logic (Simulasi Redirect ke Riwayat Peminjaman) ---
        function submitBooking() {
    const inputTujuan = document.getElementById('inputTujuan');

    // Validasi input tujuan
    if (!validateTujuan() || inputTujuan.value.length === 0) {
        inputTujuan.classList.add('border-red-500');
        document.getElementById('labelTujuan').classList.add('text-red-500');
        document.getElementById('errorTujuan').classList.remove('hidden');
        return;
    }

    const btnNext = document.getElementById('btnNext');
    const textNextBtn = document.getElementById('textNextBtn');
    
    // Tampilan tombol saat memproses
    btnNext.disabled = true;
    textNextBtn.innerText = 'Memproses...';

    // Mengambil elemen tanggal
    const tanggalMulaiEl   = document.querySelectorAll('#step2Content input[type="date"]')[0];
    const tanggalSelesaiEl = document.querySelectorAll('#step2Content input[type="date"]')[1];

    // MEMBUAT PAKET DATA (Nama field disamakan dengan Controller)
    const formData = new FormData();
    formData.append('tipe_peminjaman', currentTipe); 
    formData.append('nama_item',       document.getElementById('textItem').innerText);
    formData.append('tanggal_mulai',   tanggalMulaiEl.value);
    formData.append('tanggal_selesai', tanggalSelesaiEl.value);
    formData.append('waktu_mulai',     `${timeSelection.Mulai.hh}:${timeSelection.Mulai.mm}`);
    formData.append('waktu_selesai',   `${timeSelection.Selesai.hh}:${timeSelection.Selesai.mm}`);
    formData.append('keterangan',      inputTujuan.value); 
    formData.append('catatan',         document.getElementById('inputCatatan').value);

    // MENGIRIM DATA KE SERVER
    fetch('{{ route("bookings.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest', // Memberitahu ini adalah AJAX
            'Accept': 'application/json'
        },
        body: formData,
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Jika berhasil, pindah ke halaman riwayat
            window.location.href = '{{ route("bookings.index") }}';
        } else {
            // Jika validasi gagal (misal: keterangan kurang dari 10 huruf)
            alert('Gagal: ' + (data.message || 'Periksa kembali data Anda'));
            resetButton();
        }
    })
    .catch((err) => {
        console.error(err);
        alert('Terjadi kesalahan koneksi ke server.');
        resetButton();
    });

    function resetButton() {
        btnNext.disabled = false;
        textNextBtn.innerText = 'Ajukan Peminjaman';
    }
}

    // --- Time Picker Logic ---
    function renderTimePicker(type) {
        let hhHTML = '';
        for(let i=0; i<24; i++) {
            let val = i.toString().padStart(2, '0');
            hhHTML += `<div onclick="selectTime(event, '${type}', 'hh', '${val}')" class="time-opt hh-${type} py-1.5 px-1 mb-1 text-center cursor-pointer hover:bg-gray-100 rounded text-sm text-gray-600 transition-colors">${val}</div>`;
        }
        document.getElementById(`hh${type}`).innerHTML = hhHTML;
        
        let mmHTML = '';
        for(let i=0; i<60; i++) {
            let val = i.toString().padStart(2, '0');
            mmHTML += `<div onclick="selectTime(event, '${type}', 'mm', '${val}')" class="time-opt mm-${type} py-1.5 px-1 mb-1 text-center cursor-pointer hover:bg-gray-100 rounded text-sm text-gray-600 transition-colors">${val}</div>`;
        }
        document.getElementById(`mm${type}`).innerHTML = mmHTML;
    }

    function selectTime(event, type, unit, val) {
        event.stopPropagation();
        
        timeSelection[type][unit] = val;
        
        document.querySelectorAll(`.${unit}-${type}`).forEach(el => {
            el.classList.remove('bg-gray-600', 'text-white');
            el.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        
        const target = event.currentTarget;
        target.classList.remove('text-gray-600', 'hover:bg-gray-100');
        target.classList.add('bg-gray-600', 'text-white');

        let currentHH = timeSelection[type].hh;
        let currentMM = timeSelection[type].mm;
        
        const btnText = document.getElementById(`textWaktu${type}`);
        btnText.innerText = `${currentHH} : ${currentMM}`;
        btnText.classList.replace('text-gray-500', 'text-gray-900');
    }

    // --- General Dropdown Logic & Overflow Fix ---
    function hideAllDropdowns() {
        ['dropdownTipe', 'dropdownItem', 'dropdownWaktuMulai', 'dropdownWaktuSelesai'].forEach(id => {
            const el = document.getElementById(id);
            if(el) el.classList.add('hidden');
        });
        
        const formContainer = document.getElementById('formContainer');
        if (formContainer) formContainer.classList.add('overflow-hidden');
    }

    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        const isHidden = dropdown.classList.contains('hidden');
        
        hideAllDropdowns();

        if (isHidden) {
            dropdown.classList.remove('hidden');
            const formContainer = document.getElementById('formContainer');
            if (formContainer) formContainer.classList.remove('overflow-hidden');
        }
    }

    window.addEventListener('click', function(e) {
        const isInsideDropdown = e.target.closest('#dropdownTipeContainer') ||
                                 e.target.closest('#dropdownItemContainer') ||
                                 e.target.closest('#dropdownWaktuMulaiContainer') ||
                                 e.target.closest('#dropdownWaktuSelesaiContainer');

        if (!isInsideDropdown) {
            hideAllDropdowns();
        }
    });

    // --- Step 1 Item Select Logic ---
    function selectTipe(value, text, iconName) {
        currentTipe = value;
        document.getElementById('textTipe').innerText = text;
        
        const iconElement = document.getElementById('iconTipe');
        iconElement.setAttribute('data-lucide', iconName);
        lucide.createIcons({ nameAttr: 'data-lucide', attrs: { class: "h-4 w-4 text-gray-500" } });

        document.querySelectorAll('.check-tipe').forEach(el => el.classList.add('hidden'));
        document.getElementById(`check-${value}`).classList.remove('hidden');
        document.getElementById('labelItem').innerText = `Pilih ${text}`;
        
        const textItem = document.getElementById('textItem');
        textItem.innerText = 'Pilih item';
        textItem.classList.replace('text-gray-900', 'text-gray-500');

        renderItemOptions();
        hideAllDropdowns(); 
    }

    function selectItem(text) {
        const textElement = document.getElementById('textItem');
        textElement.innerText = text;
        textElement.classList.replace('text-gray-500', 'text-gray-900');
        hideAllDropdowns(); 
    }

    function renderItemOptions() {
        const container = document.getElementById('dropdownItem');
        const options = dataOptions[currentTipe];
        let html = '';
        options.forEach(opt => {
            html += `<div onclick="selectItem('${opt}')" class="px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">${opt}</div>`;
        });
        container.innerHTML = html;
    }
</script>
</body>
</html>