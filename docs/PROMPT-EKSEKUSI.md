# Prompt Eksekusi — SiPinjam menuju Rilis Produksi

**Pendamping dari**: [`RENCANA-PENYEMPURNAAN.md`](./RENCANA-PENYEMPURNAAN.md)
**Versi**: 1.0 · 6 September 2026
**Branch kerja**: `claude/campus-asset-lending-app-7vlx9u`

---

## Cara Memakai Dokumen Ini

Setiap fase di bawah adalah **satu prompt siap salin-tempel** ke Claude Code (atau agen AI lain). Aturan pemakaian:

1. **Jalankan berurutan.** P1 bergantung pada P0, P3 pada P1, dan seterusnya. Ketergantungan ditulis di setiap header.
2. **Satu fase = satu sesi = satu commit besar (atau beberapa commit kecil).** Jangan menggabung dua fase dalam satu sesi — konteksnya terlalu besar dan hasilnya menurun.
3. **Selalu sertakan blok "Konteks Wajib Dibaca"** — jangan dipotong; itulah yang mencegah agen menebak-nebak.
4. **Verifikasi dengan Kriteria Selesai** sebelum lanjut ke fase berikutnya. Bila ada yang merah, perbaiki di fase yang sama.
5. **Jangan izinkan agen mengubah lingkup.** Bila menemukan masalah di luar fase, minta dicatat di `docs/TEMUAN-TAMBAHAN.md`, bukan langsung dikerjakan.

### Blok Konteks Standar (sertakan di setiap prompt)

```
KONTEKS PROYEK
Proyek : SiPinjam — sistem peminjaman ruangan & barang kampus (STITEK Bontang)
Stack  : Laravel 11 (PHP 8.2+), Vue 3 Composition API, Inertia 3, Tailwind 3,
         Spatie Permission, Spatie Laravel PDF (Browsershot), FullCalendar, GSAP
Branch : claude/campus-asset-lending-app-7vlx9u
Bahasa : Seluruh teks antarmuka, pesan validasi, notifikasi, dan komentar kode
         yang menghadap pengguna memakai Bahasa Indonesia. Nama variabel/kelas
         memakai bahasa Inggris kecuali istilah domain yang sudah mapan
         (pengajuan, peminjaman, ruangan, barang, serah terima, pelanggaran, sanksi).
Prinsip: Aplikasi adalah SISTEM PENCATATAN + PENERBIT DOKUMEN, bukan pengambil
         keputusan. SOP kertas kampus tetap berjalan; aplikasi menyiapkan surat
         untuk ditandatangani, mencatat keputusan, dan mengekspos jejaknya.

BACA DULU (wajib, jangan dilewati):
- docs/RENCANA-PENYEMPURNAAN.md  (seluruhnya)
- Design System Guide .md
- README.md

ATURAN KERJA:
- Jangan mengubah lingkup di luar fase ini. Temuan lain → catat di docs/TEMUAN-TAMBAHAN.md.
- Setiap perubahan skema DB lewat migrasi baru; jangan mengedit migrasi lama yang sudah ter-commit.
- Setiap fitur baru wajib disertai test (Feature test minimal, Unit test untuk service).
- Jangan memakai env() di luar berkas config/ (harus aman terhadap `php artisan config:cache`).
- Jangan menambah dependency baru tanpa menyebutkan alasannya di ringkasan akhir.
- Jalankan `php artisan test` dan `npm run build` sebelum menyatakan selesai.
- JANGAN menjalankan perintah git apa pun (commit, branch, checkout, push, stash).
  Selesaikan pekerjaan sampai berkas berubah, lalu laporkan ringkasan perubahan
  beserta daftar berkas yang tersentuh dan alasan singkat tiap perubahan.
  Version control ditangani manual oleh pemilik repo.
```

---

## P0 — Stabilisasi & Perbaikan Blocker

**Ketergantungan**: —  ·  **Estimasi**: 3–5 hari  ·  **Tujuan**: aplikasi yang ada tidak error dan aman dijadikan fondasi.

```
[Sertakan Blok Konteks Standar di sini]

TUGAS: Fase P0 — Stabilisasi & Perbaikan Blocker.
Acuan: docs/RENCANA-PENYEMPURNAAN.md §8 (Temuan Bug & Risiko Produksi).

Kerjakan HANYA perbaikan berikut. Jangan menambah fitur baru.

1. B-01 — Rute /laporan error 500
   ReportController::userIndex() mengembalikan view('user.laporan') yang berkasnya
   tidak ada. Ubah userIndex() dan adminIndex() menjadi Inertia::render
   ('User/Laporan' dan 'Admin/Laporan') agar konsisten dengan seluruh aplikasi,
   lalu buat kedua halaman Vue-nya. Pertahankan filter tanggal & status yang ada.
   Blade resources/views/admin/laporan.blade.php beserta partial navbar/sidebar
   admin yang tidak lagi terpakai: hapus.

2. B-02 — Auto-reject membabi buta saat stok habis
   Di BookingService::approveBooking(), blok "AUTO-REJECT jika stok habis" menolak
   SEMUA pending untuk barang tersebut tanpa memeriksa rentang tanggal. Batasi agar
   hanya menolak pengajuan yang rentang waktunya BERIRISAN dengan pengajuan yang
   baru disetujui. Bila tidak beririsan, biarkan tetap menunggu.

3. B-03 — Bentrok ruangan tidak dicek saat approve
   Pindahkan assertNoScheduleConflict() sehingga juga dijalankan di dalam
   approveBooking() (dengan mengecualikan baris yang sedang disetujui).
   Tambahkan test: dua booking pending pada slot sama, approve pertama sukses,
   approve kedua harus gagal dengan pesan yang jelas.

4. B-06 — Keterangan pengguna tertimpa
   AutoRejectPendingBookings dan auto-reject stok menimpa kolom `keterangan`.
   Tambahkan migrasi kolom `alasan_sistem` (text, nullable) pada peminjamans dan
   tulis alasan pembatalan ke sana. Jangan pernah menimpa `keterangan`.

5. B-07 — Login Google menerima email apa pun
   Tambahkan config/sipinjam.php dengan kunci `allowed_email_domains`
   (array, default: ['stitek.ac.id']) dan `auto_provision_google_users` (bool).
   Di SocialiteController: tolak email di luar domain yang diizinkan dengan pesan
   Indonesia yang jelas, dan jangan buat akun baru bila auto_provision = false.
   Tambahkan test untuk email yang ditolak dan yang diterima.

6. B-08 + B-09 — Template surat PDF
   a. Hapus <script src="https://cdn.tailwindcss.com"> dan <link> Google Fonts dari
      berkas berikut (sudah diverifikasi mengandungnya):
        resources/views/pdf/surat-peminjaman.blade.php
        resources/views/user/kalender/pdf.blade.php
        resources/views/admin/laporan.blade.php  (dihapus pada butir 1)
      Periksa juga resources/views/reports/*. Ganti dengan CSS yang di-inline di
      dalam berkas (cukup CSS biasa, tidak perlu Tailwind).
      PDF harus benar tanpa akses internet.
   b. Pindahkan seluruh identitas surat ke config/sipinjam.php:
      kop.yayasan, kop.institusi, kop.alamat, kop.website, kop.telepon, kop.logo_path,
      penandatangan.pejabat.{nama,jabatan,nip},
      penandatangan.pengelola_aset.{nama,jabatan,nip},
      surat.format_nomor, surat.kota.
      Hapus nama & NIP fiktif yang di-hardcode ("Aisyah Rahmawati, S.Kom.",
      "NIP: 2024090123") dan label "Ketua Panitia"/"Sekretaris Panitia".
      Isi config dengan placeholder eksplisit: "[ISI NAMA PEJABAT]" dst.
   c. Perbaiki "Jumlah: 1 Unit" yang statis agar memakai $peminjaman->jumlah.

7. B-10 — .env.example
   Kosongkan APP_KEY (APP_KEY=), set APP_TIMEZONE=Asia/Makassar,
   APP_LOCALE=id, APP_FALLBACK_LOCALE=id, APP_FAKER_LOCALE=id_ID,
   APP_NAME=SiPinjam. Tambahkan kunci baru yang dipakai config/sipinjam.php.

8. B-13 — Deteksi binary Node terduplikasi
   Buat App\Services\PdfRenderer dengan satu metode publik untuk merender view
   menjadi PDF, memuat logika deteksi node/npm dan noSandbox() di satu tempat.
   Baca path dari config('sipinjam.pdf.*'), bukan env(). Pakai di
   BookingController, ReportController, CalendarController.

9. B-15 — Query bentrok tidak bisa memakai indeks
   Tambahkan migrasi kolom `mulai_at` dan `selesai_at` (datetime, nullable, index
   komposit) pada peminjamans, isi dari kombinasi tanggal+jam yang ada lewat
   migrasi data, lalu ubah assertNoScheduleConflict() memakai kedua kolom itu
   (mulai_at < :req_end AND selesai_at > :req_start). Hapus percabangan
   sqlite/mysql. Kolom lama tetap ada untuk kompatibilitas — akan dibereskan di P1.

10. B-20 — Tidak ada paginasi
    Ubah AdminController::kelolaPeminjaman(), kelolaUser(), dan
    BookingController::index() memakai ->paginate(20)->withQueryString().
    Buat komponen Pagination.vue yang sesuai design system dan pakai di ketiga
    halaman. Tambahkan pencarian sederhana (nama/kode) pada daftar admin.

11. Kualitas dasar
    - Self-host font Inter: pasang @fontsource/inter, impor di resources/js/app.js,
      hapus @import Google Fonts dari resources/css/app.css.
    - Tambahkan .github/workflows/ci.yml: matrix PHP 8.2/8.3, composer install,
      npm ci, npm run build, php artisan test, laravel/pint --test.
    - Tambahkan laravel/pint sebagai dev dependency dan berkas pint.json standar.

KRITERIA SELESAI (verifikasi satu per satu, tunjukkan buktinya):
[ ] `php artisan test` hijau, dengan test baru untuk B-02, B-03, B-07
[ ] `npm run build` sukses
[ ] `php artisan config:cache && php artisan route:cache` tanpa error
[ ] Rute /laporan dan /admin/laporan membuka halaman Inertia, bukan 500
[ ] `grep -rn "cdn.tailwindcss.com\|fonts.googleapis.com" resources/` = 0 hasil
[ ] `grep -rn "Aisyah Rahmawati\|2024090123" .` = 0 hasil
[ ] `grep -rn "env(" app/` = 0 hasil
[ ] PDF surat ter-render benar dengan koneksi internet dimatikan
[ ] Daftar admin ter-paginasi dan bisa dicari

LAPORKAN di akhir: tabel ringkas "temuan → berkas → perubahan → test yang menutupi".
```

---

## P1 — Fondasi Data & Domain

**Ketergantungan**: P0  ·  **Estimasi**: 7–10 hari  ·  **Tujuan**: model data yang mampu menampung paket event, pemeriksaan, pelanggaran, dan jejak audit.

```
[Sertakan Blok Konteks Standar di sini]

TUGAS: Fase P1 — Fondasi Data & Domain.
Acuan: docs/RENCANA-PENYEMPURNAAN.md §6 (Model Data Target) dan §7 (Mesin Status).

Ini fase paling menentukan. Kerjakan dengan teliti; seluruh fase berikutnya
bertumpu di sini. TIDAK ADA pekerjaan UI di fase ini selain menjaga halaman lama
tetap hidup.

1. MIGRASI — buat seluruh tabel baru persis seperti §6.1:
   pengajuan, pengajuan_items, pengajuan_timeline, serah_terima, pelanggaran,
   sanksi, dokumen, asset_blackouts, phone_verifications, notification_logs,
   tata_tertib_versions, user_agreements, settings.
   Ketentuan:
   - Semua kolom waktu bertipe datetime (BUKAN date + time terpisah).
   - pengajuan_items memakai relasi polimorfik assetable (Ruangan|Barang).
   - Tambahkan seluruh indeks yang disebut di §6.3.
   - pengajuan dan pelanggaran memakai softDeletes.
   - pengajuan_timeline TIDAK boleh punya kolom updated_at (append-only).

2. MIGRASI — perubahan tabel lama persis seperti §6.2 (users, barangs, ruangans).
   users.phone disimpan ternormalisasi E.164. Tambah unique index pada
   (phone) yang mengizinkan null.

3. MODEL — buat/perbarui Eloquent model beserta relasi, cast, dan scope:
   Pengajuan, PengajuanItem, PengajuanTimeline, SerahTerima, Pelanggaran,
   Sanksi, Dokumen, AssetBlackout, PhoneVerification, NotificationLog,
   TataTertibVersion, UserAgreement, Setting.
   Pada User tambahkan: relasi pengajuan/pelanggaran/sanksi, accessor
   sanksiAktif(), isBlocked() yang membaca tabel sanksi (bukan kolom flag),
   dan mutator normalisasi nomor telepon.
   Hapus model kosong App\Models\TataTertib.

4. SERVICE — App\Services\:
   a. PengajuanStateMachine
      - Definisikan transisi sah persis seperti diagram §7.1.
      - Satu-satunya tempat status pengajuan/pengajuan_item boleh berubah.
      - Setiap transisi WAJIB menulis pengajuan_timeline (aksi, aktor, peran,
        dari/ke status, catatan, meta, ip).
      - Lempar DomainException berpesan Indonesia untuk transisi tidak sah.
   b. AvailabilityService
      - tersediaUntuk(Model $asset, Carbon $mulai, Carbon $selesai, ?int $abaikanItemId)
      - Rumus persis §5.3.3: stok_total dikurangi item beririsan berstatus
        {disetujui, berjalan}, dikurangi yang rusak/hilang belum diganti,
        dikurangi asset_blackouts.
      - Untuk ruangan: kapasitas 1 + buffer_menit dari config.
      - Metode jadwalTerpakai() untuk kalender.
      - Kueri harus memakai indeks (tanpa CONCAT, tanpa percabangan driver).
   c. EligibilityService
      - Aturan 1–6 pada §5.3.1, semuanya membaca config/sipinjam.php.
      - Mengembalikan objek hasil berisi daftar alasan penolakan (bukan boolean),
        agar UI bisa menjelaskan.
   d. NomorSuratService
      - Format dari config, penomoran per jenis dokumen per tahun,
        aman terhadap balapan (kunci baris / tabel counter, bukan COUNT(*)).
      - Ganti Peminjaman::generateNomorSurat() yang memakai COUNT(*) — metode itu
        menghasilkan nomor ganda bila ada penghapusan atau dua approve bersamaan.

5. POLICY & PERAN
   - Seeder role Spatie: mahasiswa, dosen, staff, staf_aset, pimpinan, admin,
     beserta permission granular (pengajuan.create/verify/approve/reject,
     serahterima.create, pelanggaran.create, sanksi.manage, master.manage, dst).
   - PengajuanPolicy, SerahTerimaPolicy, PelanggaranPolicy, SanksiPolicy.
   - Migrasikan user lama: role 'user' → 'mahasiswa', 'admin' → 'admin'.

6. MIGRASI DATA LEGACY
   Buat perintah `php artisan sipinjam:migrate-legacy`:
   - Idempoten (bisa dijalankan berulang tanpa duplikasi).
   - Setiap baris peminjamans → 1 Pengajuan (jenis=tunggal) + 1 PengajuanItem.
   - Petakan status lama → status baru: menunggu→diajukan, sedang_dipinjam→berjalan,
     selesai→selesai, ditolak→ditolak.
   - Buat entri pengajuan_timeline sintetis ("Dimigrasikan dari data lama").
   - Opsi --dry-run yang menampilkan ringkasan tanpa menulis.
   - Setelah sukses, migrasi terpisah me-rename peminjamans → peminjamans_legacy.
   - Tulis test yang memverifikasi jumlah baris dan pemetaan status.

7. FACTORY & SEEDER
   Perbarui seluruh factory/seeder untuk model baru, termasuk skenario demo:
   pengajuan event dengan 5 aset, pengajuan tunggal, pengajuan bermasalah dengan
   pelanggaran + sanksi aktif, dan riwayat 3 bulan ke belakang.

8. TEST (wajib, ini jaring pengaman fase berikutnya)
   - Unit: AvailabilityService (irisan waktu, blackout, rusak/hilang, buffer)
   - Unit: PengajuanStateMachine (semua transisi sah & tidak sah)
   - Unit: NomorSuratService (tidak ada nomor ganda pada 50 pemanggilan paralel)
   - Unit: EligibilityService (tiap aturan)
   - Feature: migrasi legacy
   - Feature: policy per peran

KRITERIA SELESAI:
[ ] `php artisan migrate:fresh --seed` sukses dari nol
[ ] `php artisan sipinjam:migrate-legacy --dry-run` lalu tanpa flag, keduanya benar
[ ] Seluruh test baru hijau; test lama tetap hijau (sesuaikan bila perlu)
[ ] Tidak ada perubahan perilaku yang terlihat pengguna (UI lama masih berfungsi)
[ ] Diagram status di §7.1 dan kode PengajuanStateMachine identik

LAPORKAN: skema akhir (daftar tabel + kolom kunci), daftar transisi status,
dan ringkasan hasil migrasi legacy.
```

---

## P2 — Peran, Identitas & Notifikasi WhatsApp

**Ketergantungan**: P1  ·  **Estimasi**: 5–7 hari  ·  **Tujuan**: setiap akun punya identitas & nomor WA terverifikasi; notifikasi berjalan.

```
[Sertakan Blok Konteks Standar di sini]

TUGAS: Fase P2 — Peran, Identitas & Notifikasi WhatsApp.
Acuan: docs/RENCANA-PENYEMPURNAAN.md §5.3.1 dan §5.3.6.

PENTING: README menjanjikan integrasi WhatsApp lewat Fonnte, tetapi di kode
TIDAK ADA implementasi apa pun (grep fonnte pada app/ = 0 hasil). Fase ini
membangunnya dari nol.

1. PROFIL & IDENTITAS
   - Halaman profil diperluas: identity_number (NIM/NIP), user_type
     (mahasiswa/dosen/staff), program_studi ATAU unit_kerja (tergantung tipe),
     jabatan, angkatan, nomor WhatsApp.
   - Validasi nomor: terima format 08xx / 62xx / +62xx, simpan selalu E.164.
     Buat App\Support\PhoneNumber untuk normalisasi + penyamaran.
   - Admin dapat mengelola identitas ini di Kelola User, termasuk impor CSV massal
     (nama, email, NIM/NIP, tipe, prodi/unit, nomor WA) dengan pratinjau & validasi.

2. VERIFIKASI OTP WHATSAPP
   - Tabel phone_verifications sudah ada dari P1.
   - Kode 6 digit, disimpan ter-hash, kadaluwarsa 10 menit, maksimal 5 percobaan,
     throttle kirim 1x/60 detik per user dan 5x/jam per IP.
   - Halaman /onboarding/kontak: input nomor → kirim OTP → verifikasi.
   - Middleware EnsureProfileComplete: user terautentikasi dengan profil belum
     lengkap atau phone belum terverifikasi diarahkan ke onboarding.
     KECUALIKAN rute: onboarding.*, logout, profile.*, panduan.
   - EligibilityService menolak pengajuan bila phone belum terverifikasi.

3. LAYANAN WHATSAPP
   - Interface App\Contracts\WhatsappDriver { send(string $to, string $message, array $opts): WhatsappResult }
   - Implementasi FonnteDriver (HTTP, token dari config/services.php →
     services.fonnte.token, timeout 10 detik, retry di level job bukan HTTP).
   - Implementasi LogDriver untuk lokal/test (menulis ke log + notification_logs).
   - Pilih driver lewat config('sipinjam.whatsapp.driver').
   - SendWhatsappNotification (queued job): retry 3x, backoff 30/120/300 detik,
     setiap percobaan tercatat di notification_logs.
   - Kegagalan pengiriman TIDAK BOLEH menggagalkan transaksi bisnis
     (kirim setelah commit, lewat queue).

4. TEMPLATE NOTIFIKASI
   Buat kelas notifikasi (WA + email dari template yang sama) untuk 9 peristiwa:
   pengajuan_diterima, pengajuan_diverifikasi, pengajuan_ditolak,
   pengajuan_disetujui (berisi tautan surat), pengingat_h1, pengingat_mulai,
   pengingat_pengembalian, hasil_pemeriksaan_bermasalah, sanksi_dijatuhkan.
   Semua berbahasa Indonesia, ringkas (WA maksimal ~5 baris), memuat nomor
   pengajuan, dan tautan dalam aplikasi. Simpan teks di lang/id/notifikasi.php.
   AKTIFKAN KEMBALI notifikasi email yang dimatikan di BookingService
   (komentar "⛔ Email notification DISABLED for MVP").

5. PRIVASI (UU PDP No. 27/2022)
   - Nomor WA hanya tampil penuh untuk role admin & staf_aset; peran lain
     mendapat bentuk tersamar (+62812****7890). Terapkan di level Resource/DTO,
     bukan hanya di template.
   - Teks persetujuan pemrosesan data di halaman onboarding, tercatat di
     user_agreements.
   - Catat setiap akses ke data kontak lengkap di pengajuan_timeline/audit log.

6. UI ADMIN — KARTU KONTAK PELAKU
   Di halaman detail pengajuan dan detail pelanggaran, tampilkan kartu:
   nama, NIM/NIP, tipe & prodi/unit, email, nomor WA (klik → wa.me),
   nama & WA PJ kegiatan, riwayat pelanggaran ringkas, status sanksi.

7. TEST
   - Normalisasi & penyamaran nomor (semua format masukan)
   - OTP: benar, salah, kadaluwarsa, melebihi percobaan, throttle
   - Middleware EnsureProfileComplete (termasuk daftar pengecualian)
   - Driver WhatsApp di-fake; verifikasi job ter-dispatch & notification_logs terisi
   - Penyamaran nomor untuk peran non-admin

KRITERIA SELESAI:
[ ] User baru tidak bisa mengajukan sebelum WA terverifikasi
[ ] OTP terkirim lewat Fonnte di staging (atau LogDriver di lokal)
[ ] 9 template notifikasi ada, berbahasa Indonesia, teruji
[ ] notification_logs terisi termasuk saat gagal
[ ] Nomor WA tersamar untuk peran non-admin (dibuktikan dengan test)
[ ] README diperbarui agar klaim WhatsApp sesuai kenyataan
```

---

## P3 — Pengajuan Paket Event & Aset Tunggal

**Ketergantungan**: P1, P2  ·  **Estimasi**: 7–10 hari  ·  **Tujuan**: satu kegiatan besar = satu pengajuan berisi banyak aset.

```
[Sertakan Blok Konteks Standar di sini]

TUGAS: Fase P3 — Pengajuan Paket Event & Aset Tunggal.
Acuan: docs/RENCANA-PENYEMPURNAAN.md §5.3.2 dan §5.3.3.

Ini fitur inti permintaan pengguna: "peminjaman ruangan beserta barang untuk
pengadaan event besar dipermudah, tidak perlu satu per satu, tapi selain itu
tetap dapat dilakukan secara terpisah."

1. BACKEND — PengajuanController + FormRequest
   - store() menerima header kegiatan + array items[] (assetable_type,
     assetable_id, jumlah, mulai_at, selesai_at, catatan).
   - Validasi berlapis dalam SATU transaksi dengan lockForUpdate per aset:
     kelayakan (EligibilityService) → lead time per aset → jam operasional →
     ketersediaan (AvailabilityService) per baris → kuota pengajuan aktif.
   - Pesan galat harus menunjuk baris yang bermasalah
     ("Baris 3 — Proyektor Epson: hanya tersedia 1 dari 3 unit pada 12 Okt 08:00–17:00").
   - Endpoint GET /api/ketersediaan untuk pengecekan real-time per baris
     (throttle 60/menit).
   - Unggah lampiran proposal (pdf/doc/docx, maks 5 MB), wajib bila jenis=event.

2. VERIFIKASI & PERSETUJUAN PER BARIS
   - staf_aset dapat menyetujui/menolak TIAP BARIS dengan alasan.
   - Status header dihitung dari agregat baris; bila sebagian ditolak →
     disetujui_sebagian. Semua lewat PengajuanStateMachine.
   - Pemohon mendapat notifikasi yang menjelaskan baris mana yang tidak disetujui.

3. FRONTEND — Formulir 5 langkah (Pages/Pengajuan/Create.vue)
   Langkah 1  Jenis: ○ Aset Tunggal  ● Paket Kegiatan/Event
   Langkah 2  Identitas kegiatan (nama, jenis_kegiatan, unit_penyelenggara,
              jumlah_peserta, PJ nama + WA, deskripsi, lampiran proposal)
              — dilewati untuk Aset Tunggal
   Langkah 3  Jadwal utama + opsi "jadwal berbeda per aset"
   Langkah 4  KERANJANG ASET: pencarian & filter katalog, tambah baris,
              atur jumlah + jadwal per baris, badge ketersediaan real-time
              (Tersedia / Sisa n / Bentrok) dengan debounce 400ms,
              ringkasan total di sisi/bawah
   Langkah 5  Ringkasan lengkap + centang tata tertib + kirim
   Ketentuan UI:
   - Full-screen sheet di mobile, dialog di desktop
   - Stepper menampilkan badge merah pada langkah yang punya error
   - Draf tersimpan otomatis di localStorage agar tidak hilang saat refresh
   - Tombol "Pinjam" di katalog Ruangan/Barang membuka jalur Aset Tunggal
     dengan aset sudah terisi (maksimal 3 ketukan sampai kirim)

4. HALAMAN DAFTAR & DETAIL
   - Pages/Pengajuan/Index.vue: daftar pengajuan milik user, filter status,
     paginasi, empty state
   - Pages/Pengajuan/Show.vue: header kegiatan, tabel baris aset beserta status
     per baris, timeline (dari P4), tombol unduh dokumen, tombol batalkan
   - Admin/Pengajuan/Index.vue & Show.vue: antrean verifikasi dengan filter
     (menunggu verifikasi / menunggu persetujuan / berjalan / bermasalah),
     aksi per baris, kartu kontak pemohon & PJ

5. KONFIGURASI
   Tambahkan ke config/sipinjam.php:
   lead_time: { event_hari_kerja: 7, ruangan_hari: 2, barang_hari: 1 }
   kuota_aktif: { mahasiswa: 3, dosen: 5, staff: 5 }
   jam_operasional: { buka: '07:00', tutup: '22:00' }
   buffer_menit: 30
   maks_item_per_pengajuan: 20

6. HAPUS JALUR LAMA
   Pensiunkan BookingController::store dan BookingModal.vue lama setelah jalur
   baru berfungsi. Arahkan rute lama ke rute baru agar tautan tersimpan tidak mati.

7. TEST
   - Feature: pengajuan event 1 ruangan + 3 barang berhasil, satu nomor pengajuan
   - Feature: satu baris bentrok → seluruh pengajuan ditolak dengan pesan per baris
   - Feature: persetujuan sebagian → status disetujui_sebagian
   - Feature: lead time kurang → ditolak dengan pesan jelas
   - Feature: kuota terlampaui → ditolak
   - Feature: dua pengajuan bersamaan pada slot sama (uji balapan)
   - Feature: jalur aset tunggal dari katalog

KRITERIA SELESAI:
[ ] Panitia dapat mengajukan aula + 200 kursi + 2 proyektor dalam satu berkas
[ ] Mahasiswa dapat meminjam 1 proyektor saja dalam ≤ 3 ketukan dari katalog
[ ] Indikator ketersediaan akurat dan real-time per baris
[ ] Tidak ada double-booking pada uji balapan
[ ] Semua validasi berpesan Bahasa Indonesia yang menunjuk baris bermasalah
```

---

## P4 — Dokumen Resmi & Transparansi

**Ketergantungan**: P1, P3  ·  **Estimasi**: 5–7 hari  ·  **Tujuan**: aplikasi menerbitkan surat siap tanda tangan dan mengekspos jejaknya.

```
[Sertakan Blok Konteks Standar di sini]

TUGAS: Fase P4 — Dokumen Resmi & Transparansi.
Acuan: docs/RENCANA-PENYEMPURNAAN.md §5.1 (seluruhnya) dan §7.2.

Ingat prinsipnya: mekanisme lama (tanda tangan basah) TETAP BERJALAN. Aplikasi
menyiapkan surat, mencatat siapa menandatangani kapan, menyimpan scan, dan
membuka jejaknya. Aplikasi tidak menggantikan keputusan pejabat.

1. MODE PERSETUJUAN
   config('sipinjam.approval_mode') = hybrid (default) | digital | manual.
   Terapkan perbedaannya di PengajuanStateMachine dan UI (§5.1.1).

2. LIMA TEMPLATE DOKUMEN
   Buat resources/views/dokumen/ berisi:
   - surat-permohonan.blade.php   (terbit saat status diajukan)
   - surat-izin.blade.php          (terbit saat status disetujui)
   - bast-keluar.blade.php         (terbit saat serah terima keluar)
   - bast-kembali.blade.php        (terbit saat pemeriksaan pengembalian)
   - ba-kerusakan.blade.php        (terbit saat pelanggaran rusak/hilang)
   Ketentuan:
   - Kop surat, alamat, logo, dan SELURUH penanda tangan dibaca dari
     config/sipinjam.php. Tidak boleh ada nama/NIP yang di-hardcode.
   - Blok tanda tangan sesuai §5.1.2 (berbeda per jenis dokumen).
   - Lampiran tabel rincian SELURUH baris aset (paket event bisa >10 baris,
     harus rapi saat pindah halaman, dengan header tabel berulang).
   - CSS di-inline; tanpa CDN eksternal; harus benar tanpa internet.
   - Footer: nomor dokumen, tanggal cetak, QR verifikasi.
   - Ukuran A4, margin 2,5 cm, font serif/sans yang tersedia lokal.

3. PENOMORAN & VERIFIKASI
   - NomorSuratService (dari P1) memberi nomor per jenis dokumen per tahun.
   - Setiap dokumen menyimpan hash isi + qr_token (ULID) ke tabel dokumen.
   - Rute publik GET /verifikasi/{token} menampilkan: jenis dokumen, nomor,
     nama kegiatan, unit penyelenggara, aset, rentang waktu, status, tanggal terbit.
     TIDAK menampilkan nomor telepon, email, NIM/NIP, atau alamat.
   - QR pada PDF mengarah ke URL tersebut (pakai pustaka QR lokal, bukan API online).

4. UNGGAH SCAN TANDA TANGAN BASAH (inti mode hybrid)
   - Di detail pengajuan (peran staf_aset/admin): form unggah scan surat
     (pdf/jpg/png, maks 5 MB) + tanggal tanda tangan + nama & jabatan penanda tangan.
   - Setelah tersimpan → transisi ke status disetujui → Surat Izin terbit →
     notifikasi ke pemohon & PJ.
   - Scan tersimpan di disk privat; diakses lewat rute bertanda tangan (signed URL),
     bukan URL publik.

5. RENDER PDF LEWAT ANTREAN
   - Job GenerateDokumen (queued) memakai PdfRenderer dari P0.
   - Bila dokumen sudah ada dan isinya tidak berubah (cek hash), kembalikan berkas
     tersimpan alih-alih render ulang.
   - Tombol unduh menampilkan status "sedang disiapkan" bila job belum selesai,
     lalu memberi notifikasi saat siap.
   - Bila Browsershot gagal: catat error, beri tahu admin, tampilkan pesan ramah
     ke pengguna. JANGAN error 500.

6. TIMELINE PENGAJUAN
   - Komponen Timeline.vue: daftar vertikal berisi ikon, aktor + perannya,
     waktu relatif + absolut, aksi, dan catatan/alasan.
   - Tampilkan di Pengajuan/Show.vue (pemohon) dan Admin/Pengajuan/Show.vue.
   - Untuk pemohon: sembunyikan catatan internal yang ditandai meta.internal=true.

7. HALAMAN TRANSPARANSI PUBLIK
   - Rute GET /transparansi (tanpa autentikasi), di-cache 5 menit.
   - Tampilan kalender bulanan + daftar: nama kegiatan, unit penyelenggara,
     aset, rentang waktu, status. TANPA nama pribadi, kontak, atau keterangan bebas.
   - Filter: aset, bulan, jenis kegiatan. Responsif dan ringan.
   - Tautkan dari landing page dan dari dalam aplikasi.

8. EKSPOR AUDIT
   - Admin: ekspor CSV & PDF seluruh pengajuan + timeline pada rentang tanggal,
     untuk kebutuhan audit internal/akreditasi.
   - Ekspor besar dijalankan lewat antrean dan dikirim sebagai tautan unduh.

9. TEST
   - Setiap jenis dokumen ter-render dan memuat data yang benar
   - Tidak ada nama/NIP hardcode (uji dengan mengubah config → PDF ikut berubah)
   - Nomor dokumen unik pada 50 pembuatan paralel
   - /verifikasi/{token} valid, dan token palsu memberi 404
   - Halaman transparansi tidak membocorkan data pribadi (uji asersi eksplisit)
   - Scan hanya dapat diakses lewat signed URL oleh peran yang berhak

KRITERIA SELESAI:
[ ] 5 dokumen terbit benar, lengkap dengan blok tanda tangan yang sesuai
[ ] Mengubah nama pejabat di config langsung mengubah semua surat
[ ] Surat paket event memuat lampiran rincian semua aset dengan rapi
[ ] QR verifikasi berfungsi dan halamannya tidak membocorkan data pribadi
[ ] Alur hybrid utuh: cetak permohonan → TTD basah → unggah scan → surat izin terbit
[ ] Timeline menampilkan seluruh jejak: siapa, kapan, apa, mengapa
```

---

## P5 — Pemeriksaan, Pelanggaran, Sanksi & Ganti Rugi

**Ketergantungan**: P1, P2, P4  ·  **Estimasi**: 6–8 hari  ·  **Tujuan**: ketidakpatuhan SOP terdeteksi, pelakunya pasti, tindak lanjutnya jelas.

```
[Sertakan Blok Konteks Standar di sini]

TUGAS: Fase P5 — Pemeriksaan, Pelanggaran, Sanksi & Ganti Rugi.
Acuan: docs/RENCANA-PENYEMPURNAAN.md §5.3.4, §5.3.5, dan §8 (B-04, B-05).

PERINGATAN PENTING: kode yang ada sekarang memblokir pengguna berdasarkan TEBAKAN
(AdminController::laporBerantakan mencari "peminjaman selesai terakhir di ruangan
ini hari ini") dan berdasarkan waktu semata (ApplySanctionPenalties memblokir 30
hari meski barang mungkin sudah dikembalikan). Keduanya HARUS ditulis ulang agar
sanksi selalu bertumpu pada catatan pemeriksaan yang menyebut pelaku secara pasti.

1. SERAH TERIMA KELUAR (BAST)
   - Halaman petugas: pilih pengajuan berstatus disetujui → checklist kondisi awal
     per baris aset → jumlah diserahkan → foto → nama penerima → simpan.
   - Menghasilkan record serah_terima(tipe=keluar) + dokumen bast_keluar +
     transisi status ke berjalan.
   - Bila peminjam tidak mengambil sampai batas waktu: tandai no_show →
     pelanggaran jenis tidak_digunakan (poin rendah) + slot dibebaskan.

2. PEMERIKSAAN PENGEMBALIAN
   - Template checklist dari config, terpisah untuk ruangan dan barang
     (isi persis tabel di §5.3.4). Dapat diubah admin tanpa deploy.
   - Setiap butir: ya / tidak / tidak berlaku + catatan; FOTO WAJIB bila "tidak".
   - Untuk barang: jumlah kembali + kondisi (baik/lecet/rusak_ringan/rusak_berat/hilang).
   - Hitung keterlambatan otomatis dari selesai_at vs waktu pemeriksaan.
   - Hasil: semua lolos → status selesai; ada temuan → status bermasalah.
   - Menghasilkan record serah_terima(tipe=kembali) + dokumen bast_kembali.
   - HALAMAN INI HARUS OPTIMAL DI HP (dipakai berdiri di depan ruangan):
     tombol besar, satu kolom, ambil foto langsung dari kamera
     (<input type="file" accept="image/*" capture="environment">),
     kompresi gambar di sisi klien sebelum unggah, indikator progres unggah,
     dan penyimpanan draf lokal agar tidak hilang bila sinyal putus.

3. PELANGGARAN — PELAKU DARI DATA, BUKAN TEBAKAN
   - Record pelanggaran dibuat dari serah_terima yang bermasalah.
   - user_id diambil dari pengajuan.user_id pada baris yang diperiksa;
     data PJ (pj_nama, pj_phone) disalin ke deskripsi pelanggaran.
   - Bukti foto dari pemeriksaan ikut tertaut.
   - HAPUS AdminController::laporBerantakan() beserta rutenya. Ganti dengan
     "Catat Temuan" yang WAJIB memilih pengajuan/baris aset terkait dari daftar,
     sehingga tidak ada penetapan pelaku secara heuristik.

4. SANKSI BERJENJANG
   - Matriks sanksi persis tabel di §5.3.5, seluruhnya di config/sipinjam.php.
   - SanksiService: hitung poin akumulatif per semester, tentukan sanksi,
     buat record sanksi, perbarui cache users.is_blocked/blocked_until.
   - Setiap sanksi WAJIB: memicu notifikasi WA + email yang memuat jenis
     pelanggaran, ringkasan bukti, masa berlaku, dan cara mengajukan keberatan.
   - Halaman "Sanksi Saya" untuk pengguna: daftar pelanggaran, bukti, masa blokir,
     tombol "Ajukan Keberatan" (form + lampiran).
   - Admin dapat mencabut sanksi dengan alasan wajib
     (dicabut_oleh, dicabut_at, alasan_pencabutan) — tercatat di timeline.

5. GANTI RUGI
   - Papan admin: daftar tunggakan berisi pelaku (nama, NIM/NIP, tipe, unit),
     kontak WA (klik → wa.me), aset, jumlah, kondisi, nilai ganti rugi
     (default dari barangs.nilai_perolehan, dapat disesuaikan), status
     (menunggu → disepakati → lunas), lampiran bukti pembayaran/penggantian.
   - Selama status belum lunas untuk pelanggaran rusak/hilang → sanksi blokir
     tetap aktif (sesuai matriks).
   - Dokumen ba_kerusakan + Surat Pernyataan Ganti Rugi dapat diunduh.

6. TULIS ULANG SCHEDULER
   a. sanction:apply
      - Jangan lagi memblokir hanya karena status masih berjalan melewati waktu.
      - Alur baru: peminjaman lewat jatuh tempo → status menunggu_pemeriksaan →
        kirim pengingat ke peminjam & petugas → setelah grace period
        (config, default 24 jam) tanpa pemeriksaan → buat pelanggaran
        jenis "terlambat" → SanksiService menentukan sanksi → notifikasi terkirim.
      - Wajib memakai chunkById (jangan memuat seluruh tabel ke memori).
      - Wajib idempoten (dijalankan dua kali tidak menggandakan pelanggaran).
   b. Tambahkan perintah baru: pengingat H-1, pengingat mulai, pengingat
      pengembalian T-2 jam, dan pembebasan sanksi yang telah berakhir.
   c. Daftarkan seluruh jadwal di routes/console.php dengan
      ->withoutOverlapping()->onOneServer().

7. TEST
   - Serah terima keluar → status berjalan + dokumen terbit
   - Pemeriksaan lolos → selesai; ada temuan → bermasalah + pelanggaran terbuat
   - Pelaku pelanggaran SELALU sama dengan pemilik pengajuan (uji dengan beberapa
     pengajuan di ruangan yang sama pada hari yang sama — kasus yang dulu salah)
   - Sanksi berjenjang: pelanggaran ke-1/2/3 menghasilkan sanksi berbeda
   - Rusak/hilang → blokir sampai ganti rugi lunas; setelah lunas → otomatis bebas
   - Scheduler idempoten (jalankan 2x, jumlah pelanggaran tetap)
   - Notifikasi sanksi terkirim (driver di-fake)

KRITERIA SELESAI:
[ ] Petugas dapat menyelesaikan pemeriksaan dari HP dalam < 2 menit, dengan foto
[ ] Tidak ada satu pun jalur kode yang menetapkan pelaku secara heuristik
[ ] Setiap sanksi punya bukti tertaut dan notifikasi terkirim
[ ] Admin tahu persis siapa yang harus mengganti rugi, berapa, dan lewat kontak apa
[ ] Pengguna dapat melihat pelanggarannya dan mengajukan keberatan
[ ] laporBerantakan() dan blokir berbasis waktu semata sudah tidak ada
```

---

## P6 — PWA & Mobile-First

**Ketergantungan**: P0 (dapat berjalan paralel dengan P3–P5)  ·  **Estimasi**: 6–8 hari  ·  **Tujuan**: aplikasi terpasang di HP dan nyaman dipakai satu tangan.

```
[Sertakan Blok Konteks Standar di sini]

TUGAS: Fase P6 — PWA & Mobile-First.
Acuan: docs/RENCANA-PENYEMPURNAAN.md §5.2 (seluruhnya) dan §5.4.1 (U-01, U-05).

MASALAH TERBESAR SAAT INI: UserLayout.vue dan AdminLayout.vue memakai
<main class="ml-64"> dengan sidebar fixed w-64 dan TIDAK punya satu pun prefix
responsif (sm:/md:/lg:). Di layar 360px konten terdorong keluar viewport.
Aplikasi ini secara praktis tidak dapat dipakai di HP hari ini.

A. PWA

1. Pasang vite-plugin-pwa (strategi generateSW/Workbox). Konfigurasikan di
   vite.config.js agar berdampingan dengan laravel-vite-plugin
   (outDir public/build, base sesuai APP_URL, injeksi registrasi SW).

2. Manifest sesuai §5.2.1:
   name "SiPinjam — Peminjaman Aset Kampus", short_name "SiPinjam", id "/",
   start_url "/dashboard", scope "/", display "standalone",
   orientation "portrait", theme_color "#2563eb", background_color "#ffffff",
   lang "id", categories ["productivity","education"].
   Ikon: 192, 512, dan 512 MASKABLE (wajib, agar ikon Android tidak kotak putih),
   plus apple-touch-icon 180x180. Buat dari public/logo.png.
   Sertakan screenshots (form_factor narrow + wide) agar Chrome menampilkan
   UI instalasi yang kaya. Sertakan shortcuts: "Ajukan Peminjaman",
   "Riwayat Saya", "Cek Ketersediaan".

3. Service worker sesuai tabel strategi di §5.2.2. Tegaskan:
   - Navigasi Inertia: NetworkOnly + fallback ke /offline
   - JANGAN pernah meng-cache permintaan POST/PUT/PATCH/DELETE
   - JANGAN meng-cache respons ber-Set-Cookie atau halaman dengan data pribadi
   - skipWaiting + clientsClaim, disertai banner "Versi baru tersedia — Muat ulang"

4. Halaman /offline (blade statis, tidak lewat Inertia): pesan ramah berbahasa
   Indonesia, tombol "Coba lagi", dan nomor WA admin.

5. InstallPwaPrompt.vue — pop-up instalasi sesuai §5.2.3:
   - Server membagikan pwa.shouldPrompt lewat HandleInertiaRequests,
     di-set sekali per sesi login (kunci sesi), dimatikan bila
     users.pwa_prompt_dismissed_at terisi.
   - Chrome/Edge: tangkap dan tahan event beforeinstallprompt, tampilkan bottom
     sheet kustom, tombol "Pasang Aplikasi" memanggil prompt().
   - iOS Safari: beforeinstallprompt TIDAK PERNAH dipicu Safari. Deteksi iOS +
     bukan standalone, lalu tampilkan instruksi bergambar
     "Ketuk ikon Bagikan → Tambahkan ke Layar Utama".
   - Firefox Android: instruksi menu → Install.
   - Jangan tampilkan bila sudah terpasang:
     matchMedia('(display-mode: standalone)').matches || navigator.standalone.
   - Tombol: "Pasang" · "Nanti saja" (snooze 7 hari via localStorage) ·
     "Jangan tampilkan lagi" (POST ke server, persisten lintas perangkat).
   - Catat event appinstalled ke server untuk statistik adopsi.
   - Tambahkan entri "Pasang Aplikasi" di menu profil agar pengguna yang menunda
     tetap bisa memasang kapan saja.

6. Meta tag di resources/views/app.blade.php: theme-color (light & dark),
   apple-mobile-web-app-capable, apple-mobile-web-app-status-bar-style,
   apple-mobile-web-app-title, apple-touch-icon, dan viewport-fit=cover.

B. MOBILE-FIRST

7. Buat resources/js/Layouts/AppShell.vue menggantikan UserLayout & AdminLayout:
   - < lg : TopAppBar (judul halaman + tombol menu + aksi) · konten ·
            BottomNav 5 item · Drawer off-canvas untuk menu lengkap
   - >= lg: Sidebar permanen 264px · konten
   - BottomNav user : Beranda · Ruangan · Barang · Riwayat · Profil
   - BottomNav admin: Beranda · Pengajuan · Aset · Pemeriksaan · Lainnya
   - FAB "Ajukan Peminjaman" di mobile (peran peminjam saja)
   - padding-bottom: env(safe-area-inset-bottom) untuk iPhone
   - Toast global flash message di dalam shell

8. Komponen ResponsiveTable.vue: tabel penuh di lg+, daftar kartu di mobile
   (label di kiri, nilai di kanan, aksi di baris bawah). Terapkan ke SELURUH
   tabel admin: KelolaPeminjaman/Pengajuan, KelolaUser, KelolaBarang,
   KelolaRuangan, Admin/Dashboard, Laporan.

9. Perbaikan sentuh & masukan:
   - Semua target sentuh >= 44x44px
   - font-size >= 16px pada seluruh input (mencegah auto-zoom iOS)
   - inputmode & autocomplete yang tepat (tel, email, numeric)
   - Modal/dialog menjadi full-screen sheet di < sm
   - FullCalendar: tampilan listWeek di mobile, dayGridMonth di desktop
   - Landing page & halaman katalog: audit grid agar tidak meluber di 360px

10. Uji pada 360x640, 390x844, 768x1024, dan 1440x900. Tidak boleh ada scroll
    horizontal di halaman mana pun.

TEST & VERIFIKASI:
[ ] Lighthouse (mobile): PWA installable ✔, Performance >= 85, Accessibility >= 90
[ ] Manifest valid; ikon maskable tampil benar di Android
[ ] Pop-up instalasi muncul setiap login di Chrome; instruksi manual di iOS;
    TIDAK muncul bila aplikasi sudah terpasang
[ ] "Nanti saja" menunda 7 hari; "Jangan tampilkan lagi" persisten lintas perangkat
[ ] /offline tampil saat jaringan dimatikan
[ ] Tidak ada scroll horizontal pada seluruh halaman di 360px
[ ] Seluruh tabel admin terbaca sebagai kartu di mobile
[ ] Halaman pemeriksaan (P5) dapat diselesaikan satu tangan
[ ] `npm run build` sukses dan service worker ter-generate di public/build
```

---

## P7 — Audit UI/UX, Konsistensi Desain & Panduan Pengguna

**Ketergantungan**: P6  ·  **Estimasi**: 6–8 hari  ·  **Tujuan**: tampilan konsisten, mudah dipakai, dan pengguna baru tidak tersesat.

```
[Sertakan Blok Konteks Standar di sini]

TUGAS: Fase P7 — Audit UI/UX, Konsistensi Desain & Panduan Pengguna.
Acuan: docs/RENCANA-PENYEMPURNAAN.md §5.4 (seluruhnya).

Data audit awal: 770 kelas warna hardcoded (text-/bg-/border- slate|blue|indigo|...)
berbanding 427 token semantik. Ada dua konvensi folder komponen yang berisiko
gagal build di filesystem case-sensitive.

A. KONSISTENSI DESAIN

1. Satukan folder komponen: pindahkan resources/js/components/ui/**
   → resources/js/Components/ui/** dan perbarui seluruh impor + alias.
   Verifikasi tidak ada impor yang masih memakai huruf kecil.

2. Perluas token di resources/css/app.css dan tailwind.config.js:
   --success, --warning, --info, --surface, --surface-muted
   (beserta pasangan -foreground). Tambahkan blok .dark yang lengkap.

3. Buat resources/js/lib/status.js sebagai SATU-SATUNYA sumber pemetaan
   status → { label, deskripsi, variant, icon } untuk seluruh status pengajuan,
   status baris, dan status pelanggaran/sanksi. Buat StatusBadge.vue yang
   memakainya. Ganti seluruh penulisan label status yang tersebar di komponen.

4. Migrasikan warna hardcoded ke token, dimulai dari:
   AppShell, Sidebar, BottomNav, semua Pages/Pengajuan, semua Pages/Admin,
   BookingModal/form pengajuan, Dashboard user & admin, Landing.
   Target: 0 kelas warna mentah pada komponen inti. Warna dekoratif pada
   Landing page boleh dikecualikan bila disebut eksplisit di komentar.

5. Tambahkan komponen yang hilang ke pustaka UI:
   Table, Alert, Toast, Sheet, DropdownMenu, Checkbox, RadioGroup, Textarea,
   Skeleton, EmptyState, Pagination, Stepper, FileUpload, PhotoCapture,
   ConfirmDialog, PageHeader, Timeline.
   Semua mengikuti Design System Guide (radius, shadow, focus ring, transisi).

6. Terapkan pada setiap halaman daftar: EmptyState dengan ajakan bertindak,
   Skeleton saat memuat, Pagination, dan ConfirmDialog untuk aksi destruktif.

7. Aksesibilitas (target WCAG 2.1 AA):
   - Perbaiki kontras rendah (mis. text-blue-200/70 pada sidebar biru)
   - aria-label pada seluruh tombol ikon; alt bermakna pada gambar
   - focus-visible:ring pada SEMUA elemen interaktif termasuk tombol kustom
   - Navigasi keyboard penuh; focus trap pada dialog; skip-to-content
   - Umumkan pesan flash lewat aria-live
   - prefers-reduced-motion dihormati (nonaktifkan animasi GSAP bila diminta)

8. Ekstrak teks UI utama ke lang/id.json (atau berkas i18n setara) agar redaksi
   dapat dikoreksi terpusat. Perbaiki inkonsistensi istilah: pakai satu istilah
   untuk satu hal (mis. selalu "pengajuan", bukan campuran "booking"/"peminjaman"
   di antarmuka).

9. Tangani sesi kedaluwarsa (419) dan galat jaringan pada PWA dengan interceptor
   Inertia yang mengarahkan ke login dengan pesan jelas, bukan layar galat mentah.

10. Perbarui "Design System Guide .md" menjadi dokumen operasional: daftar token,
    tabel pemetaan status, daftar komponen beserta contoh pemakaian, aturan
    spacing/tipografi, dan aturan responsif (breakpoint mana untuk apa).

B. PANDUAN PENGGUNA BARU

11. Onboarding wajib 3 langkah (sekali per akun), sesuai §5.4.3:
    Langkah 1 Lengkapi identitas (NIM/NIP, tipe, prodi/unit)
    Langkah 2 Verifikasi nomor WhatsApp (OTP dari P2)
    Langkah 3 Baca & setujui Tata Tertib versi berlaku
    Simpan persetujuan di user_agreements (user, versi, waktu, IP).
    Tandai users.onboarding_completed_at setelah selesai.

12. Tata tertib berversi:
    - Tabel tata_tertib_versions dari P1; admin dapat menyunting & menerbitkan versi baru
    - Saat versi baru terbit, pengguna diminta menyetujui ulang sebelum mengajukan
    - Halaman Tata Tertib menampilkan versi aktif + riwayat versi

13. Tur produk (dapat dilewati & diulang):
    5–7 sorotan di Dashboard: cek ketersediaan → ajukan → pantau status →
    unduh surat → lihat sanksi. Tombol "?" di app bar untuk memutar ulang.
    Simpan status "sudah menonton" per pengguna. Gunakan pustaka ringan
    (mis. driver.js) atau implementasi sendiri — sebutkan pilihannya.

14. Halaman /panduan:
    - Tab per peran: Mahasiswa · Dosen & Staf · Petugas Aset · Admin
    - Alur SOP dalam diagram (boleh Mermaid yang dirender statis atau SVG)
    - Langkah bergambar (tangkapan layar aplikasi)
    - FAQ: berapa lama diproses, kenapa ditolak, bagaimana kalau terlambat,
      bagaimana mengajukan keberatan, bagaimana memasang aplikasi di HP
    - Contoh surat (versi contoh, ditandai jelas sebagai CONTOH)
    - Tombol unduh "Panduan Singkat (PDF)"
    - Dapat diakses tanpa login

15. Bantuan kontekstual: teks pembantu di bawah field penting, tooltip untuk
    istilah (PJ, BAST, lead time, ganti rugi), banner lead time pada form event,
    dan empty state yang mengajari langkah pertama.

KRITERIA SELESAI:
[ ] `grep -rE "(text|bg|border)-(slate|gray|blue|indigo|sky|emerald|red|amber)-[0-9]" resources/js/Components resources/js/Layouts resources/js/Pages/Pengajuan resources/js/Pages/Admin` mendekati 0 (sisanya berkomentar alasan)
[ ] Satu konvensi folder komponen; build lolos di Linux case-sensitive
[ ] Label & warna status berasal dari lib/status.js saja
[ ] Lighthouse Accessibility >= 95 pada Dashboard, Pengajuan, Katalog
[ ] Onboarding 3 langkah wajib dan tercatat di user_agreements
[ ] Tur produk dapat dilewati dan diulang
[ ] /panduan lengkap untuk 4 peran dan dapat diakses tanpa login
[ ] Design System Guide .md diperbarui dan sesuai dengan kode
```

---

## P8 — Pengerasan Rilis

**Ketergantungan**: seluruh fase  ·  **Estimasi**: 5–7 hari  ·  **Tujuan**: aman, cepat, terpantau, dan dapat diserahterimakan.

```
[Sertakan Blok Konteks Standar di sini]

TUGAS: Fase P8 — Pengerasan Rilis.
Acuan: docs/RENCANA-PENYEMPURNAAN.md §8 (Risiko Operasional) dan §10 (Checklist Rilis).

1. KUALITAS & PERFORMA
   - Test end-to-end alur lengkap: ajukan (event) → verifikasi → unggah scan →
     surat izin → serah terima → pemeriksaan bermasalah → sanksi → ganti rugi → bebas
   - Pasang Laravel Telescope/Debugbar di lokal; hilangkan seluruh kueri N+1 pada
     halaman daftar (buktikan dengan assertion jumlah kueri pada test)
   - Uji beban ringan: 2.000 pengajuan, 10.000 baris item, 500 pengguna —
     halaman daftar tetap < 500 ms
   - Cakupan test alur inti >= 70%
   - Semua rute punya nama; `php artisan route:list` bersih dari rute mati

2. KEAMANAN
   - Rate limit: login (sudah ada), OTP, pengajuan, unduh dokumen, endpoint ketersediaan
   - Security headers (CSP, X-Frame-Options, X-Content-Type-Options,
     Referrer-Policy, Permissions-Policy) — CSP harus kompatibel dengan service worker
   - Berkas unggahan (proposal, foto, scan) disimpan di disk privat, diakses lewat
     signed URL, divalidasi MIME sungguhan (bukan hanya ekstensi), dan dipindai ukuran
   - Otorisasi diuji per peran untuk SETIAP rute (test matriks peran x rute)
   - Tidak ada data pribadi di log; sanitasi payload notification_logs
   - `composer audit` dan `npm audit` bersih dari kerentanan tinggi/kritis

3. OPERASIONAL
   - Dokumen runbook deployment: kebutuhan server (PHP 8.2+, Chromium untuk
     Browsershot, Node), langkah rilis, storage:link, config:cache,
     supervisor untuk queue:work, cron untuk schedule:run, rollback
   - spatie/laravel-backup: cadangan DB + storage harian, notifikasi kegagalan
   - Monitoring galat (Sentry/Flare) + log terstruktur + health check /up
   - Berkas .env.production.example lengkap dan berkomentar
   - Uji migrasi legacy pada SALINAN data produksi sebelum rilis

4. DOKUMENTASI SERAH TERIMA (di docs/)
   - PANDUAN-ADMIN.md (kelola master data, verifikasi, sanksi, ganti rugi, konfigurasi)
   - PANDUAN-PETUGAS-ASET.md (serah terima & pemeriksaan lapangan lewat HP)
   - PANDUAN-PENGGUNA.md (mahasiswa/dosen/staf)
   - RUNBOOK-DEPLOYMENT.md
   - KONFIGURASI.md (penjelasan setiap kunci config/sipinjam.php)
   - Perbarui README.md: hapus klaim fitur yang tidak ada, perbarui daftar fitur
     sebenarnya, tambahkan bagian PWA dan alur SOP

5. DATA PRODUKSI
   - Seeder produksi: role & permission, satu akun admin (kata sandi diminta saat
     seed, tidak di-hardcode), tata tertib versi 1, konfigurasi kop surat & pejabat
   - Perintah `php artisan sipinjam:setup` yang memandu konfigurasi awal secara interaktif
   - HAPUS akun demo (admin@sipinjam.ac.id / password) dari jalur produksi

6. UAT
   - Siapkan skenario UAT tertulis untuk 4 peran (minimal 20 skenario)
   - Sediakan lingkungan staging berisi data contoh yang realistis
   - Catat temuan UAT di docs/TEMUAN-UAT.md dan tuntaskan yang berkategori blocker

KRITERIA SELESAI:
[ ] Seluruh checklist §10 pada RENCANA-PENYEMPURNAAN.md tercentang
[ ] CI hijau; audit dependensi bersih
[ ] Runbook diuji dengan deployment sungguhan ke staging
[ ] Backup terbukti dapat dipulihkan (uji restore)
[ ] Tidak ada kredensial demo yang tersisa di jalur produksi
[ ] Empat dokumen panduan lengkap dan akurat
```

---

## Lampiran A — Prompt Pendek untuk Tugas Terpisah

Gunakan bila Anda hanya ingin mengerjakan satu potong pekerjaan.

### A1. Audit ulang setelah beberapa fase

```
Audit ulang repo ini terhadap docs/RENCANA-PENYEMPURNAAN.md.
Untuk setiap baris di tabel §4 (Analisis Gap) dan §8 (Temuan Bug),
tentukan statusnya sekarang: SELESAI / SEBAGIAN / BELUM, sertakan bukti
berupa path berkas dan nomor baris. Jangan mengubah kode apa pun.
Keluarkan hasilnya sebagai tabel Markdown ke docs/STATUS-AUDIT.md.
```

### A2. Hanya PWA + pop-up instalasi

```
Kerjakan HANYA bagian A (PWA) dari Fase P6 di docs/PROMPT-EKSEKUSI.md,
tanpa menyentuh layout. Sertakan vite-plugin-pwa, manifest lengkap dengan ikon
maskable, service worker sesuai tabel strategi §5.2.2, halaman /offline, dan
komponen InstallPwaPrompt.vue dengan perilaku per platform sesuai §5.2.3
(termasuk instruksi manual untuk iOS Safari yang tidak memicu beforeinstallprompt).
```

### A3. Hanya perombakan surat

```
Kerjakan HANYA butir 2 dan 3 dari Fase P4: pindahkan seluruh identitas kop surat
dan penanda tangan ke config/sipinjam.php, hapus nama & NIP fiktif yang
di-hardcode di resources/views/pdf/surat-peminjaman.blade.php, hapus
ketergantungan CDN Tailwind/Google Fonts di seluruh template PDF, dan buat lima
template dokumen sesuai §5.1.2. Sertakan test yang membuktikan bahwa mengubah
config mengubah isi PDF.
```

### A4. Hanya audit UI/UX (tanpa mengubah kode)

```
Lakukan audit UI/UX menyeluruh terhadap seluruh berkas di resources/js.
Untuk setiap halaman, laporkan: (1) apakah terpakai di lebar 360px,
(2) jumlah kelas warna hardcoded vs token, (3) empty state ada/tidak,
(4) loading state ada/tidak, (5) masalah aksesibilitas (kontras, aria, fokus),
(6) inkonsistensi istilah. Jangan mengubah kode.
Keluarkan sebagai docs/AUDIT-UIUX.md dengan tabel per halaman dan
daftar rekomendasi terurut berdasarkan dampak.
```

### A5. Hanya perbaikan penetapan pelaku pelanggaran

```
Kerjakan HANYA butir 3 dari Fase P5. Hapus AdminController::laporBerantakan()
beserta rutenya karena menetapkan pelaku lewat tebakan query
("peminjaman selesai terakhir di ruangan ini hari ini"). Ganti dengan alur
"Catat Temuan" yang mewajibkan petugas memilih pengajuan/baris aset terkait,
sehingga pelaku diambil dari pengajuan.user_id. Sertakan test dengan tiga
pengajuan berbeda pada ruangan yang sama di hari yang sama untuk membuktikan
pelaku yang dicatat selalu benar.
```

---

## Lampiran B — Aturan Konteks untuk Agen

Sertakan blok ini bila agen mulai kehilangan arah:

```
BATASAN YANG TIDAK BOLEH DILANGGAR:
1. Aplikasi TIDAK menggantikan SOP kertas. Ia menyiapkan surat, mencatat
   keputusan, dan membuka jejaknya. Jangan membuat fitur yang mengklaim
   persetujuan digital sebagai keputusan final kecuali approval_mode = digital.
2. Sanksi TIDAK BOLEH dijatuhkan tanpa record pelanggaran yang menunjuk
   pengajuan/baris aset tertentu. Dilarang menebak pelaku dari kedekatan waktu.
3. Nama, NIP, jabatan, dan kop surat TIDAK BOLEH di-hardcode di mana pun.
   Semuanya dari config/sipinjam.php.
4. Template PDF TIDAK BOLEH bergantung pada CDN atau sumber daya internet.
5. Nomor telepon, NIM/NIP, dan foto adalah data pribadi. Jangan menampilkannya
   di permukaan publik; samarkan untuk peran yang tidak berhak.
6. Kegagalan mengirim notifikasi TIDAK BOLEH menggagalkan transaksi bisnis.
7. Setiap perubahan status WAJIB melewati PengajuanStateMachine dan menulis
   pengajuan_timeline.
8. Jangan memakai env() di luar berkas config/.
9. Jangan memuat seluruh tabel ke memori — selalu paginate atau chunkById.
10. Seluruh teks yang menghadap pengguna berbahasa Indonesia yang jelas,
    tanpa jargon teknis dan tanpa istilah Inggris yang tidak perlu.
11. Version control adalah wewenang pemilik repo. Jangan membuat commit,
    branch, atau push. Jangan pula mengusulkan pesan commit kecuali diminta.
```
