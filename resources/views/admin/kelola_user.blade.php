<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - SIPINJAM Admin</title>
    
    <!-- Tailwind CSS & FontAwesome -->
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

   <!-- SIDEBAR -->
    <aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-brand text-white transition-all duration-300 flex flex-col hidden lg:flex shadow-xl">
        <div class="flex-1 flex flex-col">
            <!-- Logo -->
            <div class="flex h-20 items-center gap-3 px-6 mt-2 flex-shrink-0">
                <img src="{{ asset('image/logo-sp.png') }}" alt="Logo SIPINJAM" class="w-10 h-auto drop-shadow-md">
                <h1 class="text-2xl font-bold tracking-wide">SIPINJAM</h1>
            </div>

            <!-- User Info -->
            <div class="px-6 py-2 flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 rounded-full border-[1.5px] border-white/50 flex items-center justify-center font-semibold text-lg bg-white/10">
                    AS
                </div>
                <div class="leading-tight">
                    <p class="text-[15px] font-semibold">Admin SIPINJAM</p>
                    <p class="text-[12px] text-white/80 font-light mt-0.5">admin@sipinjam.ac.id</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="px-4 space-y-1.5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-white/10 rounded-lg text-white/90 transition-colors">
                    <i class="fas fa-border-all w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.kelola_user') }}" class="flex items-center space-x-3 px-4 py-3 bg-white/20 rounded-lg text-white font-medium transition-colors">
                    <i class="fas fa-user-friends w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola User</span>
                </a>

                <a href="{{ route('admin.kelola_peminjaman') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-white/10 rounded-lg text-white/90 transition-colors">
                    <i class="far fa-calendar-alt w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola Peminjaman</span>
                </a>
                
                <a href="{{ route('admin.kelola_ruangan') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-white/10 rounded-lg text-white/90 transition-colors">
                    <i class="fas fa-building w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola Ruangan</span>
                </a>
                
                <a href="{{ route('admin.kelola_barang') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-white/10 rounded-lg text-white/90 transition-colors">
                    <i class="fas fa-box w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola Barang</span>
                </a>
            </nav>
        </div>

        <!-- Footer Sidebar -->
        <!-- Footer Sidebar -->
        <div class="border-t border-white/20 mt-auto">
            <a href="{{ route('logout') }}" 
                onclick="event.preventDefault(); document.getElementById('form-logout').submit();" 
                class="flex items-center space-x-3 px-8 py-5 hover:bg-white/10 text-white/90 transition-colors cursor-pointer">
                <i class="fas fa-sign-out-alt w-5 text-center text-[18px]"></i>
                <span class="text-[15px]">Keluar</span>
            </a>

            <!-- Form tersembunyi yang akan mengeksekusi proses logout -->
            <form id="form-logout" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <!-- Top Header -->
        <header class="py-4 px-8 border-b border-gray-200 bg-[#f8f9fa] flex items-center z-10">
            <h1 class="text-[1.15rem] font-bold text-gray-900">Admin Portal</h1>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8">
            
            <!-- Page Title & Top Button -->
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-[28px] font-bold text-gray-900">Kelola User</h2>
                    <p class="text-gray-500 mt-1">Manajemen user dan hak akses sistem</p>
                </div>
                <button onclick="toggleModal('addUserModal')" class="bg-brand hover:bg-brand-dark text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center gap-2">
                    <i class="fas fa-plus text-sm"></i> Tambah User
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Card Total User -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100/80">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-[15px] font-medium text-gray-800">Total User</span>
                        <i class="fas fa-user-friends text-gray-400"></i>
                    </div>
                    <div class="text-[32px] font-bold text-gray-900">6</div>
                </div>

                <!-- Card Aktif -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100/80">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-[15px] font-medium text-gray-800">Aktif</span>
                        <i class="fas fa-user-check text-green-500"></i>
                    </div>
                    <div class="text-[32px] font-bold text-green-600">5</div>
                </div>

                <!-- Card Nonaktif -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100/80">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-[15px] font-medium text-gray-800">Nonaktif</span>
                        <i class="fas fa-user-times text-red-500"></i>
                    </div>
                    <div class="text-[32px] font-bold text-red-600">1</div>
                </div>
            </div>

            <!-- Toolbar: Search & Filters -->
            <div class="flex flex-col md:flex-row gap-4 mb-4">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 font-light"></i>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand transition-all" placeholder="Cari nama atau email...">
                </div>
                
                <div class="flex gap-3">
                    <select class="border border-gray-200 bg-white rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-brand">
                        <option>Semua Role</option>
                    </select>
                    <select class="border border-gray-200 bg-white rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-brand">
                        <option>Semua Status</option>
                    </select>
                    <button onclick="toggleModal('addUserModal')" class="bg-brand hover:bg-brand-dark text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition-all">
                        <i class="fas fa-user-plus text-sm"></i> Tambah User
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-sm text-gray-900 font-bold bg-white">
                                <th class="px-6 py-4 whitespace-nowrap">Nama</th>
                                <th class="px-6 py-4 whitespace-nowrap">Email</th>
                                <th class="px-6 py-4 whitespace-nowrap">Role</th>
                                <th class="px-6 py-4 whitespace-nowrap">Status</th>
                                <th class="px-6 py-4 whitespace-nowrap">Dibuat</th>
                                <th class="px-6 py-4 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-[14px]">
                            
                            <!-- User 1 -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800">John Doe</td>
                                <td class="px-6 py-4 text-gray-600">user@sipinjam.ac.id</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                        User
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-600 text-white shadow-sm">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">01 Jan 2024</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-gray-400 hover:text-gray-700 transition-colors">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- User 2 -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800">Admin SIPINJAM</td>
                                <td class="px-6 py-4 text-gray-600">admin@sipinjam.ac.id</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-brand text-white shadow-sm">
                                        Admin
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-600 text-white shadow-sm">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">01 Jan 2024</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-gray-400 hover:text-gray-700 transition-colors">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- User 3 -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800">Jane Smith</td>
                                <td class="px-6 py-4 text-gray-600">jane.smith@sipinjam.ac.id</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                        User
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-600 text-white shadow-sm">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">15 Feb 2024</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-gray-400 hover:text-gray-700 transition-colors">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- User 4 -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800">Michael Johnson</td>
                                <td class="px-6 py-4 text-gray-600">michael.j@sipinjam.ac.id</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                        User
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-[#e11d48] text-white shadow-sm">
                                        Nonaktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">10 Mar 2024</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-gray-400 hover:text-gray-700 transition-colors">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- User 5 -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800">Sarah Williams</td>
                                <td class="px-6 py-4 text-gray-600">sarah.w@sipinjam.ac.id</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                        User
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-600 text-white shadow-sm">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">20 Apr 2024</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-gray-400 hover:text-gray-700 transition-colors">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- User 6 -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800">Admin Support</td>
                                <td class="px-6 py-4 text-gray-600">support@sipinjam.ac.id</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-brand text-white shadow-sm">
                                        Admin
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-600 text-white shadow-sm">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">15 Jan 2024</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-gray-400 hover:text-gray-700 transition-colors">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                
                <!-- Table Footer (Info) -->
                <div class="px-6 py-5 border-t border-gray-100 bg-white">
                    <p class="text-sm text-gray-500">Menampilkan 6 dari 6 user</p>
                </div>
            </div>

            <!-- Page Footer -->
            <footer class="mt-auto pt-6 pb-2 flex justify-between items-center text-[13px] text-gray-500">
                <p>&copy; 2026 SIPINJAM - Sistem Informasi Peminjaman Ruangan dan Barang</p>
                <p>Built with Next.js</p>
            </footer>

        </div>
    </main>
    <!-- MODAL TAMBAH USER -->
    <div id="addUserModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 sm:p-8 transform transition-all relative">
            
            <!-- Tombol Tutup (X) -->
            <button type="button" onclick="toggleModal('addUserModal')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>

            <h2 class="text-xl font-bold text-gray-900 mb-1">Tambah User Baru</h2>
            <p class="text-sm text-gray-500 mb-6">Masukkan informasi untuk mendaftarkan pengguna baru ke sistem.</p>

            <!-- Ganti action dengan route yang benar nanti, misal: route('admin.users.store') -->
            <form action="#" method="POST" id="formTambahUser" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1.5">Alamat Email</label>
                    <input type="email" name="email" required placeholder="Contoh: budi@sipinjam.ac.id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1.5">Role Pengguna</label>
                    <div class="flex gap-3">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="role" value="user" class="peer sr-only" checked>
                            <div class="px-4 py-3 rounded-xl border border-gray-200 text-center peer-checked:border-brand peer-checked:bg-orange-50 peer-checked:text-brand transition-all">
                                <span class="font-medium text-sm">User Biasa</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="role" value="admin" class="peer sr-only">
                            <div class="px-4 py-3 rounded-xl border border-gray-200 text-center peer-checked:border-brand peer-checked:bg-orange-50 peer-checked:text-brand transition-all">
                                <span class="font-medium text-sm">Admin</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('addUserModal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-brand hover:bg-brand-dark rounded-xl shadow-sm transition-colors">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</body>
</html>