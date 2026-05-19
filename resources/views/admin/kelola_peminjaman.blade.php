<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peminjaman - SiPinjam Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-sp.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '#ea580c', // Orange dominan
                            dark: '#c2410c',
                            light: '#f97316'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#f8f9fa] text-gray-800 font-sans flex h-screen overflow-hidden">

    @include('admin.partials.sidebar', ['activePage' => 'peminjaman'])

    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">

        @include('admin.partials.navbar')

        <div class="flex-1 overflow-y-auto p-8">
            
            <div class="mb-8">
                <h2 class="text-[28px] font-bold text-gray-900">Kelola Peminjaman</h2>
                <p class="text-gray-500 mt-1 text-[15px]">Kelola persetujuan dan pantau status peminjaman ruangan dan barang</p>
            </div>

            @php
                $totalPeminjaman = $peminjamans->count();
                $menungguCount = $peminjamans->where('status', 'menunggu')->count();
                $aktifCount = $peminjamans->whereIn('status', ['disetujui', 'sedang_dipinjam'])->count();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100/80 flex items-center gap-5">
                    <div class="w-14 h-14 rounded-xl bg-yellow-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-yellow-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[13px] text-gray-500 font-medium mb-0.5">Menunggu Persetujuan</p>
                        <p class="text-[28px] font-bold text-gray-900 leading-none">{{ $menungguCount }}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100/80 flex items-center gap-5">
                    <div class="w-14 h-14 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <i class="far fa-calendar-check text-green-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[13px] text-gray-500 font-medium mb-0.5">Aktif</p>
                        <p class="text-[28px] font-bold text-gray-900 leading-none">{{ $aktifCount }}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100/80 flex items-center gap-5">
                    <div class="w-14 h-14 rounded-xl bg-gray-50 flex items-center justify-center flex-shrink-0">
                        <i class="far fa-file-alt text-gray-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[13px] text-gray-500 font-medium mb-0.5">Total Peminjaman</p>
                        <p class="text-[28px] font-bold text-gray-900 leading-none">{{ $totalPeminjaman }}</p>
                    </div>
                </div>
            </div>

            <div class="flex bg-white rounded-xl shadow-sm border border-gray-100/80 p-1.5 mb-6 w-full">
                <button id="btn-menunggu" onclick="filterTab('menunggu', this)" class="tab-btn flex-1 flex justify-center items-center gap-2 py-2.5 text-[14px] font-semibold text-gray-900 bg-white rounded-lg shadow-sm border border-gray-100 transition-all">
                    Menunggu Persetujuan <span class="bg-[#e11d48] text-white text-[11px] w-[22px] h-[22px] flex items-center justify-center rounded-full">{{ $menungguCount }}</span>
                </button>
                <button id="btn-aktif" onclick="filterTab('aktif', this)" class="tab-btn flex-1 flex justify-center items-center gap-2 py-2.5 text-[14px] font-medium text-gray-500 hover:text-gray-800 border border-transparent transition-all">
                    Aktif <span class="bg-blue-500 text-white text-[11px] w-[22px] h-[22px] flex items-center justify-center rounded-full">{{ $aktifCount }}</span>
                </button>
                <button id="btn-riwayat" onclick="filterTab('riwayat', this)" class="tab-btn flex-1 flex justify-center items-center gap-2 py-2.5 text-[14px] font-medium text-gray-500 hover:text-gray-800 border border-transparent transition-all">
                    Riwayat
                </button>
            </div>

            <div class="relative mb-6">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 font-light"></i>
                </div>
                <input type="text" id="searchInput" onkeyup="filterTable()" class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand transition-all shadow-sm" placeholder="Cari berdasarkan nama, item, atau tujuan...">
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[13px] text-gray-900 font-bold bg-white">
                                <th class="px-6 py-4 whitespace-nowrap">Peminjam</th>
                                <th class="px-6 py-4 whitespace-nowrap">Tipe</th>
                                <th class="px-6 py-4 whitespace-nowrap">Item</th>
                                <th class="px-6 py-4 whitespace-nowrap">Tanggal</th>
                                <th class="px-6 py-4 whitespace-nowrap">Tujuan</th>
                                <th class="px-6 py-4 whitespace-nowrap">Status</th>
                                <th class="px-6 py-4 text-right whitespace-nowrap pr-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody" class="divide-y divide-gray-50 text-[14px]">
                            @forelse($peminjamans as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors search-row" data-status="{{ $item->status }}">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $item->user->name ?? 'User' }}</td>
                                
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $item->tipe == 'ruangan' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                                        {{ ucfirst($item->tipe) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-gray-700 font-medium">
                                    {{ $item->nama_item ?? '-' }}
                                </td>
                                
                                <td class="px-6 py-4 text-gray-800">
                                    <div class="font-medium">{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-' }}</div>
                                    <div class="text-[12px] text-gray-400 mt-0.5">
                                        @if($item->jam_mulai && $item->jam_selesai)
                                            {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }} WIB
                                        @else
                                            - - -
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-gray-700 truncate max-w-[150px]">{{ $item->keterangan }}</td>
                                
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'menunggu' => 'bg-yellow-100 text-yellow-600',
                                            'disetujui' => 'bg-blue-600 text-white',
                                            'sedang_dipinjam' => 'bg-blue-600 text-white',
                                            'selesai' => 'bg-gray-100 text-gray-600',
                                            'ditolak' => 'bg-red-600 text-white',
                                        ];
                                        $class = $statusClasses[$item->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold {{ $class }}">
                                        {{ ucfirst($item->status == 'sedang_dipinjam' ? 'disetujui' : $item->status) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2 pr-2">
                                        <button type="button" onclick='openAdminDetailModal(@json($item), "{{ $item->user->name ?? 'User' }}")' class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors" title="Lihat Detail">
                                            <i class="far fa-eye text-[13px]"></i>
                                        </button>

                                        @if($item->status == 'menunggu')
                                            <form action="{{ route('admin.peminjaman.setujui', $item->id) }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white hover:bg-blue-700 transition-colors" title="Setujui">
                                                    <i class="fas fa-check text-[13px]"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.peminjaman.tolak', $item->id) }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-[#e11d48] flex items-center justify-center text-white hover:bg-red-700 transition-colors" title="Tolak">
                                                    <i class="fas fa-times text-[13px]"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada data peminjaman.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <footer class="mt-auto pt-6 pb-2 flex justify-between items-center text-[13px] text-gray-500">
                <p>&copy; 2026 SIPINJAM - Sistem Informasi Peminjaman Ruangan dan Barang</p>
                <p>Built with Next.js</p>
            </footer>

        </div>
    </main>
    <script>
        // Default kategori saat halaman pertama dimuat
        let currentCategory = 'menunggu'; 

        // Fungsi saat Tab diklik
        function filterTab(category, btnElement) {
            currentCategory = category;

            // 1. Reset semua warna tombol tab menjadi tidak aktif (abu-abu)
            let buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => {
                btn.classList.remove('font-semibold', 'text-gray-900', 'bg-white', 'shadow-sm', 'border-gray-100');
                btn.classList.add('font-medium', 'text-gray-500', 'border-transparent');
            });

            // 2. Beri warna aktif (putih & bayangan) pada tombol yang diklik
            btnElement.classList.remove('font-medium', 'text-gray-500', 'border-transparent');
            btnElement.classList.add('font-semibold', 'text-gray-900', 'bg-white', 'shadow-sm', 'border-gray-100');

            // 3. Terapkan filter ke tabel
            applyFilters();
        }

        // Fungsi untuk Membuka Modal dan Mengisi Data
        function openAdminDetailModal(item, userName) {
            document.getElementById('adminDetailModal').classList.remove('hidden');
            
            // Text Sederhana
            document.getElementById('det_id').innerText = item.id;
            document.getElementById('det_nama').innerText = item.nama_item || '-';
            document.getElementById('det_tipe').innerText = item.tipe ? (item.tipe.charAt(0).toUpperCase() + item.tipe.slice(1)) : '-';
            document.getElementById('det_keterangan').innerText = item.keterangan || '-';
            document.getElementById('det_user_name').innerText = userName;

            // Format Tanggal Pengajuan
            let createdDate = new Date(item.created_at);
            document.getElementById('det_created').innerText = createdDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) + ', ' + createdDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

            // Format Jadwal Peminjaman
            const formatTgl = (dateStr) => {
                if(!dateStr) return '-';
                return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            };
            const formatJam = (timeStr) => {
                if(!timeStr) return '-';
                return timeStr.substring(0, 5) + ' WIB';
            };

            document.getElementById('det_mulai_tgl').innerText = formatTgl(item.tanggal_mulai);
            document.getElementById('det_mulai_jam').innerText = formatJam(item.jam_mulai);
            document.getElementById('det_selesai_tgl').innerText = formatTgl(item.tanggal_selesai);
            document.getElementById('det_selesai_jam').innerText = formatJam(item.jam_selesai);

            // Hitung Durasi Sederhana
            if(item.jam_mulai && item.jam_selesai) {
                let m = parseInt(item.jam_mulai.split(':')[0]);
                let s = parseInt(item.jam_selesai.split(':')[0]);
                let durasi = s - m;
                document.getElementById('det_durasi').innerText = (durasi > 0 ? durasi : 1) + " jam";
            } else {
                document.getElementById('det_durasi').innerText = "-";
            }

            // Styling Berdasar Tipe (Ruangan/Barang)
            if(item.tipe === 'ruangan') {
                document.getElementById('det_tipe_label').innerText = 'Informasi Ruangan';
                document.getElementById('det_nama_label').innerText = 'Ruangan';
                document.getElementById('det_tipe_icon').className = 'fas fa-door-open';
            } else {
                document.getElementById('det_tipe_label').innerText = 'Informasi Barang';
                document.getElementById('det_nama_label').innerText = 'Barang';
                document.getElementById('det_tipe_icon').className = 'fas fa-box';
            }

            // Styling Berdasar Status
            const statusBox = document.getElementById('det_status_box');
            const statusText = document.getElementById('det_status_text');
            const statusIcon = document.getElementById('det_status_icon');
            const headerIcon = document.getElementById('det_header_icon');

            // Reset icon class
            statusIcon.className = "w-10 h-10 rounded-full border flex items-center justify-center";

            if (item.status === 'menunggu') {
                statusText.innerText = "Menunggu Persetujuan";
                statusIcon.classList.add("border-gray-200", "text-gray-500");
                statusIcon.innerHTML = '<i class="far fa-clock text-xl"></i>';
                headerIcon.className = "far fa-clock text-gray-900 text-xl";
            } else if (item.status === 'disetujui' || item.status === 'sedang_dipinjam' || item.status === 'selesai') {
                statusText.innerText = (item.status === 'selesai') ? "Selesai" : "Disetujui";
                statusIcon.classList.add("border-gray-200", "text-gray-900");
                statusIcon.innerHTML = '<i class="far fa-check-circle text-xl"></i>';
                headerIcon.className = "far fa-check-circle text-gray-900 text-xl";
            } else {
                statusText.innerText = "Ditolak";
                statusIcon.classList.add("border-gray-200", "text-gray-900");
                statusIcon.innerHTML = '<i class="far fa-times-circle text-xl"></i>';
                headerIcon.className = "far fa-times-circle text-gray-900 text-xl";
            }
        }

        // Fungsi Menutup Modal
        function closeAdminDetailModal() {
            document.getElementById('adminDetailModal').classList.add('hidden');
        }

        // Fungsi saat mengetik di Search Bar
        function filterTable() {
            applyFilters();
        }

        // Fungsi Utama untuk menyaring data berdasarkan Tab + Search Bar
        function applyFilters() {
            let searchInput = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#tableBody .search-row");

            rows.forEach(row => {
                let rowText = row.innerText.toLowerCase();
                let status = row.getAttribute('data-status');
                
                // Cek apakah baris cocok dengan pencarian teks
                let matchesSearch = rowText.includes(searchInput);
                
                // Cek apakah baris cocok dengan Tab yang sedang dipilih
                let matchesTab = false;
                if (currentCategory === 'menunggu' && status === 'menunggu') {
                    matchesTab = true;
                } else if (currentCategory === 'aktif' && (status === 'disetujui' || status === 'sedang_dipinjam')) {
                    matchesTab = true;
                } else if (currentCategory === 'riwayat' && (status === 'selesai' || status === 'ditolak')) {
                    matchesTab = true;
                }

                // Tampilkan baris HANYA JIKA cocok dengan KEDUANYA (Tab dan Search)
                if (matchesSearch && matchesTab) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        // Jalankan filter pertama kali saat halaman dimuat (agar langsung masuk ke tab Menunggu)
        window.onload = function() {
            applyFilters();
        };
    </script>
    <div id="adminDetailModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                <div class="flex items-center gap-2">
                    <i class="far fa-check-circle text-gray-900 text-xl" id="det_header_icon"></i>
                    <h2 class="text-lg font-bold text-gray-900">Detail Peminjaman</h2>
                </div>
                <button type="button" onclick="closeAdminDetailModal()" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-5 overflow-y-auto bg-gray-50/50 space-y-4">
                <p class="text-xs text-gray-400 mb-1"># ID: booking-<span id="det_id"></span></p>

                <div id="det_status_box" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Status Peminjaman</p>
                        <h3 id="det_status_text" class="text-xl font-bold text-gray-900"></h3>
                    </div>
                    <div id="det_status_icon" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-500"></div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 text-blue-600 font-semibold text-sm">
                        <i class="fas fa-door-open" id="det_tipe_icon"></i> <span id="det_tipe_label">Informasi Ruangan</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg"><span class="text-[13px] text-gray-500">Nama <span id="det_nama_label">Ruangan</span></span> <span id="det_nama" class="text-[13px] font-bold text-gray-900 text-right w-1/2 truncate"></span></div>
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg"><span class="text-[13px] text-gray-500">Tipe Peminjaman</span> <span id="det_tipe" class="text-[13px] font-bold text-gray-900"></span></div>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 text-blue-600 font-semibold text-sm">
                        <i class="far fa-clock"></i> Jadwal Peminjaman
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <div class="border border-gray-100 p-3 rounded-xl bg-white shadow-sm">
                            <p class="text-[12px] text-gray-500 mb-1 flex items-center gap-1"><i class="far fa-calendar-alt"></i> Waktu Mulai</p>
                            <p id="det_mulai_tgl" class="text-[14px] font-bold text-gray-900"></p>
                            <p id="det_mulai_jam" class="text-[12px] text-gray-400 mt-0.5"></p>
                        </div>
                        <div class="border border-gray-100 p-3 rounded-xl bg-white shadow-sm">
                            <p class="text-[12px] text-gray-500 mb-1 flex items-center gap-1"><i class="far fa-calendar-alt"></i> Waktu Selesai</p>
                            <p id="det_selesai_tgl" class="text-[14px] font-bold text-gray-900"></p>
                            <p id="det_selesai_jam" class="text-[12px] text-gray-400 mt-0.5"></p>
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl flex justify-between items-center">
                        <p class="text-[13px] text-gray-600 flex items-center gap-1.5"><i class="fas fa-stopwatch text-gray-400"></i> Durasi Peminjaman</p>
                        <p class="text-[13px] font-bold text-gray-900" id="det_durasi"></p>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm space-y-3">
                    <div class="flex items-center gap-2 text-blue-600 font-semibold text-sm">
                        <i class="far fa-file-alt"></i> Tujuan Peminjaman
                    </div>
                    <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 text-[13px] text-gray-700 whitespace-pre-line" id="det_keterangan"></div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm space-y-3">
                    <div class="flex items-center gap-2 text-blue-600 font-semibold text-sm">
                        <i class="far fa-user"></i> Informasi Peminjam
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg"><span class="text-[13px] text-gray-500">Nama Peminjam</span> <span id="det_user_name" class="text-[13px] font-bold text-gray-900"></span></div>
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg"><span class="text-[13px] text-gray-500">Tanggal Pengajuan</span> <span id="det_created" class="text-[13px] font-bold text-gray-900"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>