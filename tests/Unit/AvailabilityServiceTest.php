<?php

use App\Models\AssetBlackout;
use App\Models\Barang;
use App\Models\Pelanggaran;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\AvailabilityService;
use Illuminate\Support\Carbon;

/**
 * Ketersediaan berbasis waktu — inti perbaikan B-02.
 */
function svc(): AvailabilityService
{
    return app(AvailabilityService::class);
}

function barang(int $stok = 3): Barang
{
    return Barang::create([
        'nama' => 'Proyektor', 'kode' => 'PRJ-'.fake()->unique()->numerify('###'),
        'stok_total' => $stok, 'stok_tersedia' => $stok,
        'kategori' => 'Elektronik', 'status' => 'tersedia',
    ]);
}

function ruangan(?int $buffer = null): Ruangan
{
    return Ruangan::create([
        'nama' => 'Aula', 'kode' => 'AULA-'.fake()->unique()->numerify('###'),
        'kapasitas' => 200, 'status' => 'tersedia', 'buffer_menit' => $buffer,
    ]);
}

function pesan(Barang|Ruangan $aset, string $mulai, string $selesai, int $jumlah = 1, string $status = PengajuanItem::STATUS_DISETUJUI): PengajuanItem
{
    $p = Pengajuan::create([
        'kode' => 'SPJ-'.fake()->unique()->numerify('######'),
        'user_id' => User::factory()->peminjam()->create()->id,
        'jenis' => Pengajuan::JENIS_TUNGGAL,
        'status' => Pengajuan::STATUS_DISETUJUI,
    ]);

    return PengajuanItem::create([
        'pengajuan_id' => $p->id,
        'assetable_type' => $aset::class,
        'assetable_id' => $aset->id,
        'jumlah' => $jumlah,
        'mulai_at' => $mulai,
        'selesai_at' => $selesai,
        'status_item' => $status,
    ]);
}

// ── Irisan waktu ─────────────────────────────────────────────────────────────

test('stok penuh saat belum ada yang memesan', function () {
    expect(svc()->tersediaUntuk(barang(3), now()->addDays(5), now()->addDays(5)->addHours(4)))->toBe(3);
});

test('pesanan yang beririsan mengurangi ketersediaan', function () {
    $b = barang(3);
    pesan($b, '2026-10-12 08:00:00', '2026-10-12 17:00:00', 2);

    expect(svc()->tersediaUntuk($b, Carbon::parse('2026-10-12 10:00'), Carbon::parse('2026-10-12 12:00')))->toBe(1);
});

test('B-02: pesanan di rentang lain TIDAK mengurangi ketersediaan', function () {
    $b = barang(3);
    pesan($b, '2026-10-12 08:00:00', '2026-10-12 17:00:00', 3);

    // Rentang Oktober habis...
    expect(svc()->tersediaUntuk($b, Carbon::parse('2026-10-12 09:00'), Carbon::parse('2026-10-12 11:00')))->toBe(0);

    // ...tetapi Desember masih penuh. Inilah yang dulu salah.
    expect(svc()->tersediaUntuk($b, Carbon::parse('2026-12-20 08:00'), Carbon::parse('2026-12-20 17:00')))->toBe(3);
});

test('slot yang bersentuhan di ujung tidak dianggap beririsan', function () {
    $b = barang(1);
    pesan($b, '2026-10-12 08:00:00', '2026-10-12 12:00:00', 1);

    expect(svc()->tersediaUntuk($b, Carbon::parse('2026-10-12 12:00'), Carbon::parse('2026-10-12 15:00')))->toBe(1);
});

test('hanya status yang memesan slot yang dihitung', function () {
    $b = barang(2);
    // "diajukan" belum memesan apa pun — kalau ikut dihitung, antrean akan
    // saling memblokir tanpa alasan.
    pesan($b, '2026-10-12 08:00:00', '2026-10-12 17:00:00', 2, PengajuanItem::STATUS_DIAJUKAN);

    expect(svc()->tersediaUntuk($b, Carbon::parse('2026-10-12 10:00'), Carbon::parse('2026-10-12 12:00')))->toBe(2);
});

test('baris yang diabaikan tidak menghitung dirinya sendiri', function () {
    $b = barang(1);
    $item = pesan($b, '2026-10-12 08:00:00', '2026-10-12 17:00:00', 1);

    expect(svc()->tersediaUntuk($b, Carbon::parse('2026-10-12 09:00'), Carbon::parse('2026-10-12 11:00')))->toBe(0)
        ->and(svc()->tersediaUntuk($b, Carbon::parse('2026-10-12 09:00'), Carbon::parse('2026-10-12 11:00'), $item->id))->toBe(1);
});

// ── Ruangan: kapasitas 1 + buffer ────────────────────────────────────────────

test('ruangan berkapasitas 1 apa pun kapasitas kursinya', function () {
    expect(svc()->kapasitasTotal(ruangan()))->toBe(1);
});

test('buffer bersih-bersih mencegah dua kegiatan menempel persis', function () {
    $r = ruangan(30);
    pesan($r, '2026-10-12 08:00:00', '2026-10-12 12:00:00', 1);

    // 12:00 tepat setelah selesai — tertolak karena buffer 30 menit.
    expect(svc()->tersediaUntuk($r, Carbon::parse('2026-10-12 12:00'), Carbon::parse('2026-10-12 15:00')))->toBe(0);

    // 12:30 sudah di luar buffer.
    expect(svc()->tersediaUntuk($r, Carbon::parse('2026-10-12 12:30'), Carbon::parse('2026-10-12 15:00')))->toBe(1);
});

test('buffer per ruangan menimpa nilai config', function () {
    config()->set('sipinjam.buffer_menit', 30);
    $r = ruangan(0); // ruangan ini tidak butuh jeda
    pesan($r, '2026-10-12 08:00:00', '2026-10-12 12:00:00', 1);

    expect(svc()->tersediaUntuk($r, Carbon::parse('2026-10-12 12:00'), Carbon::parse('2026-10-12 15:00')))->toBe(1);
});

// ── Blackout ─────────────────────────────────────────────────────────────────

test('blackout membuat aset tidak tersedia sama sekali', function () {
    $b = barang(5);
    AssetBlackout::create([
        'assetable_type' => Barang::class, 'assetable_id' => $b->id,
        'mulai_at' => '2026-10-10 00:00:00', 'selesai_at' => '2026-10-15 00:00:00',
        'alasan' => 'pemeliharaan',
    ]);

    expect(svc()->tersediaUntuk($b, Carbon::parse('2026-10-12 08:00'), Carbon::parse('2026-10-12 10:00')))->toBe(0)
        ->and(svc()->tersediaUntuk($b, Carbon::parse('2026-10-20 08:00'), Carbon::parse('2026-10-20 10:00')))->toBe(5);
});

// ── Rusak / hilang ───────────────────────────────────────────────────────────

test('barang hilang yang belum diganti mengurangi stok lintas waktu', function () {
    $b = barang(3);
    $item = pesan($b, '2026-09-01 08:00:00', '2026-09-01 17:00:00', 1, PengajuanItem::STATUS_SELESAI);

    Pelanggaran::create([
        'user_id' => $item->pengajuan->user_id,
        'pengajuan_item_id' => $item->id,
        'jenis' => 'hilang',
        'poin' => 5,
        'status_tindak_lanjut' => Pelanggaran::TINDAK_MENUNGGU,
    ]);

    expect(svc()->tersediaUntuk($b, Carbon::parse('2026-12-01 08:00'), Carbon::parse('2026-12-01 10:00')))->toBe(2);
});

test('setelah ganti rugi lunas, stok kembali utuh', function () {
    $b = barang(3);
    $item = pesan($b, '2026-09-01 08:00:00', '2026-09-01 17:00:00', 1, PengajuanItem::STATUS_SELESAI);

    $p = Pelanggaran::create([
        'user_id' => $item->pengajuan->user_id,
        'pengajuan_item_id' => $item->id,
        'jenis' => 'rusak_berat',
        'poin' => 5,
        'status_tindak_lanjut' => Pelanggaran::TINDAK_MENUNGGU,
    ]);

    expect(svc()->tersediaUntuk($b, Carbon::parse('2026-12-01 08:00'), Carbon::parse('2026-12-01 10:00')))->toBe(2);

    $p->update(['status_tindak_lanjut' => Pelanggaran::TINDAK_LUNAS]);

    expect(svc()->tersediaUntuk($b, Carbon::parse('2026-12-01 08:00'), Carbon::parse('2026-12-01 10:00')))->toBe(3);
});

// ── Helper ───────────────────────────────────────────────────────────────────

test('cukup() menjawab permintaan berjumlah', function () {
    $b = barang(3);
    pesan($b, '2026-10-12 08:00:00', '2026-10-12 17:00:00', 2);

    $mulai = Carbon::parse('2026-10-12 10:00');
    $selesai = Carbon::parse('2026-10-12 12:00');

    expect(svc()->cukup($b, $mulai, $selesai, 1))->toBeTrue()
        ->and(svc()->cukup($b, $mulai, $selesai, 2))->toBeFalse();
});

test('jadwalTerpakai mengembalikan baris yang memesan slot saja', function () {
    $r = ruangan(0);
    pesan($r, '2026-10-12 08:00:00', '2026-10-12 12:00:00', 1);
    pesan($r, '2026-10-12 13:00:00', '2026-10-12 15:00:00', 1, PengajuanItem::STATUS_DIAJUKAN);

    $jadwal = svc()->jadwalTerpakai($r, Carbon::parse('2026-10-12 00:00'), Carbon::parse('2026-10-13 00:00'));

    expect($jadwal)->toHaveCount(1);
});
