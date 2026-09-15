<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Domain Email yang Diizinkan
    |--------------------------------------------------------------------------
    |
    | Hanya email dengan domain di daftar ini yang boleh masuk lewat Login
    | Google. Isi lewat SIPINJAM_ALLOWED_EMAIL_DOMAINS, dipisahkan koma.
    | Kosongkan daftar ini untuk mengizinkan semua domain (TIDAK disarankan
    | untuk produksi).
    |
    */

    'allowed_email_domains' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('SIPINJAM_ALLOWED_EMAIL_DOMAINS', 'stitek.ac.id'))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Pembuatan Akun Otomatis dari Login Google
    |--------------------------------------------------------------------------
    |
    | Bila true, pengguna dengan domain email yang diizinkan otomatis dibuatkan
    | akun saat pertama kali login lewat Google. Bila false, akun harus lebih
    | dulu dibuat oleh admin.
    |
    | Catatan: registrasi mandiri sudah dinonaktifkan (routes/auth.php), jadi
    | Login Google adalah satu-satunya jalur akun baru. Menyetel nilai ini ke
    | false berarti setiap akun wajib dibuat manual oleh admin.
    |
    */

    'auto_provision_google_users' => (bool) env('SIPINJAM_AUTO_PROVISION_GOOGLE_USERS', true),

    /*
    |--------------------------------------------------------------------------
    | Peran
    |--------------------------------------------------------------------------
    |
    | Peran Spatie yang dianggap "peminjam" — dipakai untuk menghitung jumlah
    | pengguna pada laporan dan untuk memilih calon peminjam pada seeder.
    |
    | P1 akan menambahkan 'mahasiswa', 'dosen', dan 'staff' ke daftar ini;
    | tidak ada controller yang perlu diubah saat itu terjadi.
    |
    */

    'peran' => [
        // Peran yang boleh mengajukan peminjaman. Dibaca ReportController,
        // PeminjamanSeeder, dan PeminjamanFactory.
        'peminjam' => ['mahasiswa', 'dosen', 'staff'],

        // Peran bawaan untuk akun baru dari Login Google. Sebagian besar
        // pendaftar adalah mahasiswa; dosen/staf dinaikkan admin lewat panel.
        'default_google' => 'mahasiswa',

        // UU PDP: hanya peran ini yang melihat nomor telepon secara penuh.
        // Peran lain mendapat bentuk tersamar (+62812****7890).
        'boleh_lihat_kontak' => ['admin', 'staf_aset'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Onboarding (P2)
    |--------------------------------------------------------------------------
    |
    | Bila wajib=true, pengguna dengan profil belum lengkap atau nomor WhatsApp
    | belum terverifikasi diarahkan ke /onboarding sebelum dapat memakai
    | aplikasi. Dapat dimatikan untuk staging.
    |
    */

    'onboarding' => [
        'wajib' => (bool) env('SIPINJAM_ONBOARDING_WAJIB', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Aturan Peminjaman (P1)
    |--------------------------------------------------------------------------
    |
    | Seluruh angka kebijakan tinggal di sini supaya kampus dapat menyesuaikan
    | tanpa menyentuh kode. Dibaca EligibilityService dan AvailabilityService.
    |
    */

    // Tenggat minimum pengajuan sebelum kegiatan dimulai.
    'lead_time' => [
        'event_hari_kerja' => 7,
        'ruangan_hari' => 2,
        'barang_hari' => 1,
    ],

    // Jumlah maksimum pengajuan aktif bersamaan, per peran.
    'kuota_aktif' => [
        'mahasiswa' => 3,
        'dosen' => 5,
        'staff' => 5,
        'default' => 3,
    ],

    'jam_operasional' => [
        'buka' => '07:00',
        'tutup' => '22:00',
    ],

    // Jeda bersih-bersih antara dua kegiatan di ruangan yang sama.
    'buffer_menit' => 30,

    'maks_item_per_pengajuan' => 20,

    /*
    | Aturan kelayakan yang dapat dimatikan sementara.
    |
    | Ketiganya bergantung pada fase yang belum berjalan, jadi dimatikan dulu
    | agar tidak mengunci seluruh pengguna sebelum alurnya ada.
    */
    'kelayakan' => [
        'wajib_phone_terverifikasi' => false,   // aktifkan di P2 (OTP WhatsApp)
        'wajib_email_terverifikasi' => false,   // aktifkan di P2 (lihat T-23)
        'wajib_tata_tertib_disetujui' => false, // aktifkan di P7 (onboarding)
    ],

    /*
    |--------------------------------------------------------------------------
    | Kop Surat
    |--------------------------------------------------------------------------
    |
    | Identitas lembaga yang tercetak pada bagian atas setiap dokumen resmi.
    | Tidak boleh ada nilai yang di-hardcode di berkas Blade mana pun.
    |
    */

    'kop' => [
        'yayasan' => env('SIPINJAM_KOP_YAYASAN', 'Yayasan Pendidikan Bessai Berinta'),
        'institusi' => env('SIPINJAM_KOP_INSTITUSI', 'Sekolah Tinggi Teknologi Bontang'),
        'alamat' => env('SIPINJAM_KOP_ALAMAT', 'Jl. Brigjend Katamso No.40, Bontang Utara, Kota Bontang, Kalimantan Timur 75313'),
        'website' => env('SIPINJAM_KOP_WEBSITE', 'stitek.ac.id'),
        'telepon' => env('SIPINJAM_KOP_TELEPON', '(0548) 22212'),
        'logo_path' => env('SIPINJAM_KOP_LOGO_PATH', 'logo.png'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Penanda Tangan Dokumen
    |--------------------------------------------------------------------------
    |
    | WAJIB diisi sebelum dokumen dipakai secara resmi. Nilai placeholder di
    | bawah sengaja dibuat mencolok agar ketahuan bila belum dikonfigurasi.
    |
    */

    'penandatangan' => [
        // Pejabat yang menandatangani Surat Izin Peminjaman.
        'pejabat' => [
            'nama' => env('SIPINJAM_TTD_PEJABAT_NAMA', '[ISI NAMA PEJABAT]'),
            'jabatan' => env('SIPINJAM_TTD_PEJABAT_JABATAN', '[ISI JABATAN PEJABAT]'),
            'nip' => env('SIPINJAM_TTD_PEJABAT_NIP', '[ISI NIP PEJABAT]'),
        ],

        // Pengelola aset / sarana prasarana yang memverifikasi ketersediaan.
        'pengelola_aset' => [
            'nama' => env('SIPINJAM_TTD_PENGELOLA_NAMA', '[ISI NAMA PENGELOLA ASET]'),
            'jabatan' => env('SIPINJAM_TTD_PENGELOLA_JABATAN', '[ISI JABATAN PENGELOLA ASET]'),
            'nip' => env('SIPINJAM_TTD_PENGELOLA_NIP', '[ISI NIP PENGELOLA ASET]'),
        ],

        // Pihak yang dituju pada surat permohonan/izin.
        'penerima_surat' => [
            'nama' => env('SIPINJAM_TTD_PENERIMA_NAMA', '[ISI NAMA PENERIMA SURAT]'),
            'jabatan' => env('SIPINJAM_TTD_PENERIMA_JABATAN', '[ISI JABATAN PENERIMA SURAT]'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Surat
    |--------------------------------------------------------------------------
    |
    | format_nomor menerima dua penanda: {urut} (3 digit, nol di depan) dan
    | {tahun} (4 digit). kota dipakai pada baris "Kota, tanggal".
    |
    */

    'surat' => [
        'format_nomor' => env('SIPINJAM_SURAT_FORMAT_NOMOR', '{urut}/INT/SIPINJAM/{tahun}'),
        'kota' => env('SIPINJAM_SURAT_KOTA', 'Bontang'),
        'zona_waktu' => env('SIPINJAM_SURAT_ZONA_WAKTU', 'WITA'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Perenderan PDF (Browsershot)
    |--------------------------------------------------------------------------
    |
    | Path binary Node & npm. Dibaca lewat config (bukan env() langsung di
    | dalam app/) agar tetap terbaca setelah `php artisan config:cache`.
    | Bila dikosongkan, PdfRenderer memakai tebakan berdasarkan sistem operasi.
    |
    */

    'pdf' => [
        'node_binary' => env('NODE_BINARY_PATH'),
        'npm_binary' => env('NPM_BINARY_PATH'),

        // Diperlukan bila PHP berjalan sebagai root di dalam container.
        'no_sandbox' => (bool) env('SIPINJAM_PDF_NO_SANDBOX', true),
    ],

];
