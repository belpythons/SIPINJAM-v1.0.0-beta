<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peminjaman Baru</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 8px 8px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 22px;">📋 Peminjaman Baru Masuk</h1>
    </div>

    <div style="background: #f9fafb; padding: 24px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">
        <p>Halo Admin,</p>
        <p>Ada pengajuan peminjaman baru yang membutuhkan persetujuan Anda:</p>

        <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
            <tr>
                <td style="padding: 8px 12px; background: #eef2ff; font-weight: bold; width: 140px;">ID Peminjaman</td>
                <td style="padding: 8px 12px; background: #eef2ff;">#{{ $peminjaman->id }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-weight: bold;">Peminjam</td>
                <td style="padding: 8px 12px;">{{ $peminjaman->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; background: #eef2ff; font-weight: bold;">Tipe</td>
                <td style="padding: 8px 12px; background: #eef2ff;">{{ ucfirst($peminjaman->tipe) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-weight: bold;">Item</td>
                <td style="padding: 8px 12px;">{{ $peminjaman->nama_item }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; background: #eef2ff; font-weight: bold;">Tanggal</td>
                <td style="padding: 8px 12px; background: #eef2ff;">
                    {{ $peminjaman->tanggal_mulai->format('d M Y') }} — {{ $peminjaman->tanggal_selesai->format('d M Y') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-weight: bold;">Jam</td>
                <td style="padding: 8px 12px;">{{ $peminjaman->jam_mulai }} — {{ $peminjaman->jam_selesai }}</td>
            </tr>
        </table>

        <p>Silakan login ke panel admin untuk menyetujui atau menolak peminjaman ini.</p>

        <p style="color: #6b7280; font-size: 13px; margin-top: 24px;">— SIPINJAM System</p>
    </div>
</body>
</html>
