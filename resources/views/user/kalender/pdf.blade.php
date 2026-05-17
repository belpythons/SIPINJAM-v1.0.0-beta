<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalender Akademik STITEK {{ $calendar->year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #fff;
        }
        .header {
            text-align: center;
            padding: 20px 30px 15px;
            border-bottom: 3px solid #000;
        }
        .header h1 {
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 12px;
            color: #555;
        }
        .calendar-image {
            padding: 20px 30px;
            text-align: center;
        }
        .calendar-image img {
            max-width: 100%;
            height: auto;
            border: 2px solid #000;
        }
        .footer {
            text-align: center;
            padding: 15px 30px;
            border-top: 3px solid #000;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Kalender Akademik STITEK</h1>
        <p>Tahun Akademik {{ $calendar->year }}</p>
    </div>

    <div class="calendar-image">
        {{-- Menggunakan Base64 agar DomPDF pasti bisa me-render gambar --}}
        <img src="{{ $base64Image }}" alt="Kalender Akademik {{ $calendar->year }}">
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh SIPINJAM — Sistem Informasi Peminjaman Ruangan dan Barang</p>
        <p>Dicetak pada: {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>
</body>
</html>
