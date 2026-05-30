# **SIPINJAM: Sistem Informasi Peminjaman Alat & Reservasi Ruang Enterprise**

Selamat datang di repositori **SIPINJAM**\! Sistem ini dirancang sebagai solusi terpadu untuk menangani manajemen peminjaman aset institusi atau kampus dengan antarmuka yang sangat cepat, aman, dan beroperasi secara *real-time*.

## **1\. Overview Project**

Aplikasi ini menargetkan efisiensi tinggi tanpa beban operasional, dan mencegah masalah yang sering terjadi seperti bentrok jadwal peminjaman.

**Fitur Unggulan:**

* **Arsitektur VILT Stack:** Menawarkan navigasi super mulus sekelas *Single Page Application* (SPA) tanpa perlu *reload* halaman.  
* **Pessimistic Locking & Service Pattern:** Mengamankan transaksi di dalam BookingService untuk memastikan satu barang tidak dapat diklaim oleh dua orang pada detik yang bersamaan.  
* **SLA Auto-Reject & Scheduler:** Terdapat fitur pekerjaan latar belakang (background job) yang akan otomatis membatalkan pesanan yang tertunda (pending) terlalu lama.  
* **Omnichannel Notifications:** Setiap perubahan status akan segera diinformasikan melalui Email (SMTP Gmail) dan pesan WhatsApp (Fonnte).  
* **Socialite Google SSO:** Cukup sekali klik untuk masuk ke aplikasi, di mana akun domain instansi akan otomatis didaftarkan.  
* **Interactive Calendar:** Pemantauan jadwal ruangan secara interaktif dengan antarmuka kalender yang intuitif.

## **2\. Tech Stack Application**

Proyek ini ditenagai oleh kombinasi ekosistem PHP dan JavaScript termodern:

**Bagian Backend (Core & Database):**

* **Bahasa Utama & Framework:** PHP 8.2+ dengan Laravel 11\.  
* **Database:** MySQL 8.0 / MariaDB yang dieksekusi dengan Eloquent ORM.  
* **Sistem Autentikasi:** Laravel Breeze, Spatie Laravel Permission, dan Laravel Socialite.  
* **Generator Laporan:** Barryvdh Laravel DOMPDF.

**Bagian Frontend (UI/UX):**

* **Framework UI:** Vue 3 (Composition API) yang dijahit dengan Inertia.js.  
* **Styling & Komponen:** Menggunakan Tailwind CSS, Reka UI, Radix Vue, dan Lucide Icons.  
* **Interaktivitas:** FullCalendar Vue 3 untuk komponen kalender, dan GSAP untuk rendering animasi yang mulus.

## **3\. Step-by-Step Installation**

Mari persiapkan lingkungan pengembangan agar aplikasi ini bisa berjalan lancar di perangkat lokal Anda. Lakukan langkah-langkah ini secara berurutan.

**Tahap 1: Kloning & Dependensi**

1. Unduh kode sumber ke lokal mesin Anda: git clone https://github.com/your-username/SiPinjam.git lalu cd SiPinjam.  
2. Instal pustaka backend PHP: composer install.  
3. Instal pustaka frontend JavaScript: npm install.

**Tahap 2: Manajemen Environment & Database**

1. Gandakan file env untuk konfigurasi sistem lokal: cp .env.example .env.  
2. Generate kunci enkripsi dari Laravel: php artisan key:generate.  
3. Sambungkan penyimpanan aset lokal ke ruang publik: php artisan storage:link.  
4. Bangun tabel-tabel di database sekaligus memasukkan data awalan (Seeder): php artisan migrate:fresh \--seed.

**Tahap 3: Menjalankan Multi-Service Terminal**

Karena SIPINJAM memiliki arsitektur modern yang memproses tugas di balik layar, buka 4 tab terminal Anda di folder proyek untuk menjalankan perintah berikut:

* Terminal 1 (Backend): php artisan serve  
* Terminal 2 (Frontend Vue): npm run dev  
* Terminal 3 (Worker Notifikasi): php artisan queue:work  
* Terminal 4 (Cron/Scheduler Status SLA): php artisan schedule:work

Aplikasi siap diakses di http://localhost:8000.

## **4\. Setup Environment Lengkap (Email, WA & Google SSO)**

**PERHATIAN KRITIKAL**: Fitur terpenting di SIPINJAM bergantung pada pihak ketiga. Buka file .env di *code editor* Anda dan perhatikan cara melengkapi variabel berikut ini:

### **Konfigurasi SMTP Email (Notifikasi Lewat Gmail)**

Sistem **tidak boleh** memakai *password* akun Gmail sehari-hari Anda karena rentan diblokir.

1. Pergi ke pengaturan akun Google Anda, dan pastikan fitur **Verifikasi 2 Langkah (2-Step Verification)** sudah menyala.  
2. Cari menu **Sandi Aplikasi (App Passwords)**.  
3. Buat konfigurasi baru, lalu Anda akan diberikan sandi unik berisikan 16 digit huruf.  
4. Sesuaikan blok email di file .env sebagai berikut:  
   MAIL\_MAILER=smtp  
   MAIL\_HOST=smtp.gmail.com  
   MAIL\_PORT=465  
   MAIL\_USERNAME=emailkampus@gmail.com  
   MAIL\_PASSWORD="paste\_16\_digit\_sandi\_tanpa\_spasi"  
   MAIL\_ENCRYPTION=smtps  
   MAIL\_FROM\_ADDRESS="emailkampus@gmail.com"  
   MAIL\_FROM\_NAME="Admin SIPINJAM"

### **Konfigurasi Google SSO (Masuk Dengan Sekali Klik)**

Agar pengguna (mahasiswa/staf) bisa langsung *login* tanpa registrasi manual.

1. Akses menu kredensial pada **Google Cloud Console**.  
2. Buat kredensial bertipe **OAuth Client ID** lalu pilih jenis **Web Application**.  
3. Pada isian **Authorized redirect URIs**, wajib masukkan: http://localhost:8000/auth/google/callback.  
4. Salin ID dan Secret tersebut ke dalam .env:  
   GOOGLE\_CLIENT\_ID=salin\_client\_id\_dari\_google\_cloud  
   GOOGLE\_CLIENT\_SECRET=salin\_client\_secret\_dari\_google\_cloud  
   GOOGLE\_REDIRECT\_URI="${APP\_URL}/auth/google/callback"

### **Konfigurasi WhatsApp Gateway (Notifikasi Real-Time Fonnte)**

Untuk memastikan status *approved* atau *rejected* langsung dikabarkan ke WhatsApp si pengguna.

1. Buat akun dan lakukan *login* di platform pihak ketiga **Fonnte**.  
2. Hubungkan nomor WhatsApp pusat (Bot) dengan melakukan *scan* QR Code di dalam *dashboard* Fonnte.  
3. Temukan dan salin kode dari menu **API/Token**.  
4. Sisipkan pada pengaturan .env:  
   FONNTE\_TOKEN=salin\_token\_fonnte\_anda\_kesini  
   VITE\_ADMIN\_WA\_NUMBER=isi\_dengan\_nomor\_wa\_administrator

   *(Catatan opsional dari rujukan aslinya, pastikan Anda juga menambahkan FONNTE\_URL="https://api.fonnte.com/send" jika diminta).*
