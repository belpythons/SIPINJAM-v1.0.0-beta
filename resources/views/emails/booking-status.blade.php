<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Peminjaman</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    @php
        $isApproved = $statusBaru === 'disetujui';
        $gradient = $isApproved
            ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)'
            : 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
        $icon = $isApproved ? '✅' : '❌';
        $label = $isApproved ? 'Disetujui' : 'Ditolak';
    @endphp

    <div style="background: {{ $gradient }}; padding: 20px; border-radius: 8px 8px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 22px;">{{ $icon }} Peminjaman {{ $label }}</h1>
    </div>

    <div style="background: #f9fafb; padding: 24px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">
        <p>Halo <strong>{{ $peminjaman->user->name ?? 'User' }}</strong>,</p>

        @if($isApproved)
            <p>Peminjaman Anda telah <strong style="color: #059669;">disetujui</strong>. Silakan gunakan fasilitas sesuai jadwal yang telah ditentukan.</p>
        @else
            <p>Maaf, peminjaman Anda telah <strong style="color: #dc2626;">ditolak</strong>. Silakan ajukan ulang atau hubungi admin untuk informasi lebih lanjut.</p>
        @endif

        <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
            <tr>
                <td style="padding: 8px 12px; background: #eef2ff; font-weight: bold; width: 140px;">ID Peminjaman</td>
                <td style="padding: 8px 12px; background: #eef2ff;">#{{ $peminjaman->id }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-weight: bold;">Tipe</td>
                <td style="padding: 8px 12px;">{{ ucfirst($peminjaman->tipe) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; background: #eef2ff; font-weight: bold;">Item</td>
                <td style="padding: 8px 12px; background: #eef2ff;">{{ $peminjaman->nama_item }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-weight: bold;">Tanggal</td>
                <td style="padding: 8px 12px;">
                    {{ $peminjaman->tanggal_mulai->format('d M Y') }} — {{ $peminjaman->tanggal_selesai->format('d M Y') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; background: #eef2ff; font-weight: bold;">Status</td>
                <td style="padding: 8px 12px; background: #eef2ff;">
                    <strong style="color: {{ $isApproved ? '#059669' : '#dc2626' }};">{{ $label }}</strong>
                </td>
            </tr>
        </table>

        <p style="color: #6b7280; font-size: 13px; margin-top: 24px;">— SIPINJAM System</p>
    </div>
</body>
</html>
