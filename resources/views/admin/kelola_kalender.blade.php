<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kalender - SiPinjam Admin</title>
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

    @include('admin.partials.sidebar', ['activePage' => 'kalender'])

    <main class="flex-1 lg:ml-64 h-screen overflow-y-auto flex flex-col">

        @include('admin.partials.navbar')

        <div class="flex-1 p-6 lg:p-8 space-y-6">

            {{-- Toast --}}
            @if(session('success'))
                <div id="toast" class="fixed top-6 right-6 z-[60] bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in">
                    <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
                    <button onclick="document.getElementById('toast').remove()" class="ml-2 opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div id="toast" class="fixed top-6 right-6 z-[60] bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in">
                    <i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span>
                    <button onclick="document.getElementById('toast').remove()" class="ml-2 opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button>
                </div>
            @endif

            {{-- Page Header --}}
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Kelola Kalender</h2>
                <p class="text-gray-500 text-sm mt-1">Upload dan kelola gambar kalender akademik STITEK</p>
            </div>

            {{-- Upload Form — Corporate Clean --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-cloud-arrow-up text-brand"></i> Upload Kalender Baru
                </h3>
                <form action="{{ route('admin.kalender.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gambar Kalender</label>
                            <input type="file" name="image" accept="image/*" required
                                class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-gray-100 file:text-sm file:font-medium file:text-gray-700 file:cursor-pointer hover:file:bg-gray-200">
                            @error('image')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="w-full md:w-40">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun</label>
                            <input type="number" name="year" value="{{ date('Y') }}" min="2020" max="2100" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            @error('year')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-brand hover:bg-brand-dark text-white font-medium rounded-lg text-sm transition-colors shadow-sm whitespace-nowrap">
                            <i class="fas fa-upload mr-1.5"></i> Upload
                        </button>
                    </div>
                </form>
            </div>

            {{-- Calendar Cards Grid --}}
            @if($calendars->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="far fa-calendar text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700">Belum Ada Kalender</h3>
                    <p class="text-gray-400 mt-1 text-sm">Upload gambar kalender pertama Anda di atas.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($calendars as $cal)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            {{-- Image Preview --}}
                            <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                                <img src="{{ asset('storage/' . $cal->image_path) }}"
                                     alt="Kalender {{ $cal->year }}"
                                     class="w-full h-full object-cover">
                                @if($cal->is_active)
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                            <i class="fas fa-check-circle text-[10px]"></i> Aktif
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Card Body --}}
                            <div class="p-5 flex-1 flex flex-col">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-900">Tahun {{ $cal->year }}</h4>
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cal->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $cal->is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </div>

                                <p class="text-xs text-gray-500 mb-4">
                                    Diunggah: {{ $cal->created_at->format('d M Y, H:i') }} WIB
                                </p>

                                <div class="flex gap-3 mt-auto">
                                    @if(!$cal->is_active)
                                        <form action="{{ route('admin.kalender.activate', $cal->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-xs transition-colors">
                                                <i class="fas fa-toggle-on mr-1"></i> Aktifkan
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.kalender.destroy', $cal->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus kalender ini?')"
                                          class="{{ $cal->is_active ? 'flex-1' : '' }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-xs transition-colors">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <footer class="mt-auto px-8 py-4 border-t border-gray-200 text-xs text-gray-500 flex justify-between bg-white">
            <span>&copy; 2026 SiPinjam</span>
            <span>Built with Laravel</span>
        </footer>
    </main>

    <style>@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } } .animate-slide-in { animation: slideIn 0.3s ease-out; }</style>
    <script>setTimeout(() => { document.getElementById('toast')?.remove(); }, 4000);</script>
</body>
</html>
