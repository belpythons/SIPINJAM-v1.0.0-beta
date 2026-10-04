<!DOCTYPE html>
<html>
<head>
    <title>Kalender Akademik STITEK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body, html {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background-color: #ffffff;
        }
    </style>
</head>
<body class="flex items-center justify-center w-full h-full">
    <img src="{{ $imagePath }}" class="w-full h-full object-contain" />
</body>
</html>
