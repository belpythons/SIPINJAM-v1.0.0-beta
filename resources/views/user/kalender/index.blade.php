<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Akademik - SiPinjam</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-sp.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { user: { primary: '#3b82f6', dark: '#1e3a8a' } }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    @include('user.partials.sidebar', ['activePage' => 'kalender'])

    <main class="flex-1 flex flex-col h-screen overflow-y-auto lg:ml-64 bg-gray-50 transition-all duration-300">

        @include('user.partials.navbar')

        <div class="p-4 sm:p-6 lg:p-8 space-y-6 flex-1">

            {{-- Page Title --}}
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kalender Akademik STITEK</h1>
                <p class="text-sm text-gray-500 mt-1">Lihat dan unduh kalender akademik yang berlaku</p>
            </div>

            @if($calendar)
                {{-- Calendar Display Card — Corporate Clean --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    {{-- Header Bar --}}
                    <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white">
                                <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i> Tahun {{ $calendar->year }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Aktif
                            </span>
                        </div>
                        <a href="{{ route('kalender.export_pdf') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg text-sm transition-colors shadow-sm">
                            <i data-lucide="download" class="w-4 h-4"></i> Export PDF
                        </a>
                    </div>

                    {{-- Image Container --}}
                    <div class="p-6 bg-gray-50">
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-2 max-w-4xl mx-auto">
                            <img src="{{ asset('storage/' . $calendar->image_path) }}"
                                 alt="Kalender Akademik {{ $calendar->year }}"
                                 class="w-full rounded-md">
                        </div>
                    </div>

                    {{-- Footer Info --}}
                    <div class="bg-white px-6 py-3 flex items-center justify-between border-t border-gray-100">
                        <p class="text-xs text-gray-500 font-medium flex items-center gap-1.5">
                            <i data-lucide="info" class="w-3.5 h-3.5"></i>
                            Kalender Akademik STITEK — Tahun {{ $calendar->year }}
                        </p>
                        <p class="text-xs text-gray-400">
                            Diperbarui: {{ $calendar->updated_at->format('d M Y') }}
                        </p>
                    </div>
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-16 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="calendar-x" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Kalender</h3>
                    <p class="text-gray-400 text-sm max-w-md mx-auto">
                        Kalender akademik belum tersedia saat ini. Silakan hubungi admin untuk informasi lebih lanjut.
                    </p>
                </div>
            @endif

        </div>

        <footer class="mt-auto px-6 py-4 border-t border-gray-200 text-xs text-gray-400 flex justify-between bg-white">
            <span>© 2026 SiPinjam</span>
            <span>Built with Laravel</span>
        </footer>
    </main>
</div>

<script>lucide.createIcons();</script>
</body>
</html>
