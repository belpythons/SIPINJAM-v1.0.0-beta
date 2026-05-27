<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/logo.png">
    <title inertia>{{ config('app.name', 'SiPinjam') }}</title>
    <meta name="description" content="Sistem Peminjaman Aset STITEK Bontang - Platform peminjaman barang dan ruangan kampus yang modern dan efisien.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
