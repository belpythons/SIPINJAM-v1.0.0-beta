{{-- Admin Sidebar Partial --}}
{{-- Usage: @include('admin.partials.sidebar', ['activePage' => 'dashboard']) --}}

<aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-gradient-to-b from-[#c2410c] to-[#f97316] text-white transition-all duration-300 flex flex-col hidden lg:flex shadow-xl">
    <div class="flex-1 flex flex-col">
        <div class="flex h-16 items-center gap-3 px-6 flex-shrink-0">
            <img src="{{ asset('image/logo-sp.png') }}" alt="Logo SiPinjam" class="w-10 h-auto drop-shadow-md">
            <h1 class="text-2xl font-bold tracking-wide">SIPINJAM</h1>
        </div>

        <nav class="px-4 space-y-1.5 mt-2">
            @php
                $nav = [
                    'dashboard'   => ['route' => 'admin.dashboard',         'icon' => 'fas fa-border-all',     'label' => 'Dashboard'],
                    'user'        => ['route' => 'admin.kelola_user',       'icon' => 'fas fa-user-friends',   'label' => 'Kelola User'],
                    'peminjaman'  => ['route' => 'admin.kelola_peminjaman', 'icon' => 'far fa-calendar-alt',   'label' => 'Kelola Peminjaman'],
                    'ruangan'     => ['route' => 'admin.kelola_ruangan',    'icon' => 'fas fa-building',       'label' => 'Kelola Ruangan'],
                    'barang'      => ['route' => 'admin.kelola_barang',     'icon' => 'fas fa-box',            'label' => 'Kelola Barang'],
                    'kalender'    => ['route' => 'admin.kelola_kalender',   'icon' => 'fas fa-calendar-days',  'label' => 'Kelola Kalender'],
                    'laporan'     => ['route' => 'admin.laporan',           'icon' => 'fas fa-chart-bar',      'label' => 'Laporan'],
                ];
            @endphp

            @foreach($nav as $key => $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors
                          {{ ($activePage ?? '') === $key ? 'bg-white/20 text-white font-medium' : 'hover:bg-white/10 text-white/90' }}">
                    <i class="{{ $item['icon'] }} w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>
</aside>
