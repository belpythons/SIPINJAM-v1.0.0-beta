# 🎓 SIPINJAM — Corporate Ready 🚀

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
</p>

> **Sistem Informasi Peminjaman Alat & Reservasi Ruang** terpadu yang dirancang dengan estetika *Corporate Clean Design* dan arsitektur backend berstandar *Enterprise*.

---

## 🔎 Overview Project

**SIPINJAM** bukan sekadar aplikasi CRUD biasa. Aplikasi ini berevolusi untuk menangani beban operasional kampus yang nyata. Menghadirkan navigasi UI/UX yang elegan, fungsionalitas kalender interaktif, pelaporan ekstensif (PDF & CSV), hingga sistem autentikasi tanpa kata sandi via Google (*Single Sign-On*).

### ✨ Fitur Unggulan

- 🎨 **Corporate Clean UI/UX:** Antarmuka responsif dan profesional menggunakan Tailwind CSS dengan navigasi *sticky-navbar*.
- 🛡️ **Pessimistic Locking & Service Pattern:** Tidak ada lagi *race condition* (bentrok jadwal). Bisnis logik dikelola aman di `BookingService`.
- 🤖 **SLA Auto-Reject 48 Jam:** Booking *pending* dibiarkan berhari-hari? Sistem akan membatalkannya secara otomatis mengamankan stok inventory!
- 🔐 **Socialite Google Login:** Integrasi login satu kali klik. Email baru akan otomatis terdaftar sebagai role `user`.
- 📊 **Dynamic Reports Engine:** Ekspor riwayat data peminjaman ke dalam format **PDF** dan **CSV/Excel** dengan satu klik.

---

## 🛠️ Tech Stack Application

| Bagian | Teknologi / Library |
|--------|----------------------|
| **Backend** | Laravel 11 (PHP 8.2+), Eloquent ORM |
| **Frontend** | Blade Templates, Tailwind CSS (Utility-First) |
| **Database** | MySQL 8.0 / MariaDB |
| **Auth** | Laravel Breeze + Laravel Socialite (Google OAuth) |
| **Reporting** | `barryvdh/laravel-dompdf` (PDF), Native UTF-8 (CSV) |
| **Queue & Scheduler** | Database Driver (No Redis needed) |

---

## 🚀 Step-by-Step Installation

Mari kita bangun lingkungan pengembangan Anda dengan cepat!

### 1. Clone & Build
```bash
# Clone repositori
git clone [https://github.com/your-username/SiPinjam.git](https://github.com/your-username/SiPinjam.git)
cd SiPinjam

# Install dependensi PHP & Node.js
composer install
npm install && npm run build

```

### 2. Environment Setup

```bash
# Gandakan environment file
cp .env.example .env

# Generate application key
php artisan key:generate

```

Buka file `.env` dan sesuaikan koneksi database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipinjam
DB_USERNAME=root
DB_PASSWORD=your_db_password

```

---

### 3. Konfigurasi Kredensial Pihak Ketiga (SANGAT PENTING!)

Agar fitur **Email Notification** dan **Login with Google** berjalan, Anda wajib mengisi bagian ini di `.env`.

#### A. Konfigurasi SMTP Email ✉️

Anda bisa menggunakan **Mailtrap** (untuk testing) atau **Gmail SMTP** (untuk rilis asli).

**Jika Menggunakan Gmail:**

1. Aktifkan *2-Step Verification* pada akun Google Anda.
2. Buat *App Password* (Sandi Aplikasi) di menu Keamanan Google.
3. Masukkan ke `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME="email.kampus.anda@gmail.com"
MAIL_PASSWORD="password_aplikasi_16_huruf_tanpa_spasi"
MAIL_ENCRYPTION=smtps
MAIL_FROM_ADDRESS="email.kampus.anda@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

```

#### B. Konfigurasi Google Client (OAuth 2.0) 🌐

Agar tombol *Login with Google* berfungsi:

1. Buka [Google Cloud Console](https://console.cloud.google.com/).
2. Buat proyek baru dan buka menu **APIs & Services > Credentials**.
3. Buat kredensial **OAuth Client ID** (Tipe: Web Application).
4. Tambahkan URI Pengalihan (Redirect URI): `http://localhost:8000/auth/google/callback`.
5. Salin *Client ID* dan *Client Secret* ke `.env`:

```env
GOOGLE_CLIENT_ID="paste_client_id_anda_disini.apps.googleusercontent.com"
GOOGLE_CLIENT_SECRET="paste_secret_anda_disini"
GOOGLE_REDIRECT_URI="http://localhost:8000/auth/google/callback"

```

---

### 4. Finalisasi Instalasi

Siapkan basis data, symlink foto, dan jalankan server!

```bash
# Link penyimpanan publik (untuk foto ruangan/barang)
php artisan storage:link

# Migrasi dan masukkan data asli (Hanya ada 1 Akun Admin yang tercipta)
php artisan migrate:fresh --seed

```

💡 **Akses Default Super Admin:**

* **Email:** `admin@sipinjam.ac.id`
* **Password:** `password123`
*(Akun user biasa dibuat otomatis saat mereka login menggunakan Google).*

### 5. Jalankan Aplikasi

Karena aplikasi ini menggunakan sistem antrean (Queue) untuk email dan penjadwalan otomatis, buka **3 terminal terpisah**:

```bash
# Terminal 1 — Menjalankan Server Web
php artisan serve

# Terminal 2 — Menjalankan Pekerja Antrean Email (Queue Worker)
php artisan queue:work

# Terminal 3 — Menjalankan Penjadwal Otomatis Pembatalan (SLA Auto-Reject)
php artisan schedule:work

```

Aplikasi kini siap diakses di: **http://localhost:8000** 🎉

---

## 🔒 Arsitektur Keamanan (Concurrency)

Kami memastikan data inventaris Anda aman. Setiap operasi kritis dibungkus dengan metode *Pessimistic Locking*:

```php
DB::transaction(function () {
    $barang = Barang::lockForUpdate()->findOrFail($id);
    // Transaksi dilanjutkan...
});

```

*Mencegah kejadian lucu di mana 1 buah proyektor sukses dipinjam oleh 2 mahasiswa di detik yang sama.*

---

## 📄 Lisensi

Dikembangkan dengan standar industri tinggi untuk infrastruktur manajemen kampus. Hak Cipta dilindungi.