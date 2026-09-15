# Temuan Tambahan

Temuan yang ditemukan saat mengerjakan sebuah fase, **tetapi berada di luar lingkup fase tersebut**.
Dicatat di sini agar tidak hilang, dan dikerjakan pada fase yang tepat — bukan langsung ditambal.

Aturan pengisian: satu baris = satu temuan. Sertakan lokasi berkas dan fase yang tepat untuk menanganinya.

---

## Ditemukan saat P0 (6 September 2026)

### Kinerja & Data

| # | Temuan | Lokasi | Fase |
| :-- | :--- | :--- | :--- |
| T-01 | `Ruangan::$appends = ['is_terpakai']` memanggil `getIsTerpakaiAttribute()` yang menjalankan kueri **per baris**, sehingga setiap serialisasi koleksi memicu N+1. Terasa di `kelolaRuangan()` dan `RuanganController@index`. | `app/Models/Ruangan.php` | P1 — digantikan `AvailabilityService` |
| T-02 | `Peminjaman::generateNomorSurat()` memakai `COUNT(*)` + `LIKE`. Dua approve bersamaan menghasilkan nomor yang sama, dan penghapusan baris membuat nomor terpakai ulang — padahal `nomor_surat` punya indeks unik. | `app/Models/Peminjaman.php` | P1 — `NomorSuratService` |
| T-03 | Kolom mati yang tidak pernah ditulis aplikasi: `peminjamans.tanggal`, `barangs.foto`, `ruangans.foto` (hanya `image_path` yang dipakai). | migrasi | P1 |
| T-04 | `down()` pada migrasi pertama menghapus tabel bernama `peminjamen` (salah ketik) → rollback tidak melakukan apa pun. | `database/migrations/2026_04_15_041717_create_peminjaman_table.php` | P1 |
| T-05 | Kolom `tanggal_mulai`/`jam_mulai` kini berdampingan dengan `mulai_at`/`selesai_at` hasil P0. Duplikasi ini sengaja dibiarkan demi kompatibilitas dan harus dituntaskan saat migrasi ke `pengajuan_items`. | `peminjamans` | P1 |

### Keamanan & Otorisasi

| # | Temuan | Lokasi | Fase |
| :-- | :--- | :--- | :--- |
| T-06 | Rute admin hanya dijaga middleware `admin`; **tidak** memakai `verified` maupun `blocked` seperti rute user. | `routes/web.php` | P1 — Policy per aksi |
| T-07 | Callback Google memanggil `Auth::login()` tanpa memeriksa status blokir. User yang diblokir tetap bisa masuk lewat Google dan baru tertahan di rute ber-middleware `blocked` berikutnya. | `app/Http/Controllers/Auth/SocialiteController.php` | P1 |
| T-08 | Model peran ganda: kolom `users.role` **dan** Spatie roles, keduanya ditulis di `storeUser()`. Dua sumber kebenaran yang bisa tidak sinkron. | `AdminController::storeUser` | P1 — seeder peran baru |
| T-09 | Pola `catch (\Throwable $e) → back()->with('error', $e->getMessage())` terulang belasan kali dan membocorkan pesan exception mentah ke antarmuka pengguna. | `app/Http/Controllers/Admin/AdminController.php` | P8 — pengerasan |
| T-10 | Rate limit hanya ada pada `POST /bookings` (`throttle:5,1`). Login, unduh dokumen, dan ekspor belum dibatasi. | `routes/web.php` | P8 |

### Frontend & Build

| # | Temuan | Lokasi | Fase |
| :-- | :--- | :--- | :--- |
| T-11 | `radix-vue` **dan** `reka-ui` sama-sama terpasang — `reka-ui` adalah `radix-vue` yang berganti nama. Pustaka yang sama dikirim dua kali ke pengguna. | `package.json` | P7 |
| T-12 | `alpinejs` tidak diimpor berkas mana pun (sisa era Blade). `tailwindcss-animate` terpasang tetapi tidak terdaftar di `plugins` `tailwind.config.js`. | `package.json`, `tailwind.config.js` | P7 |
| T-13 | `import.meta.glob('./Pages/**/*.vue', { eager: true })` memuat **seluruh** halaman ke bundel awal. Itu sebabnya `chunkSizeWarningLimit` dinaikkan ke 800 alih-alih memperbaiki penyebabnya. | `resources/js/app.js` | P6 |
| T-14 | Pada `manualChunks`, `id.includes('vue')` diuji lebih dulu sehingga ikut menangkap `@inertiajs/vue3`, `radix-vue`, `@fullcalendar/vue3` — semuanya masuk `vendor-core`, bukan chunk yang dimaksud. | `vite.config.js` | P6 |
| T-15 | `content` pada `tailwind.config.js` tidak memuat `./resources/js/**/*.js`. Saat `lib/status.js` dibuat di P7, kelas warna di dalamnya akan ter-purge dan hilang dari hasil build. | `tailwind.config.js` | P7 |
| T-16 | Dua konvensi folder komponen: `resources/js/Components/*.vue` (kapital) dan `resources/js/components/ui/**` (kecil). Keduanya resolve di Linux maupun Windows, jadi **bukan** kegagalan build — tetapi tetap membingungkan. | `resources/js/` | P7 |
| T-17 | Label & warna status ditulis ulang di empat tempat dengan palet berbeda: `sedang_dipinjam` berwarna emerald di halaman admin, biru di halaman user. | `Admin/KelolaPeminjaman.vue`, `User/RiwayatPeminjaman.vue`, dua PDF laporan | P7 — `lib/status.js` |

### Catatan lingkungan (bukan bug kode)

| # | Temuan | Keterangan |
| :-- | :--- | :--- |
| T-18 | `phpunit.xml` semula menonaktifkan baris SQLite, sehingga test berjalan di atas database MySQL `sipinjam` yang sama dengan pengembangan. Pada P0 baris tersebut diaktifkan kembali (SQLite in-memory) agar suite dapat berjalan di CI tanpa layanan MySQL. Ini aman **karena** B-15 sudah menghapus SQL bercabang per driver. Bila nanti ada kueri khusus MySQL, CI perlu menambahkan service MySQL. | `phpunit.xml` |
| T-19 | Empat test Breeze berstatus *skipped* (registrasi dinonaktifkan). Bukan kegagalan, tetapi sebaiknya dihapus atau ditandai eksplisit agar laporan test bersih. | `tests/Feature/Auth/RegistrationTest.php` |

---

## Tindak lanjut P0 (6 September 2026)

### T-20 — Hook `saving()` tidak menangkap mass update dan insert

**Fase tujuan: P1**

Derivasi `mulai_at`/`selesai_at` dipasang lewat `static::saving()` pada
`app/Models/Peminjaman.php`. Hook model Eloquent **tidak** dijalankan oleh:

- `Peminjaman::where(...)->update([...])` — mass update lewat query builder
- `Peminjaman::insert([...])` — insert massal
- `upsert()`, serta perubahan langsung lewat `DB::table('peminjamans')`

Artinya, baris yang tanggal/jamnya diubah lewat salah satu jalur di atas akan
punya `mulai_at`/`selesai_at` **basi**, dan pengecekan bentrok jadwal memakai
nilai lama itu tanpa memberi peringatan apa pun.

**Saat ini masih aman**, karena satu-satunya mass update yang ada hanya
menyentuh `status` dan `alasan_sistem`:

- `BookingService::approveBooking()` — auto-reject stok habis
- `AutoRejectPendingBookings` — pembatalan SLA 48 jam

Keduanya tidak menyentuh kolom waktu, sehingga tidak ada kolom yang menjadi
basi. Risikonya bersifat **laten**: begitu ada kode baru yang mengubah
`tanggal_mulai`/`jam_mulai` secara massal — misalnya fitur "geser jadwal
seluruh kegiatan" — bug ini muncul diam-diam tanpa test yang gagal.

**Penyelesaian di P1**: masalahnya hilang dengan sendirinya. `pengajuan_items`
memakai kolom `mulai_at`/`selesai_at` bertipe datetime **sejak awal**, tanpa
kolom `tanggal_*`/`jam_*` terpisah. Tidak ada lagi nilai turunan yang bisa
basi, sehingga hook derivasi ini dapat dihapus bersama tabel `peminjamans`.

**Sampai P1 selesai**: setiap mass update yang menyentuh kolom waktu wajib ikut
menulis `mulai_at`/`selesai_at` secara eksplisit.

### T-21 — Suite test tidak pernah menyentuh MySQL, padahal produksi memakai MySQL

**Fase tujuan: P8**

Sejak P0, `phpunit.xml` memakai SQLite in-memory (lihat T-18). Konsekuensinya
seluruh suite — 51 test — **tidak pernah** dijalankan melawan MySQL, padahal
itulah database produksi (`DB_CONNECTION=mysql` di `.env.example`).

Perbedaan perilaku yang tidak akan tertangkap:

| Aspek | SQLite (test) | MySQL (produksi) |
| :--- | :--- | :--- |
| `lockForUpdate()` | `SQLiteGrammar::compileLock()` mengembalikan string kosong — **tidak menghasilkan SQL apa pun** | menghasilkan `FOR UPDATE`, mengunci baris sungguhan |
| `enum` | disimpan sebagai `varchar` + check constraint; nilai di luar daftar bisa lolos | ditolak database, atau dipotong pada mode non-strict |
| Strict mode | tidak ada padanannya | `ONLY_FULL_GROUP_BY` dapat menolak `groupBy` pada `ReportController` |
| Isolasi transaksi | serialized, satu penulis | REPEATABLE READ; deadlock nyata mungkin terjadi |
| Panjang & collation string | longgar | `utf8mb4` + batas panjang ditegakkan |

Dampak paling tajam ada pada `BookingRaceConditionTest`: test itu **tidak lagi
menguji locking sama sekali**, karena `lockForUpdate()` menjadi no-op di SQLite.
Yang masih teruji hanyalah logika bisnisnya (pengurangan stok + auto-reject).

**Penyelesaian di P8** — jalankan suite melawan MySQL sungguhan sebelum rilis:

1. Tambahkan `services: mysql:8` ke `.github/workflows/ci.yml` dengan
   health-check, lalu jalankan satu job matriks tambahan memakai
   `DB_CONNECTION=mysql`. Pertahankan job SQLite sebagai lintasan cepat.
2. Tambahkan test konkurensi sungguhan untuk `approveBooking()` memakai dua
   koneksi database terpisah, sehingga `FOR UPDATE` benar-benar diuji.
   Test ini wajib ditandai agar dilewati pada driver yang tidak mendukung
   penguncian baris.
3. Jalankan `php artisan migrate:fresh --seed` melawan MySQL di CI untuk
   membuktikan seluruh migrasi (termasuk backfill B-15) berjalan di sana.

### T-22 — `/verify-email` dan `/confirm-password` error 500 (SELESAI: dihapus)

**Tingkat keparahan: RENDAH — perancah mati, bukan blocker rilis.**
**Status: selesai pada sesi lanjutan P0 (6 September 2026) lewat penghapusan.**

Awalnya dicatat sebagai "prioritas tinggi / B-01 yang terlewat". Setelah
diverifikasi, penilaian itu **terlalu tinggi** dan sudah diturunkan.

Fakta yang ditemukan: kedua controller memang memanggil view Blade yang tidak
ada (`auth.verify-email`, `auth.confirm-password`) dan benar-benar
mengembalikan HTTP 500 — tetapi **tidak ada jalan menuju ke sana**:

| Verifikasi | Hasil |
| :--- | :--- |
| `App\Models\User` meng-implement `MustVerifyEmail`? | **Tidak** — baris 5 dikomentari, tidak ada klausa `implements` |
| Middleware `password.confirm` dipasang di suatu rute? | **Tidak** — `route:list` menunjukkan 0 rute memakainya |
| Ada tautan dari frontend? | **Tidak** — 0 hasil di `resources/js/` |

Karena `User` tidak meng-implement kontrak `MustVerifyEmail`, middleware
`verified` tidak pernah mengalihkan siapa pun ke `verification.notice`
(lihat T-23). Jadi kedua rute hanya dapat dicapai dengan mengetik URL-nya
langsung — perancah Breeze yang mati, bukan bug pada alur pengguna.

**Penyelesaian yang diambil**: menghapus, bukan membuat halaman Inertia baru.
Membangun halaman untuk alur yang tidak aktif hanya menambah kode yang harus
dirawat, dan P2 akan merancang ulang alur verifikasi dari awal.

Dihapus:

- `EmailVerificationPromptController`, `VerifyEmailController`,
  `EmailVerificationNotificationController`, `ConfirmablePasswordController`
- 5 definisi rute di `routes/auth.php` beserta 4 baris `use` yang menggantung
- `tests/Feature/Auth/EmailVerificationTest.php` dan
  `tests/Feature/Auth/PasswordConfirmationTest.php` — **berkas utuh**, karena
  seluruh 6 test di dalamnya menyentuh rute yang dihapus (bukan hanya 2 yang
  berstatus *skipped*; 4 sisanya justru berstatus lolos)

Dipertahankan: `PasswordController` (PUT `/password`, dipakai halaman profil),
`logout`, serta seluruh alur login dan reset kata sandi.

**Pembangunan ulangnya ada di T-23**, sebagai bagian dari P2.

### T-23 — Middleware `verified` terpasang tetapi tidak berefek

**Fase tujuan: P2** — blocker nyata untuk kelayakan meminjam.

`routes/web.php:28` menjaga seluruh rute pengguna dengan
`['auth', 'verified', 'blocked']`. Middleware `verified` **tidak melakukan
apa pun**, sehingga aplikasi *tampak* mewajibkan verifikasi email padahal sama
sekali tidak. Ini keamanan semu: pembaca `routes/web.php` akan menyimpulkan
email sudah pasti terverifikasi, lalu menulis kode di atas asumsi yang salah.

**Akar masalahnya: trait ada, kontrak tidak.**

`Illuminate\Foundation\Auth\User` memakai **trait** `Illuminate\Auth\MustVerifyEmail`
(baris 19), sehingga `hasVerifiedEmail()` dan `markEmailAsVerified()` tetap
tersedia dan berfungsi — itulah sebabnya test `email can be verified` dulu
lolos, sehingga masalah ini tidak pernah terlihat.

Namun `EnsureEmailIsVerified` memeriksa **kontraknya**:

```php
if (! $request->user() ||
    ($request->user() instanceof MustVerifyEmail &&   // Illuminate\Contracts\Auth\MustVerifyEmail
    ! $request->user()->hasVerifiedEmail())) {
```

`App\Models\User` tidak meng-implement kontrak itu (`app/Models/User.php:5`
masih dikomentari), sehingga syaratnya tidak pernah terpenuhi dan middleware
selalu meneruskan permintaan.

**Mengapa menjadi blocker di P2**: §5.3.1 mensyaratkan "akun terverifikasi
(email kampus) **dan** nomor WA terverifikasi" sebagai aturan kelayakan
pertama. `EligibilityService` tidak dapat bersandar pada `verified` yang mati.

**Konsekuensi penghapusan T-22 yang wajib diperhatikan**: rute
`verification.notice` sudah tidak ada. Begitu `MustVerifyEmail` di-implement,
`EnsureEmailIsVerified` akan memanggil `URL::route('verification.notice')` dan
melempar `RouteNotFoundException` — bukan lagi 500 pada satu halaman
verifikasi, melainkan galat pada **setiap** rute pengguna. Karena itu
perubahannya tidak boleh ditambal sebagian.

**Urutan pengerjaan di P2 (satu paket, jangan dipisah):**

1. Buat halaman Inertia `Pages/Auth/VerifyEmail.vue` beserta controllernya, dan
   daftarkan ulang rute bernama `verification.notice`, `verification.verify`,
   dan `verification.send`.
2. Baru setelah itu aktifkan `implements MustVerifyEmail` pada `App\Models\User`.
3. Satukan dengan verifikasi WhatsApp (OTP) dalam satu alur onboarding sesuai
   §5.4.3 — pengguna tidak diminta memverifikasi dua kali di dua tempat berbeda.
4. Putuskan perlakuan untuk akun lama: setiap `users.email_verified_at` yang
   masih NULL akan langsung terkunci begitu langkah 2 aktif. Siapkan migrasi
   data yang menandai akun Google sebagai terverifikasi (domainnya sudah
   diperiksa saat login, lihat B-07), atau kirim tautan verifikasi massal.

### T-24 — Ekspansi peran & permission granular yang ditunda ke P1

**Fase tujuan: P1 butir 5.**

Sesi ini menyatukan otorisasi ke Spatie: 8 FormRequest memakai
`can('master.manage')`, kolom `users.role` dihapus, `BookingPolicy` yang mati
dibuang, dan matriks peran × rute menjaganya. Yang **sengaja ditunda** karena
bergantung pada model data P1:

**1. Enam peran belum dibuat.** Sekarang masih `admin` dan `user`. P1 menambah
`mahasiswa`, `dosen`, `staff`, `staf_aset`, `pimpinan`, plus migrasi data
`user` → `mahasiswa`. Tempat mengubahnya sudah disiapkan:

- `database/seeders/RolePermissionSeeder.php` — konstanta `ROLES` dan `PERMISSIONS`
- `config/sipinjam.php` → `peran.peminjam` — tambahkan `mahasiswa`, `dosen`,
  `staff` di sini dan laporan serta seeder ikut benar tanpa mengedit controller
- `AdminController` memvalidasi peran lewat `Rule::exists('roles','name')`,
  jadi peran baru langsung dapat dipilih di panel admin

**2. Permission granular P1 belum ada.** Saat ini hanya `master.manage`, karena
hanya itu yang benar-benar dievaluasi kode. P1 menambah `pengajuan.create`,
`pengajuan.verify`, `pengajuan.approve`, `pengajuan.reject`,
`serahterima.create`, `pelanggaran.create`, `sanksi.manage`, dst.

**3. `master.manage` kemungkinan perlu dipecah.** Satu permission ini kini
menjaga empat domain sekaligus: Barang, Ruangan (aset) dan Banner, Kalender
(konten). Begitu `staf_aset` ada — yang seharusnya mengelola aset tetapi bukan
banner — pecah menjadi `master.aset.manage` dan `master.konten.manage`.
Pemecahannya cukup menyentuh seeder dan 8 FormRequest, tidak menyebar.

**4. `IsAdmin` mengalihkan, bukan melempar 403.** `app/Http/Middleware/IsAdmin.php:18`
memulangkan non-admin ke `/dashboard`. Matriks peran × rute mengasersi perilaku
itu apa adanya. Bila 403 yang diinginkan (lebih jujur untuk kegagalan izin, dan
lebih mudah dibedakan dari bug UI), itu perubahan perilaku tersendiri — ubah
bersamaan dengan Policy P1 supaya pengalaman penolakan konsisten di satu tempat.

**5. Toggle peran kosmetik di halaman login.** `resources/js/Pages/Auth/Login.vue`
punya `form.role` yang dikirim ke `/login` tetapi **tidak pernah dibaca server**
(`LoginRequest` tidak punya aturan untuk `role`). Tidak berbahaya, tetapi
menyesatkan: pengguna mengira sedang memilih cara masuk. Hapus, atau ubah
menjadi petunjuk yang jujur, saat P7 merapikan UI autentikasi.

### T-25 — Tabel `settings` sengaja tidak dibuat di P1

**Fase tujuan: P5 atau P8, saat ada yang benar-benar membutuhkannya.**

§6.1 mendaftarkan tabel `settings` sebagai "key-value untuk kop surat, penanda
tangan, jam operasional, denda". Seluruh nilai itu **sudah** tinggal di
`config/sipinjam.php` sejak P0, dibaca template surat, dan dijaga pagar
placeholder + test yang membuktikan perubahan config mengubah isi PDF.

Membuatnya di P1 berarti membangun sumber kebenaran kedua untuk nilai yang sama
— kelas bug yang persis dihapus dua kali berturut-turut pada sesi sebelumnya
(kolom `users.role`, lalu pemeriksaan peran di 8 FormRequest). Tidak ada satu
pun kode P1 yang akan membacanya.

**Kapan dibuat**: begitu ada kebutuhan nyata akan setelan yang dapat diubah
tanpa deploy — template checklist pemeriksaan di P5, atau panel konfigurasi
admin di P8.

**Syarat saat dibuat nanti**: `settings` harus **menimpa** config secara
eksplisit lewat satu pembaca tunggal (mis. `Setting::get($kunci, config($kunci))`),
bukan berdampingan sebagai sumber sejajar.

### T-26 — Dua definisi "terblokir" yang berbeda di enam titik

**Fase tujuan: P5** (bersama `SanksiService`). Pra-ada, bukan akibat P1.

Status blokir diperiksa dengan dua cara yang **tidak setara**:

| Cara | Menghormati `blocked_until`? | Titik |
| :--- | :--- | :--- |
| `$user->isBlocked()` | **Ya** | `CheckUserBlocked:20`, `AdminController:421`, `ApplySanctionPenalties:61` |
| kolom mentah `is_blocked` | **Tidak** | `LoginRequest:55`, `StoreBookingRequest:12`, `BookingService:23` |

Akibatnya pada data bawaan `UserSeeder`: dua akun "terblokir" dibuat dengan
`is_blocked = true` tetapi **`blocked_until` tidak pernah diisi**. Keduanya:

- **ditolak** saat login dan saat mengajukan (jalur kolom mentah), tetapi
- **tidak dianggap terblokir** oleh middleware dan scheduler (jalur `isBlocked()`,
  yang mensyaratkan `blocked_until` terisi)

Jadi masa blokirnya efektif **selamanya** — tidak ada tanggal berakhir, dan
`ApplySanctionPenalties::unblockExpiredUsers()` tidak akan pernah membebaskannya
karena kueri itu mensyaratkan `blocked_until` tidak null.

Terkunci oleh test: `tests/Unit/UserBlockingTest.php` mengasersi perilaku ini apa
adanya, supaya perubahannya di P5 ketahuan alih-alih diam-diam membebaskan atau
mengunci akun.

**Penyelesaian di P5**: jadikan `SanksiService` penulis satu-satunya, ubah keenam
titik menjadi `isBlocked()` saja, lalu hapus jalur mundur kolom pada
`User::isBlocked()` (lihat TODO di berkas itu).

---

## Status temuan setelah P1 (7 September 2026)

| Temuan | Status |
| :--- | :--- |
| **T-02** `generateNomorSurat()` memakai `COUNT(*)` | **SELESAI** — diganti `NomorSuratService` + tabel `nomor_counters`; metode lama dihapus, 9 pemanggil dialihkan |
| **T-17** model `TataTertib` kosong tanpa tabel | **SELESAI** — dihapus, digantikan `TataTertibVersion` yang berversi |
| **T-25** tabel `settings` | **DIPUTUSKAN** — tidak dibuat di P1, lihat alasannya di atas |
| T-01 `Ruangan::$appends` N+1 | **DITUNDA ke P3** — lihat T-27 |
| T-03/T-04/T-05 kolom & migrasi lama | **DITUNDA ke P3** — menyatu dengan rename `peminjamans_legacy` |
| T-06/T-07/T-08 otorisasi | **SELESAI di sesi sebelumnya** (T-08) / **P2** (T-07) / **P1 Tahap 3** (T-06 sebagian: policy sudah ada, middleware rute belum diubah) |
| T-26 dua definisi "terblokir" | **BERTAMBAH JELAS** — `isBlocked()` kini membaca tabel `sanksi` dengan jalur mundur; penuntasannya tetap di P5 |

### T-27 — `Ruangan::$appends = ['is_terpakai']` masih memicu N+1

**Fase tujuan: P3.** Ditunda dari P1 dengan alasan yang disengaja.

Accessor `getIsTerpakaiAttribute()` menjalankan satu kueri per baris, sehingga
setiap serialisasi koleksi ruangan memicu N+1. `AvailabilityService` sudah siap
menggantikannya (`jadwalTerpakai()`), **tetapi** atribut `is_terpakai` dibaca
langsung oleh empat halaman Vue:

- `Pages/Admin/KelolaRuangan.vue:171`
- `Pages/Landing.vue:233`
- `Pages/User/Dashboard.vue:468`
- `Pages/User/Ruangan.vue:111`

Menggantinya di P1 berarti menyentuh UI, yang dilarang oleh aturan fase ini
("tidak ada pekerjaan UI selain menjaga halaman lama tetap hidup"), dan
accessor lama masih membaca tabel `peminjamans` yang memang belum pensiun.

**Kerjakan di P3**, saat katalog aset ditulis ulang: buang `$appends`, ganti
dengan scope `withExists` pada keempat controller, dan alihkan sumber datanya ke
`AvailabilityService`.

### T-28 — Kolom `stok_tersedia` kini punya dua makna

**Fase tujuan: P3.**

`AvailabilityService` sengaja memakai `barangs.stok_total`, bukan
`stok_tersedia`, karena ketersediaan dihitung dari irisan waktu — bukan dari
penghitung yang bergerak (itulah akar B-02).

Sementara itu `BookingService` yang lama **masih** memotong dan mengembalikan
`stok_tersedia` pada setiap approve/complete. Jadi selama P1–P2 kolom itu:

- **tetap menjadi sumber kebenaran** bagi jalur booking lama, dan
- **diabaikan sepenuhnya** oleh jalur ketersediaan baru

Keduanya belum bertabrakan karena jalur barunya belum dipakai UI mana pun.
Begitu P3 mengalihkan pengajuan ke jalur baru, `stok_tersedia` harus turun
pangkat menjadi cache yang di-recompute (§5.3.3), atau dihapus — jangan
dibiarkan menjadi sumber kebenaran ketiga.
