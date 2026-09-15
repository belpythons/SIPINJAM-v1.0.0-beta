@php
    // B-08: TIDAK ADA sumber daya eksternal (CDN/Google Fonts) di berkas ini.
    // Seluruh gaya di-inline dan font memakai yang tersedia di sistem, supaya
    // PDF tetap benar saat server tidak punya akses internet keluar.
    //
    // B-09: seluruh identitas surat dibaca dari config/sipinjam.php.
    $kop      = config('sipinjam.kop');
    $pejabat  = config('sipinjam.penandatangan.pejabat');
    $pengelola = config('sipinjam.penandatangan.pengelola_aset');
    $penerima = config('sipinjam.penandatangan.penerima_surat');
    $kota     = config('sipinjam.surat.kota');
    $zona     = config('sipinjam.surat.zona_waktu');

    // Logo di-embed sebagai data URI agar tidak perlu permintaan jaringan.
    $logoData = null;
    $logoPath = public_path($kop['logo_path'] ?? '');
    if (! empty($kop['logo_path']) && is_file($logoPath)) {
        $logoData = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION)
            . ';base64,' . base64_encode(file_get_contents($logoPath));
    }

    $tanggalSurat = $peminjaman->approved_at ?? now();
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Izin Peminjaman - {{ $peminjaman->nomor_surat }}</title>
    <style>
        @page {
            size: A4;
            margin: 2.5cm 2.5cm 2cm 2.5cm;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }

        /* ── Kop Surat ────────────────────────────────── */
        .kop {
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 18px;
        }

        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-logo { width: 80px; vertical-align: middle; }
        .kop-logo img { width: 72px; height: auto; }
        .kop-teks { text-align: center; vertical-align: middle; }

        .kop-yayasan {
            margin: 0;
            font-size: 11pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .kop-institusi {
            margin: 2px 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-alamat { margin: 0; font-size: 9pt; }

        /* ── Identitas surat ──────────────────────────── */
        .tanggal { text-align: right; margin-bottom: 14px; }

        .meta { border-collapse: collapse; margin-bottom: 16px; }
        .meta td { padding: 1px 0; vertical-align: top; }
        .meta .label { width: 80px; }
        .meta .sep { width: 14px; }

        .tujuan { margin-bottom: 16px; }
        .tujuan p { margin: 0; }
        .tujuan .nama { font-weight: bold; }

        p.isi { text-align: justify; margin: 0 0 10px 0; }
        p.isi.indent { text-indent: 2.5em; }

        /* ── Tabel rincian ────────────────────────────── */
        table.rincian {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 18px 0;
            font-size: 11pt;
        }

        table.rincian th,
        table.rincian td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        table.rincian th {
            background: #eee;
            font-size: 10pt;
            text-transform: uppercase;
        }

        table.rincian .baris-label { width: 32%; font-weight: bold; }

        /* ── Tanda tangan ─────────────────────────────── */
        .ttd {
            width: 100%;
            border-collapse: collapse;
            margin-top: 28px;
        }

        .ttd td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .ttd .jabatan { margin: 0 0 70px 0; }
        .ttd .nama { margin: 0; font-weight: bold; text-decoration: underline; }
        .ttd .nip { margin: 2px 0 0 0; font-size: 10pt; }

        .footer {
            margin-top: 28px;
            padding-top: 6px;
            border-top: 1px solid #999;
            font-size: 8pt;
            text-align: center;
            color: #444;
        }
    </style>
</head>

<body>

    <div class="kop">
        <table class="kop-table">
            <tr>
                @if ($logoData)
                    <td class="kop-logo"><img src="{{ $logoData }}" alt="Logo {{ $kop['institusi'] }}"></td>
                @endif
                <td class="kop-teks">
                    <p class="kop-yayasan">{{ $kop['yayasan'] }}</p>
                    <p class="kop-institusi">{{ $kop['institusi'] }}</p>
                    <p class="kop-alamat">{{ $kop['alamat'] }}</p>
                    <p class="kop-alamat">Website: {{ $kop['website'] }} &nbsp;|&nbsp; Telp: {{ $kop['telepon'] }}</p>
                </td>
                @if ($logoData)
                    <td class="kop-logo"></td>
                @endif
            </tr>
        </table>
    </div>

    <p class="tanggal">{{ $kota }}, {{ \Carbon\Carbon::parse($tanggalSurat)->translatedFormat('d F Y') }}</p>

    <table class="meta">
        <tr>
            <td class="label">Nomor</td>
            <td class="sep">:</td>
            <td>{{ $peminjaman->nomor_surat }}</td>
        </tr>
        <tr>
            <td class="label">Lampiran</td>
            <td class="sep">:</td>
            <td>-</td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td class="sep">:</td>
            <td><strong>Surat Izin Peminjaman Aset ({{ ucfirst($peminjaman->tipe) }})</strong></td>
        </tr>
    </table>

    <div class="tujuan">
        <p>Kepada Yth.</p>
        <p class="nama">{{ $penerima['jabatan'] }}</p>
        <p>{{ $penerima['nama'] }}</p>
        <p>di -</p>
        <p style="padding-left: 2em;">Tempat</p>
    </div>

    <p class="isi">Dengan hormat,</p>

    <p class="isi indent">
        Sehubungan dengan kebutuhan sarana penunjang kegiatan akademis/kemahasiswaan di lingkungan
        {{ $kop['institusi'] }}, dengan ini disampaikan bahwa permohonan peminjaman aset kampus berikut
        <strong>disetujui</strong> dengan rincian sebagai berikut:
    </p>

    <table class="rincian">
        <tr>
            <td class="baris-label">Nama Peminjam</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td class="baris-label">Email</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="baris-label">Aset yang Dipinjam</td>
            <td>
                {{ $asset->nama ?? $peminjaman->nama_item }}
                @if (! empty($asset?->kode))
                    ({{ $asset->kode }})
                @endif
            </td>
        </tr>
        @if ($peminjaman->tipe === 'ruangan')
            <tr>
                <td class="baris-label">Lokasi</td>
                <td>{{ $asset->lokasi ?? '-' }}</td>
            </tr>
            <tr>
                <td class="baris-label">Kapasitas</td>
                <td>{{ $asset->kapasitas ?? '-' }} orang</td>
            </tr>
        @else
            <tr>
                <td class="baris-label">Kategori</td>
                <td>{{ $asset->kategori ?? '-' }}</td>
            </tr>
            <tr>
                {{-- B-09c: sebelumnya tertulis "1 Unit" secara statis. --}}
                <td class="baris-label">Jumlah</td>
                <td>{{ $peminjaman->jumlah ?? 1 }} unit</td>
            </tr>
        @endif
        <tr>
            <td class="baris-label">Waktu Penggunaan</td>
            <td>
                {{ \Carbon\Carbon::parse($peminjaman->tanggal_mulai)->translatedFormat('d F Y') }}
                @if ($peminjaman->tanggal_mulai != $peminjaman->tanggal_selesai)
                    s/d {{ \Carbon\Carbon::parse($peminjaman->tanggal_selesai)->translatedFormat('d F Y') }}
                @endif
                <br>
                Pukul {{ \Illuminate\Support\Str::of($peminjaman->jam_mulai)->substr(0, 5) }} –
                {{ \Illuminate\Support\Str::of($peminjaman->jam_selesai)->substr(0, 5) }} {{ $zona }}
            </td>
        </tr>
        <tr>
            <td class="baris-label">Keperluan</td>
            <td>{{ $peminjaman->keterangan ?: '-' }}</td>
        </tr>
    </table>

    <p class="isi indent">
        Dengan diterbitkannya surat izin ini, peminjam menyatakan bersedia mematuhi seluruh peraturan dan
        tata tertib peminjaman aset di {{ $kop['institusi'] }}, serta bertanggung jawab penuh atas kebersihan,
        keamanan, dan pengembalian aset dalam kondisi baik dan tepat waktu.
    </p>

    <p class="isi indent">
        Demikian surat izin peminjaman ini dibuat untuk dipergunakan sebagaimana mestinya. Atas perhatian
        dan kerja samanya, kami ucapkan terima kasih.
    </p>

    <table class="ttd">
        <tr>
            <td>
                <p style="margin:0;">Mengetahui,</p>
                <p class="jabatan">{{ $pengelola['jabatan'] }}</p>
                <p class="nama">{{ $pengelola['nama'] }}</p>
                <p class="nip">NIP: {{ $pengelola['nip'] }}</p>
            </td>
            <td>
                <p style="margin:0;">Menyetujui,</p>
                <p class="jabatan">{{ $pejabat['jabatan'] }}</p>
                <p class="nama">{{ $pejabat['nama'] }}</p>
                <p class="nip">NIP: {{ $pejabat['nip'] }}</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        Sistem SiPinjam {{ $kop['institusi'] }} &bull; Dokumen diterbitkan secara elektronik
        &bull; Dicetak: {{ now()->translatedFormat('d/m/Y H:i') }} {{ $zona }}
    </div>

</body>

</html>
