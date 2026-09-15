<?php

use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\PengajuanStateMachine;

/**
 * PengajuanStateMachine adalah satu-satunya tempat status boleh berubah, dan
 * setiap transisi wajib meninggalkan jejak timeline.
 */
function mesin(): PengajuanStateMachine
{
    return app(PengajuanStateMachine::class);
}

function buatPengajuan(string $status = Pengajuan::STATUS_DRAFT): Pengajuan
{
    return Pengajuan::create([
        'kode' => 'SPJ-'.fake()->unique()->numerify('######'),
        'user_id' => User::factory()->peminjam()->create()->id,
        'jenis' => Pengajuan::JENIS_TUNGGAL,
        'status' => $status,
        'mulai_at' => now()->addDays(3),
        'selesai_at' => now()->addDays(3)->addHours(4),
    ]);
}

// ── Diagram §7.1, ditulis ulang secara eksplisit ─────────────────────────────
//
// Bila kode dan diagram berbeda, test ini gagal. Itulah pembuktian bahwa
// keduanya identik — bukan sekadar klaim di dokumen.
const TRANSISI_DIAGRAM = [
    'draft' => ['diajukan'],
    'diajukan' => ['diverifikasi', 'ditolak', 'dibatalkan', 'kedaluwarsa'],
    'diverifikasi' => ['disetujui', 'ditolak'],
    'disetujui' => ['disetujui_sebagian', 'berjalan', 'dibatalkan'],
    'disetujui_sebagian' => ['berjalan'],
    'berjalan' => ['menunggu_pemeriksaan'],
    'menunggu_pemeriksaan' => ['selesai', 'bermasalah'],
    'bermasalah' => ['selesai'],
    'selesai' => [],
    'ditolak' => [],
    'dibatalkan' => [],
    'kedaluwarsa' => [],
];

test('tabel transisi kode identik dengan diagram §7.1', function () {
    $kode = PengajuanStateMachine::TRANSISI;

    ksort($kode);
    $diagram = TRANSISI_DIAGRAM;
    ksort($diagram);

    foreach ($kode as $dari => $tujuan) {
        sort($tujuan);
        $kode[$dari] = $tujuan;
    }
    foreach ($diagram as $dari => $tujuan) {
        sort($tujuan);
        $diagram[$dari] = $tujuan;
    }

    expect($kode)->toBe($diagram);
});

test('seluruh 12 status punya entri transisi', function () {
    foreach (Pengajuan::STATUSES as $status) {
        expect(PengajuanStateMachine::TRANSISI)->toHaveKey($status);
    }
});

// ── Transisi sah ─────────────────────────────────────────────────────────────

test('setiap transisi sah pada diagram benar-benar diterima', function () {
    foreach (TRANSISI_DIAGRAM as $dari => $tujuanSah) {
        foreach ($tujuanSah as $ke) {
            $p = buatPengajuan($dari);

            mesin()->transisi($p, $ke);

            expect($p->fresh()->status)->toBe($ke, "gagal: {$dari} -> {$ke}");
        }
    }
});

test('setiap transisi TIDAK sah ditolak dengan pesan Indonesia', function () {
    $semua = array_keys(TRANSISI_DIAGRAM);
    $diuji = 0;

    foreach (TRANSISI_DIAGRAM as $dari => $tujuanSah) {
        foreach ($semua as $ke) {
            if ($ke === $dari || in_array($ke, $tujuanSah, true)) {
                continue;
            }

            $p = buatPengajuan($dari);

            expect(fn () => mesin()->transisi($p, $ke))
                ->toThrow(DomainException::class, 'tidak dapat berpindah');

            expect($p->fresh()->status)->toBe($dari);
            $diuji++;
        }
    }

    // Memastikan loop benar-benar menguji sesuatu, bukan lolos karena kosong.
    expect($diuji)->toBeGreaterThan(50);
});

test('status final menjelaskan bahwa ia tidak dapat diubah lagi', function () {
    $p = buatPengajuan(Pengajuan::STATUS_SELESAI);

    expect(fn () => mesin()->transisi($p, Pengajuan::STATUS_BERJALAN))
        ->toThrow(DomainException::class, 'sudah final');
});

test('transisi ke status yang sama ditolak', function () {
    $p = buatPengajuan(Pengajuan::STATUS_DIAJUKAN);

    expect(fn () => mesin()->transisi($p, Pengajuan::STATUS_DIAJUKAN))
        ->toThrow(DomainException::class, 'sudah berstatus');
});

test('status tidak dikenal ditolak', function () {
    $p = buatPengajuan();
    $p->forceFill(['status' => 'status_karangan'])->saveQuietly();

    expect(fn () => mesin()->transisi($p->fresh(), Pengajuan::STATUS_DIAJUKAN))
        ->toThrow(DomainException::class, 'tidak dikenali');
});

// ── Timeline wajib tertulis ──────────────────────────────────────────────────

test('setiap transisi menulis satu baris timeline berisi siapa, dari, dan ke', function () {
    $aktor = User::factory()->admin()->create();
    $p = buatPengajuan(Pengajuan::STATUS_DRAFT);

    mesin()->transisi($p, Pengajuan::STATUS_DIAJUKAN, $aktor, 'Dikirim pemohon');

    $baris = $p->timeline()->get();

    expect($baris)->toHaveCount(1);
    expect($baris->first()->dari_status)->toBe(Pengajuan::STATUS_DRAFT)
        ->and($baris->first()->ke_status)->toBe(Pengajuan::STATUS_DIAJUKAN)
        ->and($baris->first()->actor_id)->toBe($aktor->id)
        ->and($baris->first()->actor_role)->toBe('admin')
        ->and($baris->first()->catatan)->toBe('Dikirim pemohon');
});

test('transisi gagal tidak meninggalkan jejak timeline', function () {
    $p = buatPengajuan(Pengajuan::STATUS_DRAFT);

    try {
        mesin()->transisi($p, Pengajuan::STATUS_SELESAI);
    } catch (DomainException) {
        // diabaikan — yang diuji adalah efek sampingnya
    }

    expect($p->timeline()->count())->toBe(0);
});

test('timeline bersifat append-only: tidak punya kolom updated_at', function () {
    $p = buatPengajuan();
    $baris = mesin()->catat($p, 'Uji catatan');

    expect(Schema::hasColumn('pengajuan_timeline', 'updated_at'))->toBeFalse()
        ->and($baris->created_at)->not->toBeNull();
});

// ── Status baris aset ────────────────────────────────────────────────────────

test('transisi baris aset tercatat di timeline pengajuan induknya', function () {
    $p = buatPengajuan(Pengajuan::STATUS_DIVERIFIKASI);
    $ruangan = Ruangan::create([
        'nama' => 'Aula', 'kode' => 'AULA-9', 'kapasitas' => 100, 'status' => 'tersedia',
    ]);

    $item = PengajuanItem::create([
        'pengajuan_id' => $p->id,
        'assetable_type' => Ruangan::class,
        'assetable_id' => $ruangan->id,
        'jumlah' => 1,
        'mulai_at' => now()->addDays(3),
        'selesai_at' => now()->addDays(3)->addHours(4),
        'status_item' => PengajuanItem::STATUS_DIAJUKAN,
    ]);

    mesin()->transisiItem($item, PengajuanItem::STATUS_DISETUJUI);

    expect($item->fresh()->status_item)->toBe(PengajuanItem::STATUS_DISETUJUI)
        ->and($p->timeline()->count())->toBe(1)
        ->and($p->timeline()->first()->meta['pengajuan_item_id'])->toBe($item->id);
});

test('transisi baris aset yang tidak sah ditolak', function () {
    $p = buatPengajuan(Pengajuan::STATUS_DIVERIFIKASI);
    $ruangan = Ruangan::create([
        'nama' => 'Lab', 'kode' => 'LAB-9', 'kapasitas' => 30, 'status' => 'tersedia',
    ]);

    $item = PengajuanItem::create([
        'pengajuan_id' => $p->id,
        'assetable_type' => Ruangan::class,
        'assetable_id' => $ruangan->id,
        'jumlah' => 1,
        'mulai_at' => now()->addDays(3),
        'selesai_at' => now()->addDays(3)->addHours(2),
        'status_item' => PengajuanItem::STATUS_DITOLAK,
    ]);

    expect(fn () => mesin()->transisiItem($item, PengajuanItem::STATUS_BERJALAN))
        ->toThrow(DomainException::class);
});
