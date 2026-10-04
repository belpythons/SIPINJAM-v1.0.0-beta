<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Peminjaman - {{ $peminjaman->nomor_surat }}</title>
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-white text-slate-850 px-12 py-10 leading-relaxed text-sm">
    <!-- Kop Surat Resmi -->
    <div class="flex items-center justify-between border-b-4 border-double border-slate-900 pb-3 mb-6">
        <div class="text-center w-full">
            <h2 class="text-xs font-bold tracking-widest uppercase text-slate-600 mb-0.5">Yayasan Pendidikan Bessai
                Berinta</h2>
            <h1 class="text-xl font-extrabold tracking-wide uppercase text-slate-900">Sekolah Tinggi Teknologi Bontang
            </h1>
            <p class="text-[10px] text-slate-500 font-medium mt-1">Jl. Brigjend Katamso No.40, Bontang Utara, Kota
                Bontang, Kalimantan Timur 75313</p>
            <p class="text-[10px] text-slate-400 font-medium">Website: <a href="https://stitek.ac.id"
                    class="text-blue-600 underline">stitek.ac.id</a> | Telp: (0548) 22212</p>
        </div>
    </div>

    <!-- Tanggal Kanan Atas -->
    <div class="flex justify-end mb-6">
        <div class="font-medium text-slate-800">
            Bontang, {{ \Carbon\Carbon::parse($peminjaman->approved_at)->translatedFormat('d F Y') }}
        </div>
    </div>

    <!-- Nomor, Lampiran, Perihal -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <table class="w-full text-slate-800">
                <tr>
                    <td class="w-20 valign-top font-semibold text-slate-600">Nomor</td>
                    <td class="w-4 valign-top text-slate-400">:</td>
                    <td class="text-slate-900 font-medium">{{ $peminjaman->nomor_surat }}</td>
                </tr>
                <tr>
                    <td class="valign-top font-semibold text-slate-600">Lampiran</td>
                    <td class="valign-top text-slate-400">:</td>
                    <td class="text-slate-900 font-medium">-</td>
                </tr>
                <tr>
                    <td class="valign-top font-semibold text-slate-600 text-nowrap">Perihal</td>
                    <td class="valign-top text-slate-400">:</td>
                    <td class="font-bold text-slate-900">Surat Izin Peminjaman Aset ({{ ucfirst($peminjaman->tipe) }})
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Tujuan Surat -->
    <div class="mb-6 space-y-1">
        <p class="text-slate-700">Kepada Yth.</p>
        <p class="font-bold text-slate-950">Ketua STITEK Bontang</p>
        <p class="font-semibold text-slate-800">(Bapak Hardianto, S.T., M.Eng.)</p>
        <p class="text-slate-700">di -</p>
        <p class="pl-4 text-slate-700">Tempat</p>
    </div>

    <!-- Salam Pembuka & Pengantar -->
    <div class="text-justify mb-6 space-y-3">
        <p class="text-slate-800">Dengan hormat,</p>
        <p class="indent-8 text-slate-800">
            Sehubungan dengan kebutuhan sarana penunjang kegiatan akademis/kemahasiswaan di lingkungan Sekolah Tinggi
            Teknologi Bontang, dengan ini diajukan permohonan peminjaman aset kampus dengan rincian pemohon dan aset
            sebagai berikut:
        </p>
    </div>

    <!-- Tabel Detail Peminjam & Aset (Tailwind border-slate-300) -->
    <table class="w-full border-collapse border border-slate-300 text-slate-800 mb-8 rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-slate-50 text-slate-700 border-b border-slate-300">
                <th class="border border-slate-300 px-4 py-3 text-left font-bold text-xs uppercase tracking-wider">
                    Detail Peminjam</th>
                <th class="border border-slate-300 px-4 py-3 text-left font-bold text-xs uppercase tracking-wider">Aset
                    yang Dipinjam</th>
                <th class="border border-slate-300 px-4 py-3 text-left font-bold text-xs uppercase tracking-wider">Waktu
                    Penggunaan</th>
            </tr>
        </thead>
        <tbody>
            <tr class="align-top hover:bg-slate-50/50 transition-colors">
                <td class="border border-slate-300 px-4 py-3.5 space-y-1">
                    <p class="font-bold text-slate-950">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500 font-medium">NIM / Email:</p>
                    <p class="text-xs text-slate-600 font-mono">{{ $user->email }}</p>
                </td>
                <td class="border border-slate-300 px-4 py-3.5 space-y-1">
                    <p class="font-bold text-slate-950">{{ $asset->nama ?? $peminjaman->nama_item }}</p>
                    <p class="text-xs text-slate-500 font-medium">Kode Aset: <span
                            class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-700">{{ $asset->kode ?? '-' }}</span>
                    </p>
                    @if($peminjaman->tipe === 'ruangan')
                        <p class="text-xs text-slate-600">Lokasi: {{ $asset->lokasi ?? '-' }}</p>
                        <p class="text-xs text-slate-600 font-medium">Kapasitas: {{ $asset->kapasitas ?? '-' }} Orang</p>
                    @else
                        <p class="text-xs text-slate-600">Kategori: {{ $asset->kategori ?? '-' }}</p>
                        <p class="text-xs text-slate-600 font-medium">Jumlah: 1 Unit</p>
                    @endif
                </td>
                <td class="border border-slate-300 px-4 py-3.5 space-y-1">
                    <p class="font-bold text-slate-950">
                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_mulai)->translatedFormat('d M Y') }}
                        @if($peminjaman->tanggal_mulai != $peminjaman->tanggal_selesai)
                            s/d {{ \Carbon\Carbon::parse($peminjaman->tanggal_selesai)->translatedFormat('d M Y') }}
                        @endif
                    </p>
                    <p class="text-xs text-slate-600 font-semibold">Pukul: {{ $peminjaman->jam_mulai }} -
                        {{ $peminjaman->jam_selesai }} WITA</p>
                    <p class="text-xs text-slate-500 italic mt-1.5">Keperluan: {{ $peminjaman->keterangan }}</p>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Ketentuan / Penutup -->
    <div class="text-justify mb-8 space-y-3">
        <p class="text-slate-800">
            Dengan disetujuinya surat permohonan ini, kami selaku peminjam menyatakan berkomitmen penuh untuk mematuhi
            segala peraturan dan tata tertib peminjaman aset di STITEK Bontang. Kami bertanggung jawab penuh atas
            kebersihan, keamanan, serta pengembalian aset dalam kondisi baik dan tepat waktu.
        </p>
        <p class="text-slate-800">
            Demikian surat izin peminjaman ini dibuat, atas perhatian dan kerja sama Bapak, kami ucapkan terima kasih.
        </p>
    </div>

    <!-- Tanda Tangan Sejajar di Bawah (Ketua Panitia kiri, Sekretaris Panitia kanan) -->
    <div class="grid grid-cols-2 gap-8 text-center text-slate-800 mt-12">
        <div>
            <p class="text-slate-500 font-medium">Hormat Kami,</p>
            <p class="font-bold text-slate-900 mt-1 mb-20">Ketua Panitia</p>
            <p class="font-extrabold underline text-slate-950">{{ $user->name }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">NIM / ID: {{ $user->id }}</p>
        </div>
        <div>
            <p class="text-slate-500 font-medium">Mengetahui,</p>
            <p class="font-bold text-slate-900 mt-1 mb-20">Sekretaris Panitia</p>
            <p class="font-extrabold underline text-slate-950">Aisyah Rahmawati, S.Kom.</p>
            <p class="text-xs text-slate-500 font-medium mt-1">NIP: 2024090123</p>
        </div>
    </div>

    <!-- Footer Otomatis -->
    <div
        class="fixed bottom-4 left-12 right-12 text-center text-[10px] text-slate-400 border-t border-slate-200 pt-2.5">
        Sistem SiPinjam STITEK Bontang &bull; Dokumen digital ini sah dan diterbitkan secara elektronik &bull; Dicetak:
        {{ now()->translatedFormat('d/m/Y H:i') }} WITA
    </div>
</body>

</html>