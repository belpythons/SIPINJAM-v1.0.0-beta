<?php

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * B-02 — Saat stok mencapai 0, kode lama menolak SELURUH pengajuan "menunggu"
 * untuk barang tersebut, termasuk yang jadwalnya bulan depan. Stok habis hari
 * ini tidak berarti habis bulan depan.
 *
 * B-06 — Alasan pembatalan ditulis ke `alasan_sistem`, bukan menimpa
 * `keterangan` milik pemohon.
 */
function barangUji(int $stok = 1): Barang
{
    return Barang::create([
        'nama' => 'Proyektor Epson',
        'kode' => 'PRJ-01',
        'stok_total' => $stok,
        'stok_tersedia' => $stok,
        'kategori' => 'Elektronik',
        'status' => 'tersedia',
    ]);
}

function pengajuanBarang(Barang $barang, User $user, string $mulai, string $selesai, string $keterangan): Peminjaman
{
    return Peminjaman::create([
        'user_id' => $user->id,
        'tipe' => 'barang',
        'barang_id' => $barang->id,
        'nama_item' => $barang->nama,
        'jumlah' => 1,
        'tanggal_mulai' => substr($mulai, 0, 10),
        'tanggal_selesai' => substr($selesai, 0, 10),
        'jam_mulai' => substr($mulai, 11),
        'jam_selesai' => substr($selesai, 11),
        'keterangan' => $keterangan,
        'status' => Peminjaman::STATUS_PENDING,
    ]);
}

test('stok habis hanya menolak pengajuan yang jadwalnya beririsan', function () {
    $service = app(BookingService::class);
    $barang = barangUji(1);

    $disetujui = pengajuanBarang($barang, User::factory()->create(), '2026-10-12 08:00:00', '2026-10-12 17:00:00', 'Seminar');
    $beririsan = pengajuanBarang($barang, User::factory()->create(), '2026-10-12 10:00:00', '2026-10-12 15:00:00', 'Workshop');
    $bulanDepan = pengajuanBarang($barang, User::factory()->create(), '2026-12-20 08:00:00', '2026-12-20 17:00:00', 'Wisuda');

    $service->approveBooking($disetujui);

    expect($barang->fresh()->stok_tersedia)->toBe(0);

    // Yang beririsan: ditolak.
    expect($beririsan->fresh()->status)->toBe(Peminjaman::STATUS_REJECTED);

    // Yang bulan depan: HARUS tetap menunggu — inti dari B-02.
    expect($bulanDepan->fresh()->status)->toBe(Peminjaman::STATUS_PENDING);
});

test('B-06: alasan sistem tidak menimpa keterangan pemohon', function () {
    $service = app(BookingService::class);
    $barang = barangUji(1);

    $disetujui = pengajuanBarang($barang, User::factory()->create(), '2026-10-12 08:00:00', '2026-10-12 17:00:00', 'Seminar');
    $ditolak = pengajuanBarang($barang, User::factory()->create(), '2026-10-12 10:00:00', '2026-10-12 15:00:00', 'Workshop Robotika');

    $service->approveBooking($disetujui);

    $ditolak->refresh();

    expect($ditolak->keterangan)->toBe('Workshop Robotika')
        ->and($ditolak->alasan_sistem)->toContain('stok habis');
});

test('B-06: auto-reject SLA menulis ke alasan_sistem, bukan keterangan', function () {
    $barang = barangUji(2);

    $lama = pengajuanBarang($barang, User::factory()->create(), '2026-10-12 08:00:00', '2026-10-12 17:00:00', 'Keperluan asli pemohon');
    // Paksa dibuat 8 hari lalu agar melewati SLA 7 hari.
    $lama->forceFill(['created_at' => now()->subDays(8)])->saveQuietly();

    $this->artisan('booking:auto-reject')->assertSuccessful();

    $lama->refresh();

    expect($lama->status)->toBe(Peminjaman::STATUS_REJECTED)
        ->and($lama->keterangan)->toBe('Keperluan asli pemohon')
        ->and($lama->alasan_sistem)->toContain('7 hari');
});
