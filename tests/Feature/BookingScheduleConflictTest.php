<?php

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * B-03 — Bentrok jadwal ruangan sebelumnya HANYA dicek saat membuat pengajuan,
 * tidak saat menyetujui. Akibatnya dua pengajuan "menunggu" pada slot yang
 * sama bisa dua-duanya disetujui admin.
 */
function ruanganUji(): Ruangan
{
    return Ruangan::create([
        'nama' => 'Aula Utama',
        'kode' => 'AULA-01',
        'kapasitas' => 200,
        'lokasi' => 'Gedung A',
        'status' => 'tersedia',
    ]);
}

/**
 * Dibuat langsung lewat model (bukan lewat service) untuk meniru dua pengajuan
 * yang lolos ke antrean — persis kondisi yang dulu menghasilkan double-booking.
 */
function pengajuanRuangan(Ruangan $ruangan, User $user, string $mulai, string $selesai): Peminjaman
{
    return Peminjaman::create([
        'user_id' => $user->id,
        'tipe' => 'ruangan',
        'ruangan_id' => $ruangan->id,
        'nama_item' => $ruangan->nama,
        'jumlah' => 1,
        'tanggal_mulai' => substr($mulai, 0, 10),
        'tanggal_selesai' => substr($selesai, 0, 10),
        'jam_mulai' => substr($mulai, 11),
        'jam_selesai' => substr($selesai, 11),
        'keterangan' => 'Kegiatan uji',
        'status' => Peminjaman::STATUS_PENDING,
    ]);
}

test('approve kedua pada slot yang sama ditolak dengan pesan yang jelas', function () {
    $service = app(BookingService::class);
    $ruangan = ruanganUji();

    $a = pengajuanRuangan($ruangan, User::factory()->create(), '2026-10-12 08:00:00', '2026-10-12 12:00:00');
    $b = pengajuanRuangan($ruangan, User::factory()->create(), '2026-10-12 10:00:00', '2026-10-12 14:00:00');

    // Approve pertama: sukses.
    $service->approveBooking($a);
    expect($a->fresh()->status)->toBe(Peminjaman::STATUS_APPROVED);

    // Approve kedua: harus gagal karena beririsan 10:00–12:00.
    expect(fn () => $service->approveBooking($b))
        ->toThrow(RuntimeException::class, 'Aula Utama');

    expect($b->fresh()->status)->toBe(Peminjaman::STATUS_PENDING);
});

test('approve tetap berhasil bila jadwalnya tidak beririsan', function () {
    $service = app(BookingService::class);
    $ruangan = ruanganUji();

    $pagi = pengajuanRuangan($ruangan, User::factory()->create(), '2026-10-12 08:00:00', '2026-10-12 12:00:00');
    $sore = pengajuanRuangan($ruangan, User::factory()->create(), '2026-10-12 13:00:00', '2026-10-12 17:00:00');

    $service->approveBooking($pagi);
    $service->approveBooking($sore);

    expect($pagi->fresh()->status)->toBe(Peminjaman::STATUS_APPROVED)
        ->and($sore->fresh()->status)->toBe(Peminjaman::STATUS_APPROVED);
});

test('slot yang bersentuhan persis di ujungnya tidak dianggap bentrok', function () {
    $service = app(BookingService::class);
    $ruangan = ruanganUji();

    // 08:00–12:00 lalu 12:00–15:00 — batasnya menyentuh, bukan beririsan.
    $a = pengajuanRuangan($ruangan, User::factory()->create(), '2026-10-12 08:00:00', '2026-10-12 12:00:00');
    $b = pengajuanRuangan($ruangan, User::factory()->create(), '2026-10-12 12:00:00', '2026-10-12 15:00:00');

    $service->approveBooking($a);
    $service->approveBooking($b);

    expect($b->fresh()->status)->toBe(Peminjaman::STATUS_APPROVED);
});

test('menyetujui pengajuan pertama tidak terhalang pengajuan lain yang masih menunggu', function () {
    $service = app(BookingService::class);
    $ruangan = ruanganUji();

    $a = pengajuanRuangan($ruangan, User::factory()->create(), '2026-10-12 08:00:00', '2026-10-12 12:00:00');
    pengajuanRuangan($ruangan, User::factory()->create(), '2026-10-12 09:00:00', '2026-10-12 11:00:00');

    // Hanya status "sedang_dipinjam" yang memesan slot saat approve; kalau
    // status "menunggu" ikut diperiksa, baris ini akan gagal.
    $service->approveBooking($a);

    expect($a->fresh()->status)->toBe(Peminjaman::STATUS_APPROVED);
});

test('B-15: mulai_at dan selesai_at terisi otomatis walau tidak diisi eksplisit', function () {
    $ruangan = ruanganUji();
    $p = pengajuanRuangan($ruangan, User::factory()->create(), '2026-10-12 08:00:00', '2026-10-12 12:00:00');

    expect($p->mulai_at)->not->toBeNull()
        ->and($p->selesai_at)->not->toBeNull()
        ->and($p->mulai_at->format('Y-m-d H:i'))->toBe('2026-10-12 08:00')
        ->and($p->selesai_at->format('Y-m-d H:i'))->toBe('2026-10-12 12:00');
});
