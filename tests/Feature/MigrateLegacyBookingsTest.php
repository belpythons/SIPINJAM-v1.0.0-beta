<?php

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\PengajuanTimeline;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi data legacy: peminjamans → pengajuan + pengajuan_items.
 *
 * Yang paling penting dibuktikan: perintah ini IDEMPOTEN. Tabel lama masih
 * hidup selama P1–P2, jadi perintah ini akan dijalankan ulang di P3 untuk
 * menyusul data baru — kalau ia menggandakan, seluruh riwayat rusak.
 */
function ruanganLegacy(): Ruangan
{
    return Ruangan::create([
        'nama' => 'Aula Utama', 'kode' => 'AULA-'.fake()->unique()->numerify('###'),
        'kapasitas' => 200, 'status' => 'tersedia',
    ]);
}

function barangLegacy(): Barang
{
    return Barang::create([
        'nama' => 'Proyektor', 'kode' => 'PRJ-'.fake()->unique()->numerify('###'),
        'stok_total' => 5, 'stok_tersedia' => 5, 'status' => 'tersedia',
    ]);
}

function peminjamanLegacy(array $attr = []): Peminjaman
{
    $r = ruanganLegacy();

    return Peminjaman::create(array_merge([
        'user_id' => User::factory()->peminjam()->create()->id,
        'tipe' => 'ruangan',
        'ruangan_id' => $r->id,
        'nama_item' => $r->nama,
        'jumlah' => 1,
        'tanggal_mulai' => '2026-08-12',
        'tanggal_selesai' => '2026-08-12',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '12:00:00',
        'keterangan' => 'Rapat himpunan',
        'status' => Peminjaman::STATUS_DONE,
    ], $attr));
}

test('setiap baris lama menjadi satu pengajuan dan satu baris aset', function () {
    peminjamanLegacy();
    peminjamanLegacy();
    peminjamanLegacy();

    $this->artisan('sipinjam:migrate-legacy')->assertSuccessful();

    expect(Pengajuan::count())->toBe(3)
        ->and(PengajuanItem::count())->toBe(3);
});

test('pemetaan status lama ke status baru sesuai spesifikasi', function () {
    $peta = [
        Peminjaman::STATUS_PENDING => Pengajuan::STATUS_DIAJUKAN,
        Peminjaman::STATUS_APPROVED => Pengajuan::STATUS_BERJALAN,
        Peminjaman::STATUS_DONE => Pengajuan::STATUS_SELESAI,
        Peminjaman::STATUS_REJECTED => Pengajuan::STATUS_DITOLAK,
    ];

    foreach ($peta as $lama => $baru) {
        peminjamanLegacy(['status' => $lama]);
    }

    $this->artisan('sipinjam:migrate-legacy')->assertSuccessful();

    foreach ($peta as $lama => $baru) {
        expect(Pengajuan::where('status', $baru)->count())
            ->toBe(1, "status lama {$lama} seharusnya menjadi {$baru}");
    }
});

test('IDEMPOTEN: dijalankan dua kali tidak menggandakan apa pun', function () {
    peminjamanLegacy();
    peminjamanLegacy();

    $this->artisan('sipinjam:migrate-legacy')->assertSuccessful();
    $this->artisan('sipinjam:migrate-legacy')->assertSuccessful();
    $this->artisan('sipinjam:migrate-legacy')->assertSuccessful();

    expect(Pengajuan::count())->toBe(2)
        ->and(PengajuanItem::count())->toBe(2)
        ->and(PengajuanTimeline::count())->toBe(2);
});

test('menjalankan ulang menyusul baris yang lahir setelah migrasi pertama', function () {
    peminjamanLegacy();

    $this->artisan('sipinjam:migrate-legacy')->assertSuccessful();
    expect(Pengajuan::count())->toBe(1);

    // Ini yang akan terjadi di P3: tabel lama masih hidup selama P1–P2.
    peminjamanLegacy();
    peminjamanLegacy();

    $this->artisan('sipinjam:migrate-legacy')->assertSuccessful();
    expect(Pengajuan::count())->toBe(3);
});

test('--dry-run tidak menulis apa pun', function () {
    peminjamanLegacy();
    peminjamanLegacy();

    $this->artisan('sipinjam:migrate-legacy', ['--dry-run' => true])->assertSuccessful();

    expect(Pengajuan::count())->toBe(0)
        ->and(PengajuanItem::count())->toBe(0)
        ->and(PengajuanTimeline::count())->toBe(0);
});

test('data penting terbawa: pemohon, jadwal, jumlah, nomor surat', function () {
    $b = barangLegacy();
    $pemohon = User::factory()->peminjam()->create();

    Peminjaman::create([
        'user_id' => $pemohon->id,
        'tipe' => 'barang',
        'barang_id' => $b->id,
        'nama_item' => $b->nama,
        'jumlah' => 4,
        'tanggal_mulai' => '2026-08-12',
        'tanggal_selesai' => '2026-08-12',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '17:00:00',
        'keterangan' => 'Seminar nasional',
        'status' => Peminjaman::STATUS_APPROVED,
        'nomor_surat' => '042/INT/SIPINJAM/2026',
    ]);

    $this->artisan('sipinjam:migrate-legacy')->assertSuccessful();

    $p = Pengajuan::first();
    $item = $p->items()->first();

    expect($p->user_id)->toBe($pemohon->id)
        ->and($p->nomor_surat)->toBe('042/INT/SIPINJAM/2026')
        ->and($p->jenis)->toBe(Pengajuan::JENIS_TUNGGAL)
        ->and($p->deskripsi)->toBe('Seminar nasional')
        ->and($item->jumlah)->toBe(4)
        ->and($item->assetable_type)->toBe(Barang::class)
        ->and($item->assetable_id)->toBe($b->id)
        ->and($item->mulai_at->format('Y-m-d H:i'))->toBe('2026-08-12 08:00')
        ->and($item->selesai_at->format('Y-m-d H:i'))->toBe('2026-08-12 17:00');
});

test('relasi polimorfik menunjuk model yang benar', function () {
    peminjamanLegacy();
    $b = barangLegacy();
    Peminjaman::create([
        'user_id' => User::factory()->peminjam()->create()->id,
        'tipe' => 'barang', 'barang_id' => $b->id, 'nama_item' => $b->nama, 'jumlah' => 1,
        'tanggal_mulai' => '2026-08-13', 'tanggal_selesai' => '2026-08-13',
        'jam_mulai' => '08:00:00', 'jam_selesai' => '12:00:00',
        'status' => Peminjaman::STATUS_DONE,
    ]);

    $this->artisan('sipinjam:migrate-legacy')->assertSuccessful();

    $jenis = PengajuanItem::with('assetable')->get()->map(fn ($i) => $i->assetable::class);

    expect($jenis)->toContain(Ruangan::class)->toContain(Barang::class);
});

test('setiap pengajuan hasil migrasi punya entri timeline sintetis', function () {
    $lama = peminjamanLegacy();

    $this->artisan('sipinjam:migrate-legacy')->assertSuccessful();

    $jejak = PengajuanTimeline::first();

    expect($jejak->aksi)->toBe('Dimigrasikan dari data lama')
        ->and($jejak->meta['peminjaman_id'])->toBe($lama->id)
        ->and($jejak->meta['sumber'])->toBe('peminjamans')
        ->and($jejak->actor_id)->toBeNull();
});

test('baris lama tanpa relasi aset dilaporkan, bukan didiamkan', function () {
    Peminjaman::create([
        'user_id' => User::factory()->peminjam()->create()->id,
        'tipe' => 'ruangan',
        'ruangan_id' => null,   // yatim
        'nama_item' => 'Entah',
        'jumlah' => 1,
        'tanggal_mulai' => '2026-08-12', 'tanggal_selesai' => '2026-08-12',
        'jam_mulai' => '08:00:00', 'jam_selesai' => '12:00:00',
        'status' => Peminjaman::STATUS_DONE,
    ]);

    $this->artisan('sipinjam:migrate-legacy')
        ->expectsOutputToContain('tidak punya relasi ruangan/barang')
        ->assertSuccessful();

    expect(Pengajuan::count())->toBe(0);
});

test('tabel peminjamans lama TIDAK di-rename — UI lama harus tetap hidup', function () {
    peminjamanLegacy();

    $this->artisan('sipinjam:migrate-legacy')->assertSuccessful();

    expect(Schema::hasTable('peminjamans'))->toBeTrue()
        ->and(Schema::hasTable('peminjamans_legacy'))->toBeFalse()
        ->and(Peminjaman::count())->toBe(1);
});
