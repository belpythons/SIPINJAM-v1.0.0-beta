<!DOCTYPE html>
<html>
<head>
    <title>Bukti Peminjaman #{{ $booking->id }}</title>
    <style>
        body { font-family: sans-serif; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .content { margin-top: 20px; }
        .status-badge { background: #22c55e; color: white; padding: 5px 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SIPINJAM - BUKTI PEMINJAMAN</h2>
        <p>Gedung Teknik Informasi, Universitas</p>
    </div>
    <div class="content">
        <p><strong>Nama Peminjam:</strong> {{ $booking->user->name }}</p>
        <p><strong>Item yang Dipinjam:</strong> {{ $booking->nama_item }} ({{ ucfirst($booking->tipe) }})</p>
        <p><strong>Waktu:</strong> {{ $booking->tanggal_mulai }} s/d {{ $booking->tanggal_selesai }}</p>
        <p><strong>Jam:</strong> {{ $booking->jam_mulai }} - {{ $booking->jam_selesai }} WIB</p>
        <p><strong>Keterangan:</strong> {{ $booking->keterangan }}</p>
        <p><strong>Status:</strong> <span class="status-badge">{{ ucfirst($booking->status) }}</span></p>
    </div>
</body>
</html>