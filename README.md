# 📋 SIPINJAM — Sistem Informasi Peminjaman Alat & Reservasi Ruang

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
</p>

---

## 🔎 Overview

**SIPINJAM** adalah aplikasi web untuk mengelola peminjaman alat inventaris dan reservasi ruangan di lingkungan kampus. Aplikasi ini dibangun dengan arsitektur yang aman dan siap production.

### ✨ Fitur Unggulan

| Fitur | Deskripsi |
|-------|-----------|
| **Pessimistic Locking** | Menggunakan `DB::transaction()` + `lockForUpdate()` untuk mencegah *race condition* saat 2 user booking di waktu bersamaan |
| **SLA Auto-Reject 48 Jam** | Peminjaman berstatus *pending* lebih dari 48 jam otomatis ditolak oleh scheduler. Stok barang dikembalikan secara aman |
| **Email Queue** | Notifikasi email dikirim via queue (`ShouldQueue`) sehingga tidak memblokir proses user |
| **Policy-Based Authorization** | User hanya bisa mengakses/membatalkan booking miliknya sendiri |
| **Service Layer Architecture** | Seluruh business logic terpusat di `BookingService`, controller super ramping |

---

## 🛠️ Tech Stack

- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** Blade Templates, Tailwind CSS, Alpine.js
- **Database:** MySQL 8.0
- **Queue:** Database Driver (siap pakai tanpa Redis)
- **Email:** Mailtrap (development) / SMTP (production)
- **PDF:** barryvdh/laravel-dompdf

---

## 🚀 Step-by-Step Installation

### 1. Clone Repository

```bash
git clone https://github.com/your-username/SiPinjam.git
cd SiPinjam
```

### 2. Install Dependencies

```bash
composer install
npm install && npm run build
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipinjam
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Konfigurasi Email (Mailtrap — Development)

Untuk testing email, daftar di [mailtrap.io](https://mailtrap.io) lalu isi kredensial:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@sipinjam.ac.id"
MAIL_FROM_NAME="SIPINJAM"
```

> **Fallback gratis:** Jika tidak ingin setup Mailtrap, cukup set `MAIL_MAILER=log`. Semua email akan tercatat di `storage/logs/laravel.log`.

### 5. Konfigurasi Queue

Pastikan queue driver menggunakan database (sudah default):

```dotenv
QUEUE_CONNECTION=database
```

### 6. Jalankan Migration & Seeder

```bash
php artisan migrate --seed
```

Ini akan membuat:
- **1 Super Admin:** `admin@sipinjam.ac.id` / `password123`
- **5 User Dummy:** `aisyah@sipinjam.ac.id`, `budi@sipinjam.ac.id`, dll (password: `password123`)
- **12 Ruangan** (Kampus Utama & Kampus Djuanda)
- **8 Barang Inventaris** (Smart TV, Mic, Kursi, dll)

### 7. Jalankan Aplikasi

Buka **3 terminal terpisah** dan jalankan:

```bash
# Terminal 1 — Web Server
php artisan serve

# Terminal 2 — Queue Worker (untuk proses email async)
php artisan queue:work

# Terminal 3 — Task Scheduler (untuk SLA auto-reject setiap jam)
php artisan schedule:work
```

Aplikasi tersedia di: **http://localhost:8000**

---

## 📁 Struktur Direktori Penting

```
app/
├── Console/Commands/
│   └── AutoRejectPendingBookings.php   # SLA 48 jam auto-reject
├── Http/
│   ├── Controllers/
│   │   ├── BookingController.php       # Controller ramping (delegasi ke service)
│   │   └── Admin/AdminController.php   # Approve/Reject via service
│   └── Requests/
│       └── StoreBookingRequest.php     # Validasi ketat (tanggal, exists)
├── Mail/
│   ├── BookingCreatedNotification.php  # Email ke Admin (booking baru)
│   └── BookingStatusUpdated.php        # Email ke User (status berubah)
├── Models/
│   ├── Peminjaman.php                  # Relasi ke User, Barang, Ruangan
│   ├── Barang.php
│   └── Ruangan.php
├── Policies/
│   └── BookingPolicy.php              # Otorisasi ketat per-user
└── Services/
    └── BookingService.php             # Business logic + pessimistic locking
```

---

## ⏰ SLA Auto-Reject

Peminjaman yang berstatus `menunggu` selama lebih dari **48 jam** akan otomatis ditolak oleh sistem. Proses ini:

1. Dijalankan setiap jam oleh Laravel Scheduler
2. Menggunakan pessimistic locking saat mengembalikan stok
3. Mengirim email notifikasi penolakan ke user

```bash
# Jalankan manual untuk testing:
php artisan booking:auto-reject
```

---

## 🔒 Keamanan Concurrency

Setiap operasi kritis (buat booking, approve, reject) dibungkus dalam:

```php
DB::transaction(function () {
    $barang = Barang::lockForUpdate()->findOrFail($id);
    // ... operasi aman dari race condition
});
```

Ini memastikan tidak ada 2 proses yang bisa mengurangi stok barang yang sama secara bersamaan.

---

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan internal kampus.
