# Prompt Eksekusi — Rilis SiPinjam (Lingkup Nyata)

**Tanggal**: 15 September 2026
**Basis**: hasil audit langsung + dua audit mendalam (frontend & backend) atas working tree `main`.
**Tujuan dokumen**: mengubah temuan menjadi rangkaian **prompt eksekusi siap-pakai**. Tiap tugas mencantumkan **file yang harus dibaca** (agar eksekutor tidak menyapu seluruh repo) + kriteria selesai.

> Dokumen ini **berbeda** dari `RENCANA-PENYEMPURNAAN.md` / `PROMPT-EKSEKUSI.md`. Kedua dokumen itu adalah rencana **enterprise 9-fase (±50–70 hari)** yang jauh melebihi kebutuhan yang Anda deskripsikan. Dokumen ini hanya mengerjakan **yang Anda minta**, di atas sistem yang **sudah berjalan**.

---

## 0. Basis keputusan (baca dulu sebelum eksekusi)

### Kondisi faktual
- **Sistem LAMA `Peminjaman`/`BookingController` = live & berfungsi penuh**: browse aset → `BookingModal` (3 langkah) → `POST /bookings` → admin setujui/tolak/selesai → unduh PDF surat (kop dari config, offline, tergerbang setelah approve). Locking pesimistik, delayed stock, cek bentrok saat create **dan** approve — semua benar.
- **Sistem BARU `Pengajuan` = domain core bagus + unit-tested, TAPI tidak tersambung**: `PengajuanStateMachine`, `AvailabilityService`, `EligibilityService`, `NomorSuratService` hanya dipakai test/seeder. Satu-satunya pintu HTTP (`PengajuanController`) **rusak**: tidak di-import di `routes/web.php` (500), `ketersediaan()` pakai `$validated` sebelum didefinisikan, `$user->role` pada kolom yang sudah dihapus, redirect ke route `pengajuan.index` yang tidak ada. `Pages/Pengajuan/Create.vue` **stub tanpa `<template>`** + impor `vue-router`/`uuid`/komponen yang tidak terpasang.
- **Seluruh kerja P0/P1 masih UNCOMMITTED** di `main` (59 untracked + 74 modified + 11 deleted). Belum terversion.

### Peta kebutuhan Anda → status
| # | Yang Anda minta | Status sekarang | Ditangani di |
| :- | :--- | :--- | :--- |
| 1 | Visibilitas jadwal + ketersediaan aset (kalender) | ✅ Ada (FullCalendar di Dashboard & Landing) | — (polish opsional) |
| 2 | Admin kelola user/barang/ruangan | ✅ Ada (CRUD berfungsi) | R6 (paginasi) |
| 2b | Admin kelola **tata tertib** (tambah/revisi) | 🟡 Statis; model `TataTertibVersion` ada tanpa UI | **R5** |
| 3 | Isi biodata + item + keperluan → **PDF surat berkop** | ✅ Ada (config-driven, offline) | R2 (sesuaikan gerbang) |
| 4 | **Himbauan pasca-download** (hubungi admin stlh ttd & pengembalian) | 🔴 Tidak ada | **R2** |
| 5 | **Auto-reject bila tak dilapor dalam 1 minggu** | 🟡 Ada tapi 48 jam & logika beda | **R3** |
| 6 | **Pelaporan pelanggaran oleh sivitas** (belum kembali/rusak/ruangan berantakan) | 🔴 Tidak ada (menu "Laporan" = statistik riwayat) | **R4** |
| 7 | **Admin memutuskan sanksi** (bekukan akun N hari) dari laporan | 🔴 Tidak ada UI | **R4** |
| 8 | Peminjam mhs/dosen/staf, email kampus, tanpa registrasi | ✅ Ada (domain login ditegakkan, admin-provisioned) | — |

### Rekomendasi arah (ponytail: jalur terpendek yang benar)
**Rilis di atas sistem LAMA `Peminjaman`.** Ia sudah memenuhi 5 dari 8 kebutuhan Anda hari ini. Tiga sisanya (himbauan, 1-minggu, pelaporan+sanksi) adalah **tambahan kecil** ke sistem lama. Menyelesaikan sistem BARU `Pengajuan` = mengerjakan P2–P8 (paket event, WhatsApp OTP, persetujuan berjenjang, PWA, dsb.) — semua **di luar** yang Anda deskripsikan. Sistem BARU cukup **diparkir** (inert, tidak dihapus) sebagai bahan masa depan.

### Keputusan yang mengunci plan (jawab bila tak setuju default)
| Kode | Keputusan | Default yang dipakai plan ini |
| :- | :--- | :--- |
| **D1** | Basis rilis: sistem lama vs selesaikan Pengajuan | **Sistem lama** `Peminjaman` |
| **D2** | Kapan PDF surat bisa diunduh | **Segera setelah user submit** (self-service, sesuai alur Anda "download → lalu hubungi admin"). Saat ini tergerbang setelah approve — akan dilonggarkan di R2 |
| **D3** | Timer "1 minggu" berlaku pada keadaan apa | **Peminjaman `pending`/menunggu update TTD** yang tak diproses 7 hari → otomatis **ditolak** |
| **D4** | Tabel pelaporan | **Pakai ulang** `pelanggaran` + `sanksi` (sudah ada dari P1) bila cocok; kalau overkill, tabel ramping `laporan` baru |
| **D5** | Mobile/PWA | **Di luar lingkup** rilis ini (tidak Anda minta) |

---

## Prompt Eksekusi

> Jalankan berurutan **R0 → R6**. Tiap prompt bisa dipakai sebagai instruksi tunggal ke sesi/agen. "Baca dulu" = satu-satunya file yang perlu dibuka untuk tugas itu.

---

### R0 — Amankan kerja & kunci basis

```
Tujuan: kerja P0/P1 saat ini belum ter-commit. Amankan dulu sebagai checkpoint
sebelum perubahan apa pun, dan tetapkan sistem lama Peminjaman sebagai basis rilis
(D1).

Baca dulu:
- (tidak perlu baca file; ini operasi git)

Lakukan:
1. Buat branch checkpoint: `git switch -c release/base-peminjaman`.
2. `git add -A && git commit -m "checkpoint: kerja P0/P1 (domain Pengajuan diparkir, rilis di atas sistem Peminjaman lama)"`.
3. Verifikasi tidak ada file penting yang ter-ignore keliru: `git status` bersih.

Selesai bila: `git log` menunjukkan satu commit checkpoint dan working tree bersih.
```

---

### R1 — Netralkan sistem baru yang rusak dari jalur rilis (agar bootable & build lolos)

```
Tujuan: `Pages/Pengajuan/Create.vue` mengimpor paket yang tidak terpasang
(vue-router, uuid) dan komponen yang tidak ada; karena `resources/js/app.js`
memuat semua halaman via `import.meta.glob(..., { eager: true })`, file ini
berisiko menggagalkan `npm run build`. Route /pengajuan juga 500 (controller tak
di-import). Semua ini di luar lingkup rilis — nonaktifkan, JANGAN selesaikan.

Baca dulu:
- resources/js/app.js               (konfirmasi pola eager glob)
- routes/web.php                    (baris 36–40: blok route /pengajuan)
- resources/js/Pages/Pengajuan/Create.vue   (konfirmasi stub)
- resources/js/Pages/Home.vue       (boilerplate mati, tak ada route)

Lakukan:
1. Hapus blok route `/pengajuan` (create & store) dari routes/web.php.
2. Hapus resources/js/Pages/Pengajuan/Create.vue dan folder Pengajuan/ bila kosong.
3. Hapus resources/js/Pages/Home.vue (dead scaffolding).
4. JANGAN hapus app/Services/*, app/Models/Pengajuan*.php, migrasi, atau test —
   biarkan diparkir (inert, tidak dirujuk route mana pun).

Selesai bila:
- `npm run build` sukses.
- `php artisan route:list` bersih tanpa referensi PengajuanController.
- Tidak ada tautan UI ke /pengajuan.
```

---

### R2 — Alur PDF surat self-service + himbauan pasca-download

```
Tujuan: sesuai alur Anda, user submit → langsung bisa unduh PDF surat → muncul
himbauan "tandatangani lalu hubungi admin; bila tak dilaporkan dalam 1 minggu,
peminjaman otomatis ditolak". Saat ini unduh PDF tergerbang setelah admin approve
(BookingController.php ~baris 82–89) dan tidak ada himbauan.

Baca dulu:
- app/Http/Controllers/BookingController.php   (store, generatePDF, gerbang status)
- app/Models/Peminjaman.php                    (konstanta STATUS_*)
- app/Services/BookingService.php              (createBooking: status awal & nomor surat)
- resources/js/Components/BookingModal.vue     (aksi setelah submit sukses)
- resources/js/Pages/User/RiwayatPeminjaman.vue (tombol "Download Surat")
- resources/views/pdf/surat-peminjaman.blade.php (pastikan biodata+item+keperluan tercetak)
- app/Services/NomorSuratService.php           (penerbitan nomor bila diunduh saat pending)

Lakukan (D2):
1. Longgarkan gerbang generatePDF: izinkan pemilik mengunduh saat status pending/
   menunggu (bukan hanya approved). Tetap tolak milik user lain (403).
   Putuskan penomoran: nomor surat "permohonan" boleh terbit saat submit, atau
   pakai penanda "DRAF" hingga approve — pilih satu, konsisten dengan blade.
2. Setelah submit sukses di BookingModal (atau di halaman RiwayatPeminjaman),
   tampilkan modal/toast himbauan berisi: (a) unduh surat, (b) langkah tanda
   tangan, (c) instruksi hubungi admin setelah ttd & setelah pengembalian untuk
   update status, (d) peringatan auto-reject 1 minggu, (e) tombol "Hubungi Admin"
   (wa.me pakai VITE_ADMIN_WA_NUMBER yang sudah ada di .env.example).
3. Pastikan blade surat mencetak: nama peminjam (biodata), daftar item + jumlah,
   keperluan (keterangan), tanggal/jam — dari data peminjaman, bukan nilai statis.

Selesai bila: user baru bisa unduh surat berkop lengkap tepat setelah submit, dan
himbauan muncul dengan tombol kontak admin.
```

---

### R3 — Aturan auto-reject 1 minggu

```
Tujuan: peminjaman yang statusnya tidak diperbarui (belum lapor validasi TTD/
pengembalian) dalam 1 minggu → otomatis DITOLAK. Saat ini AutoRejectPendingBookings
memakai SLA 48 jam, dan ApplySanctionPenalties hanya menandai (grace 12 jam) +
membebaskan blokir kedaluwarsa.

Baca dulu:
- app/Console/Commands/AutoRejectPendingBookings.php  (SLA 48 jam hardcoded)
- app/Console/Commands/ApplySanctionPenalties.php     (GRACE_JAM=12, alur tandai/bebaskan)
- routes/console.php                                  (jadwal booking:auto-reject / sanction:apply)
- app/Models/Peminjaman.php                           (STATUS_*, kolom waktu & alasan_sistem)

Lakukan (D3):
1. Ubah ambang di AutoRejectPendingBookings dari 48 jam → 7 hari (7*24), atau
   tambahkan konfigurasi `config('sipinjam.auto_reject_hari', 7)`.
2. Pastikan alasan ditulis ke `alasan_sistem` (bukan menimpa `keterangan` pemohon).
3. Selaraskan pesan/himbauan R2 dengan durasi ini (satu sumber angka).
4. Pastikan jadwal di routes/console.php aktif (harian cukup untuk aturan 7 hari).

Selesai bila: peminjaman yang menggantung >7 hari otomatis ditolak dengan alasan
sistem, terverifikasi lewat test perintah (Peminjaman dibuat created_at 8 hari lalu
→ setelah command → status REJECTED).
```

---

### R4 — Pelaporan pelanggaran oleh sivitas + keputusan sanksi admin (FITUR BARU INTI)

```
Tujuan: setiap user email kampus dapat melapor pelanggaran (barang belum kembali /
rusak-cacat / ruangan berantakan). Laporan masuk ke modul admin; admin memutuskan
sanksi = bekukan akun pelaku selama N hari. Ini fitur yang PALING tidak ada.

Baca dulu:
- app/Models/User.php                              (isBlocked(), is_blocked, blocked_until, blocked_reason)
- app/Http/Middleware/CheckUserBlocked.php         (cara blokir ditegakkan)
- app/Http/Requests/Auth/LoginRequest.php          (gerbang blokir saat login)
- app/Http/Controllers/Admin/AdminController.php   (pola CRUD + cara set blokir user)
- app/Models/Pelanggaran.php  app/Models/Sanksi.php (kandidat pakai-ulang, D4)
- database/migrations/2026_09_07_000002_create_pemeriksaan_dan_sanksi_tables.php (skema pelanggaran/sanksi)
- routes/web.php                                   (tempat daftar route baru)
- resources/js/Pages/Admin/KelolaUser.vue          (template UI tabel+modal admin)
- resources/js/Components/Sidebar.vue  resources/js/Components/AdminSidebar.vue (tambah menu)

Lakukan (D4 — putuskan pakai-ulang `pelanggaran`/`sanksi` atau tabel `laporan` ramping):
1. Backend user:
   - Route + controller `LaporanPelanggaranController@create/store` (middleware
     auth+blocked). Field: target (peminjaman_id ATAU ruangan_id/barang_id lepas),
     jenis (belum_kembali|rusak|ruangan_berantakan), deskripsi, foto opsional
     (pakai ImageService yang sudah ada), pelapor = Auth::id().
   - Simpan sebagai record `pelanggaran` (status_tindak_lanjut='menunggu') atau
     tabel `laporan` baru. JANGAN menebak pelaku otomatis — pelaku ditetapkan admin
     dari data peminjaman terkait (hindari bug lama laporBerantakan/B-05).
2. Frontend user: halaman/`modal` "Lapor Pelanggaran" + menu sidebar user.
3. Backend admin:
   - Route + controller daftar laporan masuk + aksi "Putuskan Sanksi": input jumlah
     hari → set user.blocked_until = now()+N hari, is_blocked=true, blocked_reason,
     tautkan ke laporan (buat record `sanksi` bila pakai-ulang). Gunakan SATU jalur
     blokir yang konsisten (isBlocked() menghormati blocked_until).
4. Frontend admin: halaman "Kelola Laporan/Sanksi" (kloning pola KelolaUser.vue) +
   menu AdminSidebar.

Selesai bila:
- User A melapor → laporan tampil di modul admin.
- Admin memutuskan freeze 7 hari untuk pelaku → pelaku tak bisa login/booking
  hingga blocked_until lewat, lalu otomatis pulih (ApplySanctionPenalties sudah
  membebaskan blokir kedaluwarsa).
- Ada satu test alur: lapor → sanksi → user terblokir → lewat tanggal → pulih.
```

---

### R5 — Manajemen Tata Tertib (admin tambah/revisi)

```
Tujuan: admin dapat menambah/merevisi tata tertib; halaman user menampilkan versi
berlaku. Saat ini User/TataTertib.vue = teks hardcoded; model TataTertibVersion ada
tanpa UI/CRUD.

Baca dulu:
- app/Http/Controllers/TataTertibController.php    (render statis sekarang)
- app/Models/TataTertibVersion.php                 (kolom: versi, konten, berlaku_sejak, dibuat_oleh)
- database/migrations/2026_09_07_000002_* dan _000003_* (cari tabel tata_tertib_versions; grep bila perlu)
- resources/js/Pages/User/TataTertib.vue           (ganti sumber jadi prop dari controller)
- resources/js/Pages/Admin/KelolaKalender.vue      (template CRUD sederhana admin)
- routes/web.php                                   (tambah route admin.tata_tertib.*)

Lakukan:
1. TataTertibController@index: kirim TataTertibVersion terbaru (berlaku_sejak<=now,
   urut desc) sebagai prop ke User/TataTertib.vue; hapus teks hardcoded.
2. Route + method admin: index/store (buat versi baru), set-aktif/berlaku. Cukup
   "buat versi baru" (append) daripada edit in-place — jejak revisi terjaga.
3. Halaman Admin/KelolaTataTertib.vue (kloning KelolaKalender.vue) + menu AdminSidebar.

Selesai bila: admin membuat versi baru tata tertib → user melihat isi terbaru tanpa
deploy ulang.
```

---

### R6 — Pengerasan rilis minimal & kejujuran README

```
Tujuan: rapikan footgun produksi dan samakan README dengan realitas (README kini
menjanjikan WhatsApp/Fonnte, PWA, banyak dokumen — yang TIDAK dibangun di lingkup ini).

Baca dulu:
- README.md                                        (klaim fitur berlebih)
- .env.example  config/app.php                     (B-10: APP_TIMEZONE vs Asia/Makassar/WITA)
- app/Http/Controllers/Admin/AdminController.php   (kelolaBarang/kelolaRuangan pakai ->get(): B-20)
- resources/js/Pages/Admin/KelolaBarang.vue  KelolaRuangan.vue (dukung paginasi)
- resources/js/Pages/Auth/Login.vue                (kredensial uji ter-hardcode → hapus)
- routes/web.php                                   (T-10: rate-limit login/unduh/ekspor)
- resources/js/Components/AdminSidebar.vue  Sidebar.vue (tautkan /laporan & /admin/laporan yang yatim, atau hapus)

Lakukan:
1. Paginasi KelolaBarang/KelolaRuangan (backend ->paginate + Pagination.vue yang ada).
2. Set APP_TIMEZONE konsisten (Asia/Makassar) agar jam surat (WITA) benar.
3. Hapus prefilled credential di Login.vue.
4. Tambah rate-limit pada login, unduh PDF, ekspor.
5. Tautkan atau hapus dua halaman Laporan yatim dari nav.
6. Tulis ulang README: hapus janji WhatsApp/PWA/multi-dokumen; deskripsikan hanya
   fitur yang benar-benar ada. Isi placeholder penandatangan di config sebelum PDF
   dipakai resmi ([ISI NAMA PEJABAT], dst.).
7. Jalankan `php artisan test` — semua hijau (buang/skip test yang menyasar fitur
   yang diparkir bila perlu).

Selesai bila: test hijau, README jujur, tidak ada kredensial uji terkirim, jam surat
benar, daftar admin terpaginasi.
```

---

## Urutan & catatan

- **Jalur kritis**: R0 → R1 (bootable) → R2/R3 (alur inti) → R4 (fitur besar) → R5 → R6.
- **R4 paling berat** (± beberapa hari); sisanya tugas kecil-menengah.
- **Diparkir sengaja** (bukan dihapus): seluruh domain `Pengajuan` + `Availability/Eligibility/StateMachine` + migrasi/test terkait. Bila kelak butuh paket event/persetujuan berjenjang, lanjutkan dari `RENCANA-PENYEMPURNAAN.md` P3.
- **Di luar lingkup rilis ini** (tidak Anda minta): PWA/mobile, WhatsApp OTP, transparansi publik, BAST/serah terima, QR verifikasi.
```
