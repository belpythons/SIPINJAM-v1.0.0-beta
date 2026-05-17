{{-- User Sidebar Partial --}}
{{-- Usage: @include('user.partials.sidebar', ['activePage' => 'dashboard']) --}}

<aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-gradient-to-b from-[#3b82f6] to-[#1e3a8a] text-white transition-all duration-300 flex flex-col hidden lg:flex">
    {{-- Logo --}}
    <div class="flex h-16 items-center gap-3 px-6 flex-shrink-0">
        <img src="{{ asset('image/logo-sp.png') }}" alt="Logo SiPinjam" class="w-10 h-auto drop-shadow-md">
        <h1 class="text-2xl font-bold tracking-tight">SIPINJAM</h1>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-1 px-3 py-4 overflow-y-auto">
        @php
            $nav = [
                'dashboard'  => ['route' => 'dashboard',       'icon' => 'home',          'label' => 'Dashboard'],
                'bookings'   => ['route' => 'bookings.index',  'icon' => 'calendar',      'label' => 'Riwayat Peminjaman'],
                'ruangan'    => ['route' => 'ruangan.index',   'icon' => 'door-open',     'label' => 'Ruangan'],
                'barang'     => ['route' => 'barang.index',    'icon' => 'package',       'label' => 'Barang'],
                'tata_tertib'=> ['route' => 'tata_tertib.index','icon' => 'book-open',    'label' => 'Tata Tertib'],
                'kalender'   => ['route' => 'kalender.index',  'icon' => 'calendar-days', 'label' => 'Kalender Akademik'],
                'laporan'    => ['route' => 'laporan.index',   'icon' => 'bar-chart-3',   'label' => 'Laporan Saya'],
            ];
        @endphp

        @foreach($nav as $key => $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200
                      {{ ($activePage ?? '') === $key ? 'bg-white/20 text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <i data-lucide="{{ $item['icon'] }}" class="h-5 w-5 flex-shrink-0"></i>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>
</aside>
