<?php

use App\Models\Barang;
use App\Models\Pelanggaran;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\Sanksi;
use App\Models\TataTertibVersion;
use App\Models\User;
use App\Services\EligibilityService;
use Illuminate\Support\Carbon;

/**
 * Aturan kelayakan §5.3.1 — mengembalikan DAFTAR ALASAN, bukan boolean,
 * supaya UI bisa menjelaskan kepada pengguna kenapa ia tertahan.
 */
function kelayakan(): EligibilityService
{
    return app(EligibilityService::class);
}

function peminjam(): User
{
    return User::factory()->peminjam()->create();
}

function asetBarang(int $stok = 5, array $attr = []): Barang
{
    return Barang::create(array_merge([
        'nama' => 'Proyektor', 'kode' => 'PRJ-'.fake()->unique()->numerify('###'),
        'stok_total' => $stok, 'stok_tersedia' => $stok, 'status' => 'tersedia',
    ], $attr));
}

function pengajuanAktifUntuk(User $u, int $berapa): void
{
    for ($i = 0; $i < $berapa; $i++) {
        Pengajuan::create([
            'kode' => 'SPJ-'.fake()->unique()->numerify('######'),
            'user_id' => $u->id,
            'jenis' => Pengajuan::JENIS_TUNGGAL,
            'status' => Pengajuan::STATUS_DIAJUKAN,
        ]);
    }
}

// ── Aturan 2: sanksi aktif ───────────────────────────────────────────────────

test('user tanpa halangan dinyatakan layak', function () {
    $hasil = kelayakan()->periksa(peminjam());

    expect($hasil->layak())->toBeTrue()
        ->and($hasil->alasan())->toBe([]);
});

test('user yang sedang kena sanksi ditolak dengan tanggal berakhirnya', function () {
    $u = peminjam();
    Sanksi::create([
        'user_id' => $u->id, 'jenis' => Sanksi::JENIS_BLOKIR,
        'mulai_at' => now()->subDay(), 'sampai_at' => now()->addDays(7),
        'alasan' => 'Terlambat',
    ]);

    $hasil = kelayakan()->periksa($u->fresh());

    expect($hasil->layak())->toBeFalse()
        ->and($hasil->pesan())->toContain('diblokir')
        ->and($hasil->pesan())->toContain(now()->addDays(7)->translatedFormat('d F Y'));
});

// ── Aturan 3: kuota pengajuan aktif ──────────────────────────────────────────

test('kuota diambil dari peran pengguna', function () {
    config()->set('sipinjam.kuota_aktif', ['mahasiswa' => 2, 'default' => 9]);

    expect(kelayakan()->kuotaUntuk(peminjam()))->toBe(2);
});

test('tiap peran peminjam punya kuotanya sendiri', function () {
    config()->set('sipinjam.kuota_aktif', ['mahasiswa' => 3, 'dosen' => 5, 'staff' => 5, 'default' => 1]);

    expect(kelayakan()->kuotaUntuk(User::factory()->berperan('mahasiswa')->create()))->toBe(3)
        ->and(kelayakan()->kuotaUntuk(User::factory()->berperan('dosen')->create()))->toBe(5)
        ->and(kelayakan()->kuotaUntuk(User::factory()->berperan('staff')->create()))->toBe(5);
});

test('peran tanpa kuota khusus memakai nilai default', function () {
    config()->set('sipinjam.kuota_aktif', ['dosen' => 5, 'default' => 3]);

    expect(kelayakan()->kuotaUntuk(peminjam()))->toBe(3);
});

test('melebihi kuota pengajuan aktif menahan pengguna', function () {
    config()->set('sipinjam.kuota_aktif', ['default' => 2]);
    $u = peminjam();
    pengajuanAktifUntuk($u, 2);

    $hasil = kelayakan()->periksa($u);

    expect($hasil->layak())->toBeFalse()
        ->and($hasil->pesan())->toContain('batas 2');
});

test('pengajuan berstatus draf dan yang sudah tuntas tidak dihitung kuota', function () {
    config()->set('sipinjam.kuota_aktif', ['default' => 1]);
    $u = peminjam();

    foreach ([Pengajuan::STATUS_DRAFT, Pengajuan::STATUS_SELESAI, Pengajuan::STATUS_DITOLAK] as $st) {
        Pengajuan::create([
            'kode' => 'SPJ-'.fake()->unique()->numerify('######'),
            'user_id' => $u->id, 'jenis' => Pengajuan::JENIS_TUNGGAL, 'status' => $st,
        ]);
    }

    expect(kelayakan()->periksa($u)->layak())->toBeTrue();
});

// ── Aturan 4: tunggakan ──────────────────────────────────────────────────────

test('tunggakan ganti rugi menahan pengajuan baru', function () {
    $u = peminjam();
    $p = Pengajuan::create([
        'kode' => 'SPJ-'.fake()->unique()->numerify('######'),
        'user_id' => $u->id, 'jenis' => Pengajuan::JENIS_TUNGGAL, 'status' => Pengajuan::STATUS_SELESAI,
    ]);
    $b = asetBarang();
    $item = PengajuanItem::create([
        'pengajuan_id' => $p->id, 'assetable_type' => Barang::class, 'assetable_id' => $b->id,
        'jumlah' => 1, 'mulai_at' => now()->subDays(5), 'selesai_at' => now()->subDays(4),
        'status_item' => PengajuanItem::STATUS_SELESAI,
    ]);

    $pel = Pelanggaran::create([
        'user_id' => $u->id, 'pengajuan_item_id' => $item->id,
        'jenis' => 'rusak_berat', 'poin' => 5,
        'status_tindak_lanjut' => Pelanggaran::TINDAK_MENUNGGU,
    ]);

    expect(kelayakan()->periksa($u)->pesan())->toContain('belum Anda selesaikan');

    $pel->update(['status_tindak_lanjut' => Pelanggaran::TINDAK_LUNAS]);

    expect(kelayakan()->periksa($u->fresh())->layak())->toBeTrue();
});

// ── Aturan 1 & 6: sakelar config ─────────────────────────────────────────────

test('verifikasi WA hanya menahan bila sakelarnya dinyalakan', function () {
    $u = peminjam();

    expect(kelayakan()->periksa($u)->layak())->toBeTrue();

    config()->set('sipinjam.kelayakan.wajib_phone_terverifikasi', true);

    expect(kelayakan()->periksa($u)->pesan())->toContain('WhatsApp');
});

test('persetujuan tata tertib hanya menahan bila sakelarnya dinyalakan', function () {
    config()->set('sipinjam.kelayakan.wajib_tata_tertib_disetujui', true);
    TataTertibVersion::create([
        'versi' => '1.0', 'konten' => 'Isi tata tertib', 'berlaku_sejak' => now()->subDay(),
    ]);

    expect(kelayakan()->periksa(peminjam())->pesan())->toContain('tata tertib');
});

test('tanpa tata tertib terbit, aturannya tidak menahan siapa pun', function () {
    config()->set('sipinjam.kelayakan.wajib_tata_tertib_disetujui', true);

    expect(kelayakan()->periksa(peminjam())->layak())->toBeTrue();
});

// ── Kelayakan per baris aset ─────────────────────────────────────────────────

test('lead time kurang ditolak dengan pesan H-berapa', function () {
    Carbon::setTestNow('2026-10-12 08:00:00');

    config()->set('sipinjam.lead_time.barang_hari', 3);
    $b = asetBarang();

    $hasil = kelayakan()->periksaAset(
        peminjam(), $b,
        Carbon::parse('2026-10-12 13:00'), Carbon::parse('2026-10-12 17:00')
    );

    expect($hasil->layak())->toBeFalse()
        ->and($hasil->pesan())->toContain('H-3');

    Carbon::setTestNow();
});

test('lead time per aset menimpa nilai config', function () {
    // Waktu dibekukan supaya rentangnya selalu jatuh di dalam jam operasional —
    // tanpa ini hasilnya bergantung pada jam berapa suite dijalankan.
    Carbon::setTestNow('2026-10-12 08:00:00');

    config()->set('sipinjam.lead_time.barang_hari', 30);
    config()->set('sipinjam.jam_operasional', ['buka' => '07:00', 'tutup' => '22:00']);

    $b = asetBarang(5, ['min_lead_time_jam' => 1]);

    $hasil = kelayakan()->periksaAset(
        peminjam(), $b,
        Carbon::parse('2026-10-12 13:00'), Carbon::parse('2026-10-12 17:00')
    );

    expect($hasil->layak())->toBeTrue();

    Carbon::setTestNow();
});

test('di luar jam operasional ditolak', function () {
    config()->set('sipinjam.jam_operasional', ['buka' => '07:00', 'tutup' => '22:00']);
    config()->set('sipinjam.lead_time.barang_hari', 0);
    $b = asetBarang();

    $hasil = kelayakan()->periksaAset(
        peminjam(), $b,
        Carbon::parse('2026-10-12 05:00'), Carbon::parse('2026-10-12 06:00')
    );

    expect($hasil->pesan())->toContain('07:00–22:00');
});

test('stok tidak cukup menyebut angka tersedia dan diminta', function () {
    config()->set('sipinjam.lead_time.barang_hari', 0);
    $b = asetBarang(2);

    $hasil = kelayakan()->periksaAset(
        peminjam(), $b,
        Carbon::parse('2026-10-12 08:00'), Carbon::parse('2026-10-12 12:00'), 5
    );

    expect($hasil->pesan())->toContain('hanya tersedia 2 dari 5 unit');
});

test('aset yang dibatasi peran menolak peran lain', function () {
    config()->set('sipinjam.lead_time.barang_hari', 0);
    $b = asetBarang(5, ['role_diizinkan' => ['dosen']]);

    $hasil = kelayakan()->periksaAset(
        peminjam(), $b,
        Carbon::parse('2026-10-12 08:00'), Carbon::parse('2026-10-12 12:00')
    );

    expect($hasil->pesan())->toContain('tidak tersedia untuk peran Anda');
});

test('waktu selesai sebelum mulai langsung ditolak', function () {
    $b = asetBarang();

    $hasil = kelayakan()->periksaAset(
        peminjam(), $b,
        Carbon::parse('2026-10-12 12:00'), Carbon::parse('2026-10-12 08:00')
    );

    expect($hasil->pesan())->toContain('setelah waktu mulai');
});

test('beberapa aturan gagal sekaligus menghasilkan beberapa alasan', function () {
    config()->set('sipinjam.kuota_aktif', ['default' => 1]);
    config()->set('sipinjam.kelayakan.wajib_phone_terverifikasi', true);

    $u = peminjam();
    pengajuanAktifUntuk($u, 1);

    expect(kelayakan()->periksa($u)->alasan())->toHaveCount(2);
});
