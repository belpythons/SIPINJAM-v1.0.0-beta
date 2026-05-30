# **🎓 SIPINJAM 🚀**

**Sistem Informasi Peminjaman Alat & Reservasi Ruang Enterprise**

*Solusi terpadu untuk manajemen peminjaman aset institusi dengan antarmuka yang cepat, aman, dan real-time.*

## **1\. 🔎 Overview Project**

**SIPINJAM** dirancang untuk menangani beban operasional kampus atau instansi secara *real-time* tanpa perlu memuat ulang halaman (*page reload*). Sistem ini mencegah terjadinya *race condition* (bentrok peminjaman) dengan keamanan *database locking* tingkat tinggi, dan kini dilengkapi dengan notifikasi Multi-Channel (Email & WhatsApp).

### **✨ Fitur Unggulan**

* ⚡ **VILT Stack Architecture:** Navigasi super cepat ala SPA berkat Vue 3 & Inertia.js. Transisi antarmuka terasa sangat *smooth*.  
* 🛡️ **Pessimistic Locking & Service Pattern:** Mencegah 1 barang dipinjam oleh 2 orang di detik yang sama. Transaksi aman terkendali di dalam BookingService.  
* 🤖 **SLA Auto-Reject & Scheduler:** Booking *pending* dibiarkan berhari-hari? Sistem akan membatalkannya secara otomatis melalui *background job*.  
* 📲 **Omnichannel Notifications:** Terintegrasi dengan SMTP Gmail dan WhatsApp Gateway via **Fonnte** untuk notifikasi status peminjaman instan.  
* 🔐 **Socialite Google SSO:** Login satu kali klik. Akun baru dengan domain kampus/instansi akan otomatis terdaftar.  
* 📅 **Interactive Calendar:** Menggunakan FullCalendar Vue 3 untuk visualisasi jadwal ruangan yang intuitif.

## **2\. 🛠️ Tech Stack Application**

| Kategori | Teknologi Utama |
| :---- | :---- |
| **Backend Core** | Laravel 11 (PHP 8.2+), Eloquent ORM |
| **Frontend Core** | Vue 3 (Composition API), Inertia.js |
| **Styling & UI** | Tailwind CSS, Reka UI, Radix Vue, Lucide Icons |
| **Interactive Elements** | FullCalendar Vue 3, GSAP (Animations) |
| **Database** | MySQL 8.0 / MariaDB |
| **Authentication** | Laravel Breeze \+ Laravel Socialite (Google OAuth) |
| **Role & Permission** | Spatie Laravel Permission |
| **Reports & Export** | Barryvdh Laravel DOMPDF (PDF Reports) |
| **Integrations** | Mail (SMTP Gmail) & WhatsApp Gateway (Fonnte) |

## **3\. 🚀 Step-by-Step Installation**

Mari persiapkan lingkungan pengembangan Anda. Ikuti panduan ini secara berurutan.

### **Tahap 1: Clone & Install Dependencies**

\# 1\. Clone repositori ke mesin lokal Anda  
git clone https://github.com/your-username/SiPinjam.git  
cd SiPinjam

\# 2\. Install dependensi Backend (PHP)  
composer install

\# 3\. Install dependensi Frontend (Node.js)  
npm install

### **Tahap 2: Environment Setup**

Duplikat file konfigurasi *environment* dan hasilkan *app key*.

cp .env.example .env  
php artisan key:generate

Buka file .env di *code editor* Anda dan atur koneksi database:

DB\_CONNECTION=mysql  
DB\_HOST=127.0.0.1  
DB\_PORT=3306  
DB\_DATABASE=sipinjam  
DB\_USERNAME=root  
DB\_PASSWORD=password\_database\_anda

### **Tahap 3: Konfigurasi Kredensial (⚠️ KRITIKAL)**

**Penting:** Bagian ini wajib diisi agar fitur Email, Login Google, dan Notifikasi WhatsApp dapat berjalan sempurna.

#### **A. Konfigurasi SMTP Email (Gmail) ✉️**

Jangan gunakan password email biasa Anda. Gunakan **App Password** Google.

1. Aktifkan **2-Step Verification** pada akun Google Anda.  
2. Buka *Kelola Akun Google \> Keamanan \> Sandi Aplikasi (App Passwords)*.  
3. Buat sandi baru (Anda akan mendapat 16 digit huruf).  
4. Masukkan ke dalam .env:

MAIL\_MAILER=smtp  
MAIL\_HOST=smtp.gmail.com  
MAIL\_PORT=465  
MAIL\_USERNAME="email.kampus.anda@gmail.com"  
MAIL\_PASSWORD="kodenamabelasdigit" \# Tanpa spasi  
MAIL\_ENCRYPTION=smtps  
MAIL\_FROM\_ADDRESS="email.kampus.anda@gmail.com"  
MAIL\_FROM\_NAME="${APP\_NAME}"

#### **B. Konfigurasi Login with Google (OAuth 2.0) 🌐**

1. Buka [Google Cloud Console](https://console.cloud.google.com/).  
2. Buat proyek baru lalu buka **APIs & Services \> Credentials**.  
3. Buat kredensial **OAuth Client ID** (Tipe: Web Application).  
4. Tambahkan *Authorized redirect URIs*: http://localhost:8000/auth/google/callback  
5. Salin ID & Secret, lalu tambahkan di .env:

GOOGLE\_CLIENT\_ID="paste\_client\_id\_anda.apps.googleusercontent.com"  
GOOGLE\_CLIENT\_SECRET="paste\_client\_secret\_anda"  
GOOGLE\_REDIRECT\_URI="http://localhost:8000/auth/google/callback"

#### **C. Konfigurasi WhatsApp Gateway (Fonnte) 💬**

1. Daftar dan login ke akun [Fonnte](https://fonnte.com/).  
2. Hubungkan nomor WhatsApp (Bot) Anda di *dashboard* Fonnte dengan scan QR Code.  
3. Masuk ke menu **API/Token** dan salin Token yang diberikan.  
4. Tambahkan baris konfigurasi ini di .env:

FONNTE\_TOKEN="paste\_token\_fonnte\_anda\_disini"  
FONNTE\_URL="https://api.fonnte.com/send"

### **Tahap 4: Finalisasi Database & Storage**

Terapkan struktur database, symlink file, dan masukkan data *dummy/default*.

\# 1\. Link penyimpanan publik (wajib untuk render gambar aset)  
php artisan storage:link

\# 2\. Migrasi database dan jalankan Seeder  
php artisan migrate:fresh \--seed

💡 **Akses Default Super Admin:**

* **Email:** admin@sipinjam.ac.id *(Atau cek UserSeeder.php)*  
* **Password:** password

### **Tahap 5: Menjalankan Aplikasi 🚦**

Karena aplikasi ini modern dan memiliki fitur *background task*, Anda harus membuka **4 terminal terpisah** di dalam direktori project:

**Terminal 1 — Server Backend (Laravel)**

php artisan serve

**Terminal 2 — Server Frontend (Vite/Vue)**

npm run dev

**Terminal 3 — Background Jobs (Email & WA)**

php artisan queue:work

**Terminal 4 — Scheduler (Auto-Reject & SLA)**

php artisan schedule:work

🎉 **Selesai\!** Aplikasi kini dapat diakses melalui browser Anda di: http://localhost:8000
