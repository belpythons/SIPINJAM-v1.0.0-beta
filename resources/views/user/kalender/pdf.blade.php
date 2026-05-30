<!DOCTYPE html>
<html>
<head>
    <title>Kalender Akademik STITEK</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            text-align: center;
        }
        .header {
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #111;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #666;
        }
        .calendar-image {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Kalender Akademik Tahun {{ $calendar->year }}</h1>
        <p>Sekolah Tinggi Teknologi Bontang (STITEK)</p>
    </div>
    <div class="content">
        <img src="{{ $imagePath }}" class="calendar-image" />
    </div>
</body>
</html>
