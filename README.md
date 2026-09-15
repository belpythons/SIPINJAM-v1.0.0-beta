# SiPinjam
### Sistem Informasi Peminjaman Ruangan & Barang Kampus

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-3.2-9575CD?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

---

## Overview

**SiPinjam** adalah platform peminjaman ruangan dan barang kampus (STITEK Bontang): sivitas mengajukan peminjaman, mengunduh surat izin resmi, dan admin mengelola aset, persetujuan, tata tertib, serta pelanggaran — semua lewat satu aplikasi web.

Dokumen ini mendeskripsikan **fitur yang benar-benar berjalan** di cabang rilis ini. Domain `Pengajuan` (alur 5 langkah, paket event, verifikasi WhatsApp OTP, persetujuan berjenjang) ada di dalam kode sebagai fondasi untuk pengembangan lanjutan, tetapi **belum tersambung ke antarmuka** — lihat `docs/PROMPT-RILIS.md` untuk konteks lengkap.

### Fitur yang Berjalan

*   **Kalender & Katalog Real-time** — FullCalendar menampilkan jadwal ruangan yang sudah disetujui; katalog barang menampilkan stok tersedia yang dihitung dari peminjaman aktif.
*   **Delayed Stock Deduction** — stok barang baru dikurangi saat admin menyetujui peminjaman (bukan saat pengajuan dibuat), sehingga pengajuan yang ditolak tidak pernah menyentuh stok.
*   **Deteksi Bentrok Jadwal** — pengajuan ruangan pada slot yang tumpang tindih dengan peminjaman lain yang masih menunggu/disetujui otomatis ditolak sistem.
*   **Surat Peminjaman PDF Self-Service** — begitu peminjaman diajukan, peminjam langsung bisa mengunduh surat izin resmi (kop, nomor surat, biodata, item, keperluan — semua dari data peminjaman) untuk ditandatangani, tanpa menunggu persetujuan admin.
*   **Auto-Reject 7 Hari** — pengajuan yang tak kunjung diproses/dikonfirmasi dalam 7 hari otomatis ditolak sistem lewat scheduler harian.
*   **Lapor Pelanggaran & Sanksi** — sivitas dapat melaporkan aset yang belum dikembalikan/rusak/ruangan berantakan atas sebuah peminjaman; admin meninjau dan memutuskan sanksi (bekukan akun N hari) yang otomatis pulih setelah masa berlakunya lewat.
*   **Tata Tertib Berversi** — admin menerbitkan versi tata tertib baru (append, riwayat revisi terjaga); user selalu melihat versi yang sedang berlaku.
*   **Admin CRUD** — kelola user, ruangan, barang, banner landing page, kalender akademik, dengan paginasi dan pemangkasan gambar otomatis ke rasio 4:3.
*   **Login Google Terbatas Domain** — hanya email institusi yang bisa masuk; tidak ada pendaftaran mandiri (akun dibuat admin atau via Google).

---

## Tech Stack

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend** | Laravel 11 (PHP 8.2+) | Routing, auth, business logic |
| **Frontend** | Vue 3 (Composition API) | UI reaktif |
| **Bridge** | Inertia.js | Laravel ↔ Vue tanpa REST API manual |
| **Styling** | Tailwind CSS & Lucide Icons | |
| **Hak Akses** | Spatie Laravel Permission | Role Admin & User |
| **Ekspor PDF** | Spatie Laravel PDF & Browsershot | Render HTML ke PDF via headless Chrome |
| **OAuth** | Laravel Socialite | Login Google domain kampus |

---

## Instalasi

```bash
git clone <url-repo-anda>
cd sipinjam-vilt-capstone

composer install
npm install

cp .env.example .env
php artisan key:generate

# Sesuaikan koneksi database di .env, lalu:
php artisan migrate:fresh --seed
php artisan storage:link
```

Jalankan dalam beberapa terminal terpisah:

```bash
php artisan serve      # web server
npm run dev            # asset compiler (mode pengembangan)
php artisan schedule:work   # scheduler: booking:auto-reject (jam-jaman), sanction:apply (harian)
```

---

## Environment (`.env`)

### Browsershot (Ekspor PDF)
Membutuhkan Node.js untuk merender PDF via headless Chrome. Bila jalur binary Anda tidak standar:
```env
NODE_BINARY_PATH="/usr/bin/node"
NPM_BINARY_PATH="/usr/bin/npm"
```

### Google Login (OAuth)
1. Buat project di [Google Cloud Console](https://console.cloud.google.com/welcome/new), konfigurasikan OAuth Consent Screen, buat OAuth 2.0 Client ID.
2. Daftarkan redirect URI: `http://localhost:8000/auth/google/callback`.
3. Isi:
```env
GOOGLE_CLIENT_ID="..."
GOOGLE_CLIENT_SECRET="..."
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### Kontak WhatsApp Admin
Tombol "Hubungi Admin" pada halaman login dan setelah pengajuan peminjaman adalah tautan `wa.me` statis — bukan integrasi gateway otomatis. Isi nomor tujuan:
```env
VITE_ADMIN_WA_NUMBER="628XXXXXXXXXX"
```

### Penandatangan Surat
Sebelum surat peminjaman dipakai secara resmi, isi identitas penandatangan (nama, NIP, jabatan) di `config/sipinjam.php` — nilai bawaan adalah placeholder dan sengaja diblokir dari produksi selama masih kosong.

---

## Akun Uji Coba

Setelah `php artisan migrate:fresh --seed`:

*   **Admin**: `admin@sipinjam.ac.id` / `password`
*   **Mahasiswa**: `mahasiswa@sipinjam.ac.id` / `password`
