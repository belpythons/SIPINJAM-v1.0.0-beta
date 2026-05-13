<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ruangan - SIPINJAM Admin</title>
    
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

    <aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-gradient-to-b from-brand-dark to-brand-light text-white transition-all duration-300 flex flex-col hidden lg:flex shadow-xl">
        <div class="flex-1 flex flex-col">
            <div class="flex h-20 items-center gap-3 px-6 mt-2 flex-shrink-0">
                <img src="{{ asset('image/logo-sp.png') }}" alt="Logo SIPINJAM" class="w-10 h-auto drop-shadow-md">
                <h1 class="text-2xl font-bold tracking-wide">SIPINJAM</h1>
            </div>

            <div class="px-6 py-2 flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 rounded-full border-[1.5px] border-white/50 flex items-center justify-center font-semibold text-lg bg-white/10">
                    AS
                </div>
                <div class="leading-tight">
                    <p class="text-[15px] font-semibold">Admin SIPINJAM</p>
                    <p class="text-[12px] text-white/80 font-light mt-0.5">admin@sipinjam.ac.id</p>
                </div>
            </div>

            <nav class="px-4 space-y-1.5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-white/10 rounded-lg text-white/90 transition-colors">
                    <i class="fas fa-border-all w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.kelola_user') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-white/10 rounded-lg text-white/90 transition-colors">
                    <i class="fas fa-user-friends w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola User</span>
                </a>

                <a href="{{ route('admin.kelola_peminjaman') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-white/10 rounded-lg text-white/90 transition-colors">
                    <i class="far fa-calendar-alt w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola Peminjaman</span>
                </a>
                
                <a href="{{ route('admin.kelola_ruangan') }}" class="flex items-center space-x-3 px-4 py-3 bg-white/20 rounded-lg text-white font-medium transition-colors">
                    <i class="fas fa-building w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola Ruangan</span>
                </a>
                
                <a href="{{ route('admin.kelola_barang') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-white/10 rounded-lg text-white/90 transition-colors">
                    <i class="fas fa-box w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola Barang</span>
                </a>
            </nav>
        </div>

        <div class="border-t border-white/20 mt-auto">
            <a href="{{ route('logout') }}" 
                onclick="event.preventDefault(); document.getElementById('form-logout').submit();" 
                class="flex items-center space-x-3 px-8 py-5 hover:bg-white/10 text-white/90 transition-colors cursor-pointer">
                <i class="fas fa-sign-out-alt w-5 text-center text-[18px]"></i>
                <span class="text-[15px]">Keluar</span>
            </a>

            <form id="form-logout" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

    <main class="flex-1 lg:ml-64 h-screen overflow-y-auto flex flex-col p-8 relative">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Kelola Ruangan</h2>
                <p class="text-gray-500 mt-1">Kelola semua ruangan dalam sistem</p>
            </div>
            <!-- Tombol ditambah event onclick="openModal()" -->
            <button onclick="openModal()" class="bg-brand hover:bg-brand-dark text-white px-5 py-2.5 rounded-lg font-medium shadow-md transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Ruangan
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <span class="text-gray-800 font-semibold text-sm">Total Ruangan</span>
                <span class="text-4xl font-bold text-gray-900 mt-4">5</span>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <span class="text-gray-800 font-semibold text-sm">Tersedia</span>
                <span class="text-4xl font-bold text-green-600 mt-4">3</span>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <span class="text-gray-800 font-semibold text-sm">Booking</span>
                <span class="text-4xl font-bold text-yellow-500 mt-4">1</span>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <span class="text-gray-800 font-semibold text-sm">Digunakan</span>
                <span class="text-4xl font-bold text-red-600 mt-4">1</span>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Cari ruangan atau gedung..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand">
            </div>
            
            <div class="flex gap-4">
                <div class="relative min-w-[180px]">
                    <select class="w-full pl-10 pr-8 py-2.5 border border-gray-200 rounded-xl appearance-none bg-white shadow-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand">
                        <option>Semua Status</option>
                        <option>Tersedia</option>
                        <option>Booking</option>
                        <option>Digunakan</option>
                    </select>
                    <i class="fas fa-filter absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
                <div class="relative min-w-[180px]">
                    <select class="w-full pl-4 pr-8 py-2.5 border border-gray-200 rounded-xl appearance-none bg-white shadow-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand">
                        <option>Semua Gedung</option>
                        <option>Gedung A</option>
                        <option>Gedung B</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 flex-1 content-start">
            
            <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 flex flex-col">
                <div class="relative h-48 bg-gray-200">
                    <img src="https://images.unsplash.com/photo-1576060965384-2a6c2f70b4a9?auto=format&fit=crop&q=80&w=800" alt="Ruang Kuliah" class="w-full h-full object-cover">
                    <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-white text-[11px] font-bold tracking-wide bg-green-500 shadow-sm uppercase">
                        Tersedia
                    </span>
                </div>
                <div class="p-5 flex-1 border-t border-gray-50">
                    <h3 class="font-bold text-lg text-gray-900 mb-1">Ruang Kuliah 304</h3>
                </div>
            </div>

            <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 flex flex-col">
                <div class="relative h-48 bg-gray-200">
                    <img src="https://images.unsplash.com/photo-1596522354195-e84ae3c98731?auto=format&fit=crop&q=80&w=800" alt="Ruang Seminar" class="w-full h-full object-cover">
                    <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-white text-[11px] font-bold tracking-wide bg-yellow-500 shadow-sm uppercase">
                        Booking
                    </span>
                </div>
                <div class="p-5 flex-1 border-t border-gray-50">
                    <h3 class="font-bold text-lg text-gray-900 mb-1">Ruang Seminar</h3>
                </div>
            </div>

            <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 flex flex-col">
                <div class="relative h-48 bg-gray-200">
                    <img src="https://images.unsplash.com/photo-1544531586-fde5298cdd40?auto=format&fit=crop&q=80&w=800" alt="Lab Komputer" class="w-full h-full object-cover">
                    <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-white text-[11px] font-bold tracking-wide bg-red-600 shadow-sm uppercase">
                        Digunakan
                    </span>
                </div>
                <div class="p-5 flex-1 border-t border-gray-50">
                    <h3 class="font-bold text-lg text-gray-900 mb-1">Lab Komputer</h3>
                </div>
            </div>

        </div>

        <footer class="mt-auto pt-4 pb-2 flex justify-between items-center text-[13px] text-gray-500 border-t border-gray-200/60">
            <p>&copy; 2026 SIPINJAM - Sistem Informasi Peminjaman Ruangan dan Barang</p>
            <p>Built with Next.js</p>
        </footer>

    </main>

    <!-- Modal Tambah Ruangan -->
    <div id="modalTambahRuangan" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-[550px] overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Header Modal -->
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Tambah Ruangan Baru</h3>
                    <p class="text-[13px] text-gray-500 mt-1">Tambahkan ruangan baru ke sistem</p>
                </div>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Body Modal -->
            <div class="p-6 overflow-y-auto">
                <form action="#" method="POST" class="space-y-4">
                    
                    <!-- Foto Ruangan -->
                    <div>
                        <label class="block text-[14px] font-medium text-gray-900 mb-2">Foto Ruangan</label>
                        
                        <label class="inline-flex w-fit cursor-pointer items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-arrow-up-from-bracket"></i> Upload Foto
                            
                            <input type="file" name="foto_ruangan" accept="image/*" class="hidden">
                        </label>
                    </div>

                    <!-- Nama Ruangan -->
                    <div>
                        <label class="block text-[14px] font-medium text-gray-900 mb-2">Nama Ruangan *</label>
                        <input type="text" placeholder="Contoh: Ruang Kuliah 301" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm transition-all text-gray-700 placeholder-gray-400">
                    </div>

                    <!-- Gedung & Lantai -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-medium text-gray-900 mb-2">Gedung *</label>
                            <input type="text" placeholder="Contoh: Gedung A" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm transition-all text-gray-700 placeholder-gray-400">
                        </div>
                        <div>
                            <label class="block text-[14px] font-medium text-gray-900 mb-2">Lantai</label>
                            <input type="number" value="1" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm transition-all text-gray-700">
                        </div>
                    </div>

                    <!-- Kapasitas & Status -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-medium text-gray-900 mb-2">Kapasitas *</label>
                            <input type="number" value="1" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm transition-all text-gray-700">
                        </div>
                        <div>
                            <label class="block text-[14px] font-medium text-gray-900 mb-2">Status *</label>
                            <div class="relative">
                                <select class="w-full px-4 py-2.5 border border-gray-200 rounded-lg appearance-none bg-white shadow-sm text-gray-700 text-[14px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    <option>Tersedia</option>
                                    <option>Booking</option>
                                    <option>Digunakan</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Fasilitas -->
                    <div>
                        <label class="block text-[14px] font-medium text-gray-900 mb-2">Fasilitas *</label>
                        <div class="flex gap-3">
                            <input type="text" placeholder="Tambah fasilitas" class="flex-1 px-4 py-2.5 border border-gray-200 rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm transition-all text-gray-700 placeholder-gray-400">
                            <button type="button" class="px-5 py-2.5 bg-[#f3f4f6] hover:bg-gray-200 text-gray-800 font-medium rounded-lg text-[14px] transition-colors">
                                Tambah
                            </button>
                        </div>
                    </div>

                </form>
            </div>

            <!-- Footer Modal -->
            <div class="px-6 py-4 flex justify-end gap-3 mt-auto">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors text-[14px] shadow-sm">
                    Batal
                </button>
                <button type="button" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-[14px] shadow-sm">
                    Tambah Ruangan
                </button>
            </div>
            
        </div>
    </div>

    <!-- Script JavaScript untuk Modal -->
    <script>
        function openModal() {
            const modal = document.getElementById('modalTambahRuangan');
            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('modalTambahRuangan');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside the container
        document.getElementById('modalTambahRuangan').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>