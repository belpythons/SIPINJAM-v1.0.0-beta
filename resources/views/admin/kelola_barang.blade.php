<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peminjaman - SIPINJAM Admin</title>
    
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
                
                <a href="{{ route('admin.kelola_user') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-white/10 rounded-lg text-white/90 transition-colors">
                    <i class="fas fa-user-friends w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola User</span>
                </a>

                <!-- Aktif -->
                <a href="{{ route('admin.kelola_peminjaman') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-white/10 rounded-lg text-white/90 transition-colors">
                    <i class="far fa-calendar-alt w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola Peminjaman</span>
                </a>
                
                <a href="{{ route('admin.kelola_ruangan') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-white/10 rounded-lg text-white/90 transition-colors">
                    <i class="fas fa-building w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola Ruangan</span>
                </a>
                
                <a href="{{ route('admin.kelola_barang') }}" class="flex items-center space-x-3 px-4 py-3 bg-white/20 rounded-lg text-white font-medium transition-colors">
                    <i class="fas fa-box w-5 text-center text-[18px]"></i>
                    <span class="text-[15px]">Kelola Barang</span>
                </a>
            </nav>
        </div>

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

            <!-- Page Footer -->
            <footer class="mt-auto pt-6 pb-2 flex justify-between items-center text-[13px] text-gray-500">
                <p>&copy; 2026 SIPINJAM - Sistem Informasi Peminjaman Ruangan dan Barang</p>
                <p>Built with Next.js</p>
            </footer>

        </div>
    </main>
</body>
</html>