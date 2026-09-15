<?php

use App\Models\Pelanggaran;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\Ruangan;
use App\Models\Sanksi;
use App\Models\SerahTerima;
use App\Models\User;
use App\Policies\PelanggaranPolicy;
use App\Policies\PengajuanPolicy;
use App\Policies\SanksiPolicy;
use App\Policies\SerahTerimaPolicy;
use Illuminate\Support\Facades\Gate;

/**
 * Policy per peran.
 *
 * Yang paling penting dibuktikan di sini: SOP kampus memisahkan siapa yang
 * MEMVERIFIKASI ketersediaan dari siapa yang MENYETUJUI. Satu tombol yang
 * merangkap keduanya tidak dapat merepresentasikan SOP nyata.
 */
function userPeran(string $peran): User
{
    return User::factory()->berperan($peran)->create();
}

function pengajuanMilik(User $pemohon, string $status = Pengajuan::STATUS_DIAJUKAN): Pengajuan
{
    return Pengajuan::create([
        'kode' => 'SPJ-'.fake()->unique()->numerify('######'),
        'user_id' => $pemohon->id,
        'jenis' => Pengajuan::JENIS_TUNGGAL,
        'status' => $status,
    ]);
}

// ── PengajuanPolicy ──────────────────────────────────────────────────────────

test('pemohon melihat pengajuannya sendiri, orang lain tidak', function () {
    $pemohon = userPeran('mahasiswa');
    $orangLain = userPeran('mahasiswa');
    $p = pengajuanMilik($pemohon);

    expect($pemohon->can('view', $p))->toBeTrue()
        ->and($orangLain->can('view', $p))->toBeFalse();
});

test('staf aset dan pimpinan melihat seluruh pengajuan', function () {
    $p = pengajuanMilik(userPeran('mahasiswa'));

    expect(userPeran('staf_aset')->can('view', $p))->toBeTrue()
        ->and(userPeran('pimpinan')->can('view', $p))->toBeTrue();
});

test('hanya peran peminjam yang boleh membuat pengajuan', function () {
    expect(userPeran('mahasiswa')->can('create', Pengajuan::class))->toBeTrue()
        ->and(userPeran('dosen')->can('create', Pengajuan::class))->toBeTrue()
        ->and(userPeran('staff')->can('create', Pengajuan::class))->toBeTrue()
        ->and(userPeran('staf_aset')->can('create', Pengajuan::class))->toBeFalse()
        ->and(userPeran('pimpinan')->can('create', Pengajuan::class))->toBeFalse();
});

test('PEMISAHAN KEWENANGAN: staf aset memverifikasi, pimpinan menyetujui', function () {
    $stafAset = userPeran('staf_aset');
    $pimpinan = userPeran('pimpinan');

    $diajukan = pengajuanMilik(userPeran('mahasiswa'), Pengajuan::STATUS_DIAJUKAN);
    $diverifikasi = pengajuanMilik(userPeran('mahasiswa'), Pengajuan::STATUS_DIVERIFIKASI);

    // Staf aset boleh verifikasi, TIDAK boleh menyetujui.
    expect($stafAset->can('verify', $diajukan))->toBeTrue()
        ->and($stafAset->can('approve', $diverifikasi))->toBeFalse();

    // Pimpinan boleh menyetujui, TIDAK boleh verifikasi.
    expect($pimpinan->can('approve', $diverifikasi))->toBeTrue()
        ->and($pimpinan->can('verify', $diajukan))->toBeFalse();
});

test('verifikasi hanya sah pada status diajukan', function () {
    $stafAset = userPeran('staf_aset');

    expect($stafAset->can('verify', pengajuanMilik(userPeran('mahasiswa'), Pengajuan::STATUS_DIAJUKAN)))->toBeTrue()
        ->and($stafAset->can('verify', pengajuanMilik(userPeran('mahasiswa'), Pengajuan::STATUS_SELESAI)))->toBeFalse();
});

test('persetujuan hanya sah pada status diverifikasi', function () {
    $pimpinan = userPeran('pimpinan');

    expect($pimpinan->can('approve', pengajuanMilik(userPeran('mahasiswa'), Pengajuan::STATUS_DIVERIFIKASI)))->toBeTrue()
        ->and($pimpinan->can('approve', pengajuanMilik(userPeran('mahasiswa'), Pengajuan::STATUS_DIAJUKAN)))->toBeFalse();
});

test('pemohon membatalkan miliknya selama belum tuntas', function () {
    $pemohon = userPeran('mahasiswa');

    expect($pemohon->can('cancel', pengajuanMilik($pemohon, Pengajuan::STATUS_DIAJUKAN)))->toBeTrue()
        ->and($pemohon->can('cancel', pengajuanMilik($pemohon, Pengajuan::STATUS_SELESAI)))->toBeFalse();
});

test('menyunting hanya selama masih draf', function () {
    $pemohon = userPeran('mahasiswa');

    expect($pemohon->can('update', pengajuanMilik($pemohon, Pengajuan::STATUS_DRAFT)))->toBeTrue()
        ->and($pemohon->can('update', pengajuanMilik($pemohon, Pengajuan::STATUS_DIAJUKAN)))->toBeFalse();
});

// ── PelanggaranPolicy ────────────────────────────────────────────────────────

test('pelaku berhak melihat pelanggarannya dan mengajukan keberatan', function () {
    $pelaku = userPeran('mahasiswa');
    $p = pengajuanMilik($pelaku, Pengajuan::STATUS_SELESAI);
    $r = Ruangan::create(['nama' => 'Aula', 'kode' => 'A-1', 'kapasitas' => 10, 'status' => 'tersedia']);

    $item = PengajuanItem::create([
        'pengajuan_id' => $p->id, 'assetable_type' => Ruangan::class, 'assetable_id' => $r->id,
        'jumlah' => 1, 'mulai_at' => now()->subDay(), 'selesai_at' => now(),
        'status_item' => PengajuanItem::STATUS_SELESAI,
    ]);

    $pel = Pelanggaran::create([
        'user_id' => $pelaku->id, 'pengajuan_item_id' => $item->id,
        'jenis' => 'tidak_bersih', 'poin' => 2,
        'status_tindak_lanjut' => Pelanggaran::TINDAK_MENUNGGU,
    ]);

    expect($pelaku->can('view', $pel))->toBeTrue()
        ->and($pelaku->can('ajukanKeberatan', $pel))->toBeTrue()
        ->and(userPeran('mahasiswa')->can('view', $pel))->toBeFalse();

    $pel->update(['status_tindak_lanjut' => Pelanggaran::TINDAK_LUNAS]);

    expect($pelaku->can('ajukanKeberatan', $pel->fresh()))->toBeFalse();
});

test('hanya staf aset dan admin yang mencatat pelanggaran', function () {
    expect(userPeran('staf_aset')->can('create', Pelanggaran::class))->toBeTrue()
        ->and(userPeran('admin')->can('create', Pelanggaran::class))->toBeTrue()
        ->and(userPeran('mahasiswa')->can('create', Pelanggaran::class))->toBeFalse()
        ->and(userPeran('pimpinan')->can('create', Pelanggaran::class))->toBeFalse();
});

// ── SanksiPolicy ─────────────────────────────────────────────────────────────

test('setiap orang berhak melihat sanksi atas dirinya', function () {
    $kena = userPeran('mahasiswa');
    $s = Sanksi::create([
        'user_id' => $kena->id, 'jenis' => Sanksi::JENIS_BLOKIR,
        'mulai_at' => now(), 'sampai_at' => now()->addDays(7), 'alasan' => 'Uji',
    ]);

    expect($kena->can('view', $s))->toBeTrue()
        ->and(userPeran('mahasiswa')->can('view', $s))->toBeFalse()
        ->and(userPeran('admin')->can('view', $s))->toBeTrue();
});

test('hanya pemegang sanksi.manage yang menjatuhkan dan mencabut', function () {
    $s = Sanksi::create([
        'user_id' => userPeran('mahasiswa')->id, 'jenis' => Sanksi::JENIS_BLOKIR,
        'mulai_at' => now(), 'sampai_at' => now()->addDays(7), 'alasan' => 'Uji',
    ]);

    expect(userPeran('admin')->can('cabut', $s))->toBeTrue()
        ->and(userPeran('staf_aset')->can('cabut', $s))->toBeFalse()
        ->and(userPeran('mahasiswa')->can('create', Sanksi::class))->toBeFalse();
});

test('sanksi yang sudah dicabut tidak dapat dicabut dua kali', function () {
    $s = Sanksi::create([
        'user_id' => userPeran('mahasiswa')->id, 'jenis' => Sanksi::JENIS_BLOKIR,
        'mulai_at' => now(), 'sampai_at' => now()->addDays(7),
        'dicabut_at' => now(), 'alasan_pencabutan' => 'Keberatan diterima',
    ]);

    expect(userPeran('admin')->can('cabut', $s))->toBeFalse();
});

// ── Auto-discovery ───────────────────────────────────────────────────────────

test('policy ditemukan otomatis oleh Laravel (bukan kode mati seperti BookingPolicy)', function () {
    expect(Gate::getPolicyFor(Pengajuan::class))->toBeInstanceOf(PengajuanPolicy::class)
        ->and(Gate::getPolicyFor(Pelanggaran::class))->toBeInstanceOf(PelanggaranPolicy::class)
        ->and(Gate::getPolicyFor(Sanksi::class))->toBeInstanceOf(SanksiPolicy::class)
        ->and(Gate::getPolicyFor(SerahTerima::class))->toBeInstanceOf(SerahTerimaPolicy::class);
});
