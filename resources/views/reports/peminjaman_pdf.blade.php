<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman SiPinjam</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #ea580c; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #ea580c; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #666; }
        .stats { display: flex; margin-bottom: 20px; }
        .stat-box { flex: 1; text-align: center; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; margin: 0 4px; }
        .stat-box .value { font-size: 20px; font-weight: bold; }
        .stat-box .label { font-size: 9px; color: #666; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f8f9fa; color: #374151; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 10px; text-align: left; border-bottom: 2px solid #e5e7eb; }
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
        <h1>LAPORAN PEMINJAMAN</h1>
        <p>SiPinjam — Sistem Informasi Peminjaman Ruangan dan Barang</p>
        <p style="margin-top: 6px;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="text-align: center; border: 1px solid #e5e7eb; padding: 10px; border-radius: 4px;">
                <div style="font-size: 18px; font-weight: bold; color: #111;">{{ $stats['total'] }}</div>
                <div style="font-size: 9px; color: #666; text-transform: uppercase;">Total</div>
            </td>
            <td style="text-align: center; border: 1px solid #e5e7eb; padding: 10px;">
                <div style="font-size: 18px; font-weight: bold; color: #d97706;">{{ $stats['pending'] }}</div>
                <div style="font-size: 9px; color: #666; text-transform: uppercase;">Menunggu</div>
            </td>
            <td style="text-align: center; border: 1px solid #e5e7eb; padding: 10px;">
                <div style="font-size: 18px; font-weight: bold; color: #059669;">{{ $stats['approved'] }}</div>
                <div style="font-size: 9px; color: #666; text-transform: uppercase;">Disetujui</div>
            </td>
            <td style="text-align: center; border: 1px solid #e5e7eb; padding: 10px;">
                <div style="font-size: 18px; font-weight: bold; color: #dc2626;">{{ $stats['rejected'] }}</div>
                <div style="font-size: 9px; color: #666; text-transform: uppercase;">Ditolak</div>
            </td>
            <td style="text-align: center; border: 1px solid #e5e7eb; padding: 10px;">
                <div style="font-size: 18px; font-weight: bold; color: #6b7280;">{{ $stats['done'] }}</div>
                <div style="font-size: 9px; color: #666; text-transform: uppercase;">Selesai</div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Peminjam</th>
                <th>Tipe</th>
                <th>Item</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peminjamans as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->created_at?->format('d/m/Y') }}</td>
                    <td>{{ $p->user->name ?? '-' }}</td>
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
