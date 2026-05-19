<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Peminjaman - {{ $user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #3b82f6; margin-bottom: 20px; }
        .header h1 { font-size: 16px; color: #1e3a8a; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #666; }
        .user-info { margin-bottom: 15px; padding: 10px; background: #eff6ff; border-radius: 6px; }
        .user-info strong { color: #1e3a8a; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #eff6ff; color: #1e3a8a; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 10px; text-align: left; border-bottom: 2px solid #bfdbfe; }
        td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        tr:nth-child(even) { background: #fafafa; }
        .status { padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .status-menunggu { background: #fef3c7; color: #92400e; }
        .status-sedang_dipinjam { background: #d1fae5; color: #065f46; }
        .status-ditolak { background: #fee2e2; color: #991b1b; }
        .status-selesai { background: #f3f4f6; color: #374151; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RIWAYAT PEMINJAMAN</h1>
        <p>SiPinjam — Sistem Informasi Peminjaman Ruangan dan Barang</p>
    </div>

    <div class="user-info">
        <strong>Nama:</strong> {{ $user->name }} &nbsp;|&nbsp;
        <strong>Email:</strong> {{ $user->email }} &nbsp;|&nbsp;
        <strong>Total Peminjaman:</strong> {{ $peminjamans->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Item</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peminjamans as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->created_at?->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($p->tipe ?? '-') }}</td>
                    <td>
                        @if($p->tipe === 'ruangan')
                            {{ $p->ruangan->nama ?? $p->nama_item ?? '-' }}
                        @else
                            {{ $p->barang->nama ?? $p->nama_item ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $p->tanggal_mulai?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $p->tanggal_selesai?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($p->keterangan ?? '-', 40) }}</td>
                    <td>
                        <span class="status status-{{ $p->status }}">
                            {{ ucfirst(str_replace('_', ' ', $p->status ?? '-')) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada {{ now()->format('d M Y, H:i') }} — SiPinjam &copy; 2026</p>
    </div>
</body>
</html>
