<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalender Akademik {{ config('sipinjam.kop.institusi') }}</title>
    {{-- B-08: Tailwind Play CDN dihapus — hanya dipakai untuk dua kelas.
         PDF harus benar tanpa akses internet. --}}
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #ffffff;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <img src="{{ $imagePath }}" alt="Kalender akademik {{ $calendar->year ?? '' }}">
</body>
</html>
