# 🏛️ SIPINJAM — Sistem Peminjaman Sarana & Prasarana Kampus
### Versi: `v1.0.0-beta` (Open Beta Release — Ready to Launch)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-3.2-9575CD?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
[![Vite](https://img.shields.io/badge/Vite-8.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![PWA Ready](https://img.shields.io/badge/PWA-Level%203%20Offline-0284c7?style=for-the-badge&logo=pwa&logoColor=white)](https://web.dev/progressive-web-apps/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

---

## 🌟 Tentang Proyek & Status Rilis

**SIPINJAM** adalah platform terintegrasi untuk pengelolaan dan peminjaman sarana prasarana (ruangan dan barang inventaris) kampus **STITEK Bontang**. Aplikasi ini menjembatani mahasiswa, dosen, dan staf pengelola aset kampus melalui sistem berbasis web modern yang transparan, bebas konflik jadwal, dan mendukung pengoperasian aplikasi progresif (*Progressive Web App*).

Aplikasi saat ini berada pada tahap **Ready to Release (Open Beta)** dengan seluruh fungsionalitas inti, keamanan hak akses, validasi stok, cetak surat izin PDF otomatis, edukasi tata tertib, dan kapabilitas offline PWA yang telah teruji **100% lulus (212 automated tests / 925 assertions)**.

---

## 🚀 Fitur Unggulan Sistem

### 1. 📱 Dukungan PWA (Progressive Web App) Terpasang
- **Level 1 (Installable Native App)**: Dapat di-install langsung di Android, iOS, Windows, dan macOS dengan ikon adaptif (*maskable icon*) dan tampilan *fullscreen standalone*.
- **Level 2 (Offline Fallback & Asset Caching)**: Service Worker Workbox meng-cache seluruh bundle aset (JS, CSS, Font Inter, Logo) dan menyediakan halaman fallback ramah pengguna saat koneksi terputus.
- **Level 3 (Offline Read)**: Halaman statis seperti **Tata Tertib**, **Kalender Akademik**, dan **Katalog Sarpras** tetap dapat dibaca secara lokal tanpa jaringan internet.
- **Auto-Update Toast & Install Banner**: Pengguna mendapatkan notifikasi pembaruan instan dan ajakan pasang aplikasi di beranda perangkat.

### 2. 🗓️ Kalender Interaktif & Deteksi Bentrok Real-Time
- **FullCalendar Interaktif**: Tampilan jadwal peminjaman ruangan dan barang yang disetujui secara real-time pada Landing Page dan Dashboard.
- **Drag-to-Book**: Pengunjung dapat memilih rentang tanggal langsung pada kalender beranda untuk melanjutkan pengajuan peminjaman otomatis.
- **Pencegahan Double-Booking**: Sistem menolak secara otomatis setiap pengajuan pada ruangan atau slot waktu yang bertabrakan.

### 3. 📦 Manajemen Stok Tertunda (*Delayed Stock Deduction*)
- Stok barang inventaris baru dikurangi ketika Admin menyetujui peminjaman (bukan saat draf dibuat), memastikan stok fisik tetap akurat dan tidak terkunci oleh pengajuan yang dibatalkan/ditolak.

### 4. 📄 Dokumen Izin Resmi & PDF Self-Service
- **Cetak Surat Izin Otomatis**: Mahasiswa dapat langsung mengunduh Surat Peminjaman PDF ber-kop resmi, bernomor surat otomatis, dan berformat standar institusi setelah mengajukan.
- **Pengamanan Kop & Tanda Tangan**: Guard otomatis memastikan placeholder konfigurasi telah terisi sebelum surat resmi diterbitkan.

### 5. ⚖️ Modul Tata Tertib Resmi & Sanksi Akademik
- **7 Modul Tata Tertib Statis**: Pedoman operasional (07:00 - 22:00 WITA), hak & kewajiban, tata cara peminjaman, serta matriks sanksi ganti rugi dan pembekuan akun.
- **Edukasi Tata Tertib Proaktif**: Modal dialog saat pertama kali mengakses dashboard, banner himbauan, serta checklist persetujuan pada formulir peminjaman.
- **Lapor Pelanggaran & Pemulihan Akun**: Pengguna dapat melaporkan aset rusak atau ruangan kotor; admin dapat memberikan sanksi pembekuan akun yang otomatis pulih setelah masa sanksi berakhir.

### 6. 🎨 Desain Sistem & Konsistensi UI/UX
- **Tipografi Inter Self-Hosted**: Menggunakan font Inter lokal `@fontsource/inter` (tanpa ketergantungan CDN eksternal, ramah PWA).
- **Tema Visual Berdasarkan Peran**:
  - **User**: Biru Primer (`#2563eb`) dengan logo watermark terisolasi di navigasi menu.
  - **Admin**: Oranye / Amber (`#ea580c`) dengan indikator panel pengelola sarpras.
- **Real-Time Live Clock**: Penunjuk waktu WITA real-time pada hero banner User dan Admin.

---

## 🛠️ Arsitektur Teknologi

| Lapisan | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 12 (PHP 8.2+) | Routing, Controller, Policy, Database, & Task Scheduling |
| **Frontend Framework** | Vue 3 (Composition API) | Komponen reaktif, Script Setup, & State Management |
| **Monolith Bridge** | Inertia.js 3.2 | Menghubungkan Laravel & Vue secara seamless tanpa REST boilerplate |
| **Build Tool & PWA** | Vite 8 + `vite-plugin-pwa` | HMR super cepat, Workbox Service Worker, & Manifest Generator |
| **Desain & UI** | Tailwind CSS + Shadcn / Radix Vue | Sistem token CSS HSL, Lucide Icons, dan Card responsif |
| **Hak Akses & Role** | Spatie Laravel Permission | Pemisahan hak akses ketat antara `admin` dan `mahasiswa` |
| **Ekspor PDF** | Spatie Browsershot & Puppeteer | Render HTML/Blade ke PDF berstandar cetak presisi tinggi |
| **Otentikasi Google** | Laravel Socialite | Login SSO dengan validasi domain institusi `@stitek.ac.id` |

---

## 💻 Panduan Instalasi & Menjalankan Lokal

### 1. Kloning Repositori & Pasang Dependensi
```bash
git clone https://github.com/username/sipinjam-vilt-capstone.git
cd sipinjam-vilt-capstone

# Pasang dependensi PHP dan Node.js
composer install
npm install
```

### 2. Konfigurasi Lingkungan (`.env`)
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Migrasi & Seeding Database Bersih
```bash
# Jalankan migrasi dan seeding data tetapan (ruangan, barang, banner, tata tertib)
php artisan migrate:fresh --seed
php artisan storage:link
```

### 4. Menjalankan Server Pengembangan
Jalankan perintah berikut pada terminal terpisah:
```bash
# Terminal 1: Web Server Laravel
php artisan serve

# Terminal 2: Vite Dev Server & HMR
npm run dev

# Terminal 3: Scheduler Background (Auto-reject SLA 7 hari & Sanksi)
php artisan schedule:work
```

---

## 🔑 Akun Default Sistem

Setelah menjalankan `php artisan migrate:fresh --seed`, akun awal yang tersedia:

| Peran | Email Akun | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin Utama** | `admin@sipinjam.test` | `password` | Akses penuh Panel Pengelola Sarpras |
| **Admin Belva** | `belvapranamasriwibowo@gmail.com` | `belva123` | Akses penuh Panel Pengelola Sarpras |
| **Mahasiswa** | `user@sipinjam.test` | `password` | Akses peminjaman sarpras & riwayat |

---

## ⚙️ Konfigurasi Layanan Eksternal (`.env`)

### 1. Google OAuth (Login SSO)
```env
GOOGLE_CLIENT_ID="isi_client_id_dari_google_cloud"
GOOGLE_CLIENT_SECRET="isi_client_secret_dari_google_cloud"
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
SIPINJAM_ALLOWED_EMAIL_DOMAINS="stitek.ac.id"
SIPINJAM_AUTO_PROVISION_GOOGLE_USERS=true
```

### 2. WhatsApp Admin
```env
VITE_ADMIN_WA_NUMBER="6281234567890"
```

### 3. Email Notifikasi SMTP
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME="emailkampus@gmail.com"
MAIL_PASSWORD="16_digit_google_app_password"
MAIL_ENCRYPTION=smtps
MAIL_FROM_ADDRESS="emailkampus@gmail.com"
MAIL_FROM_NAME="Admin SIPINJAM"
```

---

## 🧪 Pengujian & Kualitas Kode

Proyek ini telah dilengkapi rangkaian pengujian otomatis (*Automated Feature & Unit Testing*):

```bash
# Menjalankan seluruh rangkaian tes backend
php artisan test

# Melakukan kompilasi bundle frontend dan PWA Service Worker
npm run build
```

**Hasil Pengujian:**
- **Backend**: `212 passed` (925 assertions)
- **Frontend**: Vite PWA precache 106 entries, 0 compilation error.

---

## 📂 Dokumentasi Teknis Tambahan

Dokumentasi rinci mengenai modul-modul sistem tersimpan pada folder [`docs/`](file:///d:/KOD%20ING/sipinjam-vilt-capstone/docs):
- [Design System Guide](file:///d:/KOD%20ING/sipinjam-vilt-capstone/docs/Design%20System%20Guide%20.md) — Panduan token warna, tipografi, dan komponen.
- [PWA Integration Plan](file:///d:/KOD%20ING/sipinjam-vilt-capstone/docs/PLAN_PWA_INTEGRATION.md) — Rencana arsitektur PWA.
- [PWA Testing Report](file:///d:/KOD%20ING/sipinjam-vilt-capstone/docs/DOKUMENTASI-PWA-IMPLEMENTASI-DAN-TESTING.md) — Laporan pengujian PWA & Service Worker.
- [Tata Tertib Resmi](file:///d:/KOD%20ING/sipinjam-vilt-capstone/docs/dokumentasi_desain_isi_dan_reason_tata_tertib.md) — Klausul tata tertib dan matriks sanksi.

---

© 2026 **STITEK Bontang** — SIPINJAM Development Team.
