<?php

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * B-08 — template surat tidak boleh bergantung pada CDN/internet.
 * B-09 — kop & penanda tangan harus berasal dari config, bukan hardcode.
 */
function renderSurat(array $overrides = []): string
{
    $user = User::factory()->create(['name' => 'Siti Aminah', 'email' => 'siti@stitek.ac.id']);

    $barang = Barang::create([
        'nama' => 'Proyektor Epson', 'kode' => 'PRJ-9',
        'stok_total' => 5, 'stok_tersedia' => 5,
        'kategori' => 'Elektronik', 'status' => 'tersedia',
    ]);

    $peminjaman = Peminjaman::create(array_merge([
        'user_id' => $user->id,
        'tipe' => 'barang',
        'barang_id' => $barang->id,
        'nama_item' => $barang->nama,
        'jumlah' => 4,
        'tanggal_mulai' => '2026-10-12',
        'tanggal_selesai' => '2026-10-12',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '17:00:00',
        'keterangan' => 'Seminar Nasional',
        'status' => Peminjaman::STATUS_APPROVED,
        'nomor_surat' => '007/INT/SIPINJAM/2026',
        'approved_at' => now(),
    ], $overrides));

    return view('pdf.surat-peminjaman', [
        'peminjaman' => $peminjaman,
        'user' => $user,
        'asset' => $barang,
    ])->render();
}

test('B-08: surat tidak memuat sumber daya dari internet', function () {
    $html = renderSurat();

    expect($html)->not->toContain('cdn.tailwindcss.com')
        ->and($html)->not->toContain('fonts.googleapis.com')
        ->and($html)->not->toContain('cdnjs.cloudflare.com')
        ->and($html)->not->toContain('<script');
});

test('B-09: nama & NIP fiktif yang di-hardcode sudah tidak ada', function () {
    $html = renderSurat();

    expect($html)->not->toContain('Aisyah Rahmawati')
        ->and($html)->not->toContain('2024090123')
        ->and($html)->not->toContain('Ketua Panitia')
        ->and($html)->not->toContain('Sekretaris Panitia')
        ->and($html)->not->toContain('Hardianto');
});

test('B-09: mengubah config langsung mengubah isi surat', function () {
    config()->set('sipinjam.penandatangan.pejabat', [
        'nama' => 'Dr. Contoh Pejabat, M.T.', 'jabatan' => 'Wakil Ketua II', 'nip' => '199001012020121001',
    ]);
    config()->set('sipinjam.penandatangan.pengelola_aset', [
        'nama' => 'Contoh Pengelola', 'jabatan' => 'Kepala Sarpras', 'nip' => '198505052015031002',
    ]);
    config()->set('sipinjam.kop.institusi', 'Institut Contoh Nusantara');
    config()->set('sipinjam.surat.kota', 'Samarinda');

    $html = renderSurat();

    expect($html)->toContain('Dr. Contoh Pejabat, M.T.')
        ->and($html)->toContain('Wakil Ketua II')
        ->and($html)->toContain('199001012020121001')
        ->and($html)->toContain('Contoh Pengelola')
        ->and($html)->toContain('Kepala Sarpras')
        ->and($html)->toContain('Institut Contoh Nusantara')
        ->and($html)->toContain('Samarinda');
});

test('B-09c: jumlah unit diambil dari data, bukan ditulis "1 Unit"', function () {
    $html = renderSurat();

    expect($html)->toContain('4 unit')
        ->and($html)->not->toContain('1 Unit');
});
