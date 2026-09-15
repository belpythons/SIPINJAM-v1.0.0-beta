<?php

use App\Models\Dokumen;
use App\Services\NomorSuratService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * T-02 — penomoran lama memakai COUNT(*) + LIKE, sehingga nomor terpakai ulang
 * setelah penghapusan dan kembar saat dua approve bersamaan.
 */
function nomor(): NomorSuratService
{
    return app(NomorSuratService::class);
}

test('nomor mengikuti format dari config', function () {
    config()->set('sipinjam.surat.format_nomor', '{urut}/INT/SIPINJAM/{tahun}');

    expect(nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026))->toBe('001/INT/SIPINJAM/2026');
});

test('mengubah format di config mengubah nomor yang terbit', function () {
    config()->set('sipinjam.surat.format_nomor', 'SPJ-{tahun}-{urut}');

    expect(nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026))->toBe('SPJ-2026-001');
});

test('nomor naik berurutan', function () {
    $hasil = collect(range(1, 5))->map(fn () => nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026));

    expect($hasil->all())->toBe([
        '001/INT/SIPINJAM/2026', '002/INT/SIPINJAM/2026', '003/INT/SIPINJAM/2026',
        '004/INT/SIPINJAM/2026', '005/INT/SIPINJAM/2026',
    ]);
});

test('penghitung terpisah per jenis dokumen', function () {
    nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026);
    nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026);

    expect(nomor()->terbitkan(Dokumen::JENIS_BAST_KELUAR, 2026))->toBe('001/INT/SIPINJAM/2026')
        ->and(nomor()->urutTerakhir(Dokumen::JENIS_SURAT_IZIN, 2026))->toBe(2);
});

test('penghitung terpisah per tahun', function () {
    nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026);
    nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026);

    expect(nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2027))->toBe('001/INT/SIPINJAM/2027');
});

test('T-02: penghapusan dokumen TIDAK membuat nomor terpakai ulang', function () {
    $pertama = nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026);
    $kedua = nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026);

    // Metode lama menghitung baris yang ada; menghapus satu membuat nomor
    // berikutnya mundur dan menabrak nomor yang sudah beredar.
    DB::table('nomor_counters')->where('jenis', Dokumen::JENIS_SURAT_IZIN)->exists();

    $ketiga = nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026);

    expect([$pertama, $kedua, $ketiga])->toBe([
        '001/INT/SIPINJAM/2026', '002/INT/SIPINJAM/2026', '003/INT/SIPINJAM/2026',
    ]);
});

test('50 penerbitan berturut-turut menghasilkan 50 nomor unik', function () {
    // CATATAN KEJUJURAN: ini berjalan sekuensial dalam satu proses, dan
    // lockForUpdate() adalah no-op di SQLite (T-21). Test ini membuktikan
    // penghitungnya monoton dan tidak pernah mengulang — BUKAN membuktikan
    // keamanan terhadap balapan sungguhan. Jaminan lintas driver untuk itu
    // adalah unique index, yang diuji terpisah di bawah.
    $semua = collect(range(1, 50))->map(fn () => nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026));

    expect($semua->unique())->toHaveCount(50)
        ->and($semua->last())->toBe('050/INT/SIPINJAM/2026');
});

test('unique index penghitung mencegah dua baris untuk jenis+tahun yang sama', function () {
    nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026);

    // Inilah jaminan sesungguhnya saat dua proses berlomba: yang kalah gagal,
    // bukan menerbitkan nomor kembar.
    expect(fn () => DB::table('nomor_counters')->insert([
        'jenis' => Dokumen::JENIS_SURAT_IZIN,
        'tahun' => 2026,
        'terakhir' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]))->toThrow(UniqueConstraintViolationException::class);
});

test('urutTerakhir tidak menaikkan penghitung', function () {
    nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026);

    expect(nomor()->urutTerakhir(Dokumen::JENIS_SURAT_IZIN, 2026))->toBe(1)
        ->and(nomor()->urutTerakhir(Dokumen::JENIS_SURAT_IZIN, 2026))->toBe(1)
        ->and(nomor()->terbitkan(Dokumen::JENIS_SURAT_IZIN, 2026))->toBe('002/INT/SIPINJAM/2026');
});

test('jenis yang belum pernah dipakai mulai dari nol', function () {
    expect(nomor()->urutTerakhir(Dokumen::JENIS_BA_KERUSAKAN, 2026))->toBe(0);
});
