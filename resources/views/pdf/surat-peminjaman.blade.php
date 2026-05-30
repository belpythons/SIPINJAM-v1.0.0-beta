<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Peminjaman - {{ $peminjaman->nomor_surat }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            padding: 20px 40px;
        }

        /* Kop Surat */
        .kop-surat {
            text-align: center;
            border-bottom: 4px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .kop-surat .institusi {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .kop-surat .sub-institusi {
            font-size: 10pt;
            margin-top: 2px;
        }
        .kop-surat .alamat {
            font-size: 9pt;
            color: #333;
            margin-top: 4px;
        }
        .kop-surat .tagline {
            font-size: 8pt;
            font-style: italic;
            color: #555;
            margin-top: 2px;
        }

        /* Judul Surat */
        .judul-surat {
            text-align: center;
            margin: 20px 0;
        }
        .judul-surat h2 {
            font-size: 14pt;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 1px;
        }
        .judul-surat .nomor {
            font-size: 11pt;
            margin-top: 4px;
        }

        /* Content */
        .content {
            margin: 20px 0;
        }
        .content p {
            text-align: justify;
            margin-bottom: 10px;
            text-indent: 40px;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 6px 10px;
            vertical-align: top;
            font-size: 11pt;
        }
        .data-table .label {
            width: 180px;
            font-weight: bold;
        }
        .data-table .separator {
            width: 15px;
            text-align: center;
        }

        /* Detail Aset Table */
        .aset-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
            border: 2px solid #000;
        }
        .aset-table th,
        .aset-table td {
            border: 1px solid #000;
            padding: 8px 12px;
            font-size: 11pt;
            text-align: left;
        }
        .aset-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
        }

        /* Signature */
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-left,
        .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signature-name {
            margin-top: 80px;
            font-weight: bold;
            text-decoration: underline;
        }
        .signature-nip {
            font-size: 10pt;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 20px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 8pt;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <!-- Kop Surat -->
    <div class="kop-surat">
        <div class="institusi">Sekolah Tinggi Teknologi (STITEK) Bontang</div>
        <div class="sub-institusi">Yayasan Pendidikan Pupuk Kaltim</div>
        <div class="alamat">
            Jl. Brigjend Katamso No.40, Bontang Utara, Kota Bontang, Kalimantan Timur 75313
        </div>
        <div class="tagline">The Knowledgeable and Virtue Campus</div>
    </div>

    <!-- Judul Surat -->
    <div class="judul-surat">
        <h2>Surat Peminjaman Aset Kampus</h2>
        <div class="nomor">Nomor: {{ $peminjaman->nomor_surat }}</div>
    </div>

    <!-- Isi Surat -->
    <div class="content">
        <p>
            Yang bertanda tangan di bawah ini, Biro Administrasi Sekolah Tinggi Teknologi (STITEK) Bontang,
            dengan ini menerangkan bahwa peminjaman aset kampus berikut telah <strong>DISETUJUI</strong>
            dan dapat digunakan sesuai dengan ketentuan yang berlaku:
        </p>
    </div>

    <!-- Data Peminjam -->
    <table class="data-table">
        <tr>
            <td class="label">Nama Peminjam</td>
            <td class="separator">:</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td class="separator">:</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Peminjaman</td>
            <td class="separator">:</td>
            <td>{{ ucfirst($peminjaman->tipe) }}</td>
        </tr>
        <tr>
            <td class="label">Keperluan</td>
            <td class="separator">:</td>
            <td>{{ $peminjaman->keterangan }}</td>
        </tr>
    </table>

    <!-- Detail Aset -->
    <table class="aset-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Aset</th>
                <th>Nama {{ ucfirst($peminjaman->tipe) }}</th>
                @if($peminjaman->tipe === 'ruangan')
                    <th>Lokasi</th>
                    <th>Kapasitas</th>
                @else
                    <th>Kategori</th>
                @endif
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $asset->kode ?? '-' }}</td>
                <td>{{ $asset->nama ?? $peminjaman->nama_item }}</td>
                @if($peminjaman->tipe === 'ruangan')
                    <td>{{ $asset->lokasi ?? '-' }}</td>
                    <td>{{ $asset->kapasitas ?? '-' }} Orang</td>
                @else
                    <td>{{ $asset->kategori ?? '-' }}</td>
                @endif
            </tr>
        </tbody>
    </table>

    <!-- Jadwal Peminjaman -->
    <table class="data-table">
        <tr>
            <td class="label">Tanggal Mulai</td>
            <td class="separator">:</td>
            <td>{{ $peminjaman->tanggal_mulai->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Selesai</td>
            <td class="separator">:</td>
            <td>{{ $peminjaman->tanggal_selesai->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Jam Penggunaan</td>
            <td class="separator">:</td>
            <td>{{ $peminjaman->jam_mulai }} — {{ $peminjaman->jam_selesai }} WITA</td>
        </tr>
        <tr>
            <td class="label">Tanggal Persetujuan</td>
            <td class="separator">:</td>
            <td>{{ $peminjaman->approved_at ? $peminjaman->approved_at->format('d F Y, H:i') . ' WITA' : '-' }}</td>
        </tr>
    </table>

    <!-- Ketentuan -->
    <div class="content" style="margin-top: 20px;">
        <p>
            Dengan dikeluarkannya surat ini, peminjam <strong>wajib mematuhi</strong> seluruh tata tertib peminjaman
            aset kampus STITEK Bontang. Segala bentuk kerusakan, kehilangan, atau pelanggaran
            ketentuan menjadi tanggung jawab penuh peminjam.
        </p>
        <p>
            Demikian surat peminjaman ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
        </p>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="signature-left">
            <p>Peminjam,</p>
            <p class="signature-name">{{ $user->name }}</p>
            <p class="signature-nip">{{ $user->email }}</p>
        </div>
        <div class="signature-right">
            <p>Bontang, {{ now()->format('d F Y') }}</p>
            <p>Biro Administrasi,</p>
            <p class="signature-name">Administrator SIPINJAM</p>
            <p class="signature-nip">STITEK Bontang</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Dicetak secara otomatis oleh sistem SiPinjam — STITEK Bontang &bull;
        {{ now()->format('d/m/Y H:i') }} WITA &bull;
        Dokumen ini sah tanpa tanda tangan basah.
    </div>
</body>
</html>
