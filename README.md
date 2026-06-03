# 🎓 SiPinjam 🚀
### **Smart Item & Room Reservation Enterprise System**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-3.2-9575CD?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

---

## 🚀 Overview Project

**SiPinjam** adalah platform sistem informasi peminjaman ruangan dan barang kampus terpadu yang dirancang untuk mengotomatisasi seluruh alur peminjaman secara efisien, transparan, dan real-time. Platform ini didesain khusus untuk menyelesaikan masalah umum peminjaman aset kampus seperti konflik jadwal, transparansi stok, proses birokrasi surat izin yang lambat, hingga keterlambatan pengembalian barang.

### 🌟 Fitur Utama
*   📅 **Kalender Interaktif & Deteksi Konflik Jadwal (Real-time)**: Terintegrasi dengan *FullCalendar* untuk memvisualisasikan jadwal peminjaman aktif. Sistem secara otomatis memblokir pengajuan peminjaman ruangan jika terdapat bentrokan tanggal dan waktu pada slot yang sama.
*   ⏳ **Delayed Stock & Approval Validation**: Menjamin keakuratan data stok barang. Pengurangan stok barang hanya terjadi secara aktual saat Admin memberikan persetujuan (*Approved*), bukan saat peminjaman baru diajukan (*Pending*).
*   📄 **Auto-Generated PDF & Nomor Surat Otomatis**: Surat izin peminjaman resmi akan di-generate secara dinamis menggunakan *Spatie PDF / Browsershot* lengkap dengan nomor surat berurutan unik segera setelah peminjaman disetujui.
*   ⚖️ **Sistem Sanksi & Auto-Block Akun Otomatis**: Scheduler harian yang memvalidasi pengembalian barang secara otomatis setelah melewati tenggat waktu grace period 12 jam, lalu memblokir akun mahasiswa secara otomatis selama 30 hari jika terlambat mengembalikan barang atau meninggalkan ruangan dalam kondisi berantakan.
*   🖼️ **Penyelarasan Gambar &GD Image Crop**: Memastikan tampilan visual katalog ruangan dan barang seragam dengan pemangkasan otomatis ke aspek rasio 4:3 (800x600 px) serta mempertahankan transparansi gambar.

---

## 💻 Tech Stack Project

Sistem dibangun menggunakan **VILT Stack (Vue, Inertia, Laravel, Tailwind)** untuk performa Single Page Application (SPA) yang cepat dengan kenyamanan pengembangan backend Laravel yang andal.

| Komponen | Teknologi / Library Utama | Deskripsi |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 11 (PHP 8.2+) | Engine utama sistem, REST API, & Auth |
| **Frontend Framework** | Vue 3 (Composition API) | UI interaktif dan reaktif |
| **Bridge Layer** | Inertia.js (Vue 3) | Menghubungkan Backend Laravel dan Frontend Vue tanpa API manual |
| **Styling & UI** | Tailwind CSS & Lucide Icons | Antarmuka bergaya modern minimalis & responsif |
| **Animasi** | GSAP (GreenSock) | Transisi halaman login dan dashboard yang halus |
| **Manajemen Hak Akses**| Spatie Laravel Permission | Role-Based Access Control (Admin & User) |
| **Ekspor PDF** | Spatie Laravel PDF & Browsershot | Render HTML/CSS modern ke PDF via Headless Chrome |
| **OAuth / SSO** | Laravel Socialite | Integrasi login praktis menggunakan Akun Google mahasiswa |

---

## 🛠️ Step-by-Step Installation

Ikuti langkah-langkah di bawah ini untuk memasang dan menjalankan proyek SiPinjam di lingkungan lokal Anda:

### **Langkah 1: Clone Repositori dan Pasang Dependensi**
```bash
# Clone proyek ke lokal
git clone https://github.com/username/sipinjam-core.git
cd sipinjam-core

# Instal dependensi backend PHP (Composer)
composer install

# Instal dependensi frontend JS (NPM)
npm install
```

### **Langkah 2: Konfigurasi Environment & Kunci Aplikasi**
Salin berkas konfigurasi contoh `.env.example` menjadi `.env`, kemudian generate application key:
```bash
cp .env.example .env
php artisan key:generate
```

### **Langkah 3: Konfigurasi Database & Jalankan Seeder**
Sesuaikan konfigurasi database di berkas `.env` Anda, kemudian jalankan migrasi database beserta data dummy (seeder):
```bash
php artisan migrate:fresh --seed
```

### **Langkah 4: Hubungkan Storage Symlink**
Hubungkan direktori publik agar gambar ruangan/barang yang diunggah dapat diakses oleh browser:
```bash
php artisan storage:link
```

### **Langkah 5: Jalankan Server Pengembang**
Untuk menjalankan sistem secara penuh, buka beberapa tab terminal terpisah dan jalankan perintah berikut:

*   **Terminal 1: Laravel Web Server**
    ```bash
    php artisan serve
    ```
*   **Terminal 2: Vite Assets Compiler**
    ```bash
    npm run dev
    ```
*   **Terminal 3: Laravel Queue Worker (Mengirim Email & WA)**
    ```bash
    php artisan queue:work
    ```
*   **Terminal 4: Cron Scheduler (Pengecekan Sanksi Harian)**
    ```bash
    php artisan schedule:work
    ```

---

## ⚙️ Environment (`.env`) Setup & 3rd Party Integrations

Buka berkas `.env` di direktori root proyek untuk mengonfigurasi jalur binary internal serta integrasi pihak ketiga:

### **1. Konfigurasi Browsershot (Untuk Ekspor PDF)**
Spatie Browsershot membutuhkan Chrome/Chromium dan Node.js untuk merender halaman HTML ke PDF. Kami telah menyediakan fallback otomatis yang andal berdasarkan sistem operasi Anda.
*   **Linux (Fallback Global)**: Sistem secara otomatis mendeteksi `/usr/bin/node` dan `/usr/bin/npm`.
*   **Windows (Fallback Laragon/XAMPP)**: Menggunakan path default program files.
Jika path Node.js Anda berbeda, definisikan secara manual pada baris berikut:
```env
NODE_BINARY_PATH="/usr/bin/node"
NPM_BINARY_PATH="/usr/bin/npm"
```

---

### **2. Integrasi Layanan Pihak Ketiga**

#### **A. SMTP Gmail (Notifikasi Email)**
Sistem menggunakan email untuk mengirimkan link reset password serta notifikasi peminjaman.
1. Aktifkan **2-Step Verification** pada Akun Google Anda.
2. Buat password aplikasi pihak ketiga baru (16 digit kode rahasia) melalui [Google App Passwords](https://myaccount.google.com/apppasswords).
3. Salin kode tersebut ke variabel berikut:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME="email-anda@gmail.com"
MAIL_PASSWORD="enam-belas-digit-app-password"
MAIL_ENCRYPTION=smtps
MAIL_FROM_ADDRESS="email-anda@gmail.com"
MAIL_FROM_NAME="Admin SIPINJAM"
```

#### **B. Google Login (OAuth / SSO)**
Untuk mengizinkan pengguna masuk secara otomatis menggunakan akun Google kampus mereka (`@stitek.ac.id`):
1. Masuk ke [Google Cloud Console](https://console.cloud.google.com/welcome/new).
2. Buat project baru, konfigurasikan OAuth Consent Screen, lalu buat kredensial baru berupa **OAuth 2.0 Client ID**.
3. Daftarkan callback redirect URI: `http://localhost:8000/auth/google/callback`.
4. Salin Client ID dan Client Secret yang didapatkan ke baris berikut:
```env
GOOGLE_CLIENT_ID="kredensial-client-id-google.apps.googleusercontent.com"
GOOGLE_CLIENT_SECRET="kredensial-client-secret-rahasia"
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

#### **C. WhatsApp Gateway (Fonnte)**
Sistem menggunakan WhatsApp Gateway Fonnte untuk mengirim notifikasi penalti/blokir akun kepada pengguna secara langsung.
1. Masuk/Daftar akun Fonnte di [Fonnte Dashboard](https://md.fonnte.com/new/login.php).
2. Dapatkan API token perangkat Anda dari dashboard Fonnte.
3. Konfigurasikan pada variabel berikut:
```env
FONNTE_TOKEN="token-fonnte-aktif-anda"
VITE_ADMIN_WA_NUMBER="628XXXXXXXXXX" # Nomor WhatsApp Admin untuk pengaduan/tanya jawab
```

---

### **🔑 Akun Default Login Uji Coba**
Setelah menjalankan migrasi dan seeder (`php artisan migrate:fresh --seed`), Anda dapat masuk menggunakan akun bawaan berikut:

*   **Akun Administrator (Admin)**
    *   **Email**: `admin@sipinjam.ac.id`
    *   **Password**: `password`
*   **Akun Mahasiswa (User)**
    *   **Email**: `mahasiswa@sipinjam.ac.id` (atau menggunakan NIM seeder acak seperti `101230001@stitek.ac.id`)
    *   **Password**: `password`
