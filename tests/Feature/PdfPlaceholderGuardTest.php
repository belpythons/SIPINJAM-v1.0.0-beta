<?php

use App\Services\PdfRenderer;

/**
 * Pagar placeholder dua tingkat.
 *
 * - sipinjam.kop.*           → wajib untuk SEMUA dokumen
 * - sipinjam.penandatangan.* → wajib HANYA untuk dokumen dengan blok tanda tangan
 *
 * Tingkatnya ditentukan pemanggil lewat pemilihan metode (render vs
 * renderDokumenResmi), bukan ditebak dari nama view.
 */
function isiKopLengkap(): void
{
    config()->set('sipinjam.kop', [
        'yayasan' => 'Yayasan Contoh',
        'institusi' => 'Institut Contoh',
        'alamat' => 'Jl. Contoh No. 1',
        'website' => 'contoh.ac.id',
        'telepon' => '(0548) 111222',
        'logo_path' => 'logo.png',
    ]);
}

function isiPenandatanganLengkap(): void
{
    config()->set('sipinjam.penandatangan', [
        'pejabat' => ['nama' => 'Dr. Contoh', 'jabatan' => 'Wakil Ketua II', 'nip' => '1990'],
        'pengelola_aset' => ['nama' => 'Contoh Pengelola', 'jabatan' => 'Kepala Sarpras', 'nip' => '1985'],
        'penerima_surat' => ['nama' => 'Contoh Ketua', 'jabatan' => 'Ketua'],
    ]);
}

function isiConfigLengkap(): void
{
    isiKopLengkap();
    isiPenandatanganLengkap();
}

/** Kop beres, penanda tangan masih placeholder — kondisi inti yang diuji. */
function kopBeresPenandatanganPlaceholder(): void
{
    isiConfigLengkap();
    config()->set('sipinjam.penandatangan.pejabat.nama', '[ISI NAMA PEJABAT]');
    config()->set('sipinjam.penandatangan.pejabat.nip', '[ISI NIP PEJABAT]');
}

function produksi(): void
{
    app()->detectEnvironment(fn () => 'production');
}

// ── Inti: penandatangan placeholder memisahkan dua tingkat ───────────────────

test('ekspor laporan TETAP berhasil saat penandatangan masih placeholder', function () {
    produksi();
    kopBeresPenandatanganPlaceholder();

    expect(fn () => app(PdfRenderer::class)->render('reports.peminjaman_pdf'))
        ->not->toThrow(RuntimeException::class);

    expect(fn () => app(PdfRenderer::class)->render('reports.user_peminjaman_pdf'))
        ->not->toThrow(RuntimeException::class);
});

test('ekspor kalender TETAP berhasil saat penandatangan masih placeholder', function () {
    produksi();
    kopBeresPenandatanganPlaceholder();

    expect(fn () => app(PdfRenderer::class)->render('user.kalender.pdf'))
        ->not->toThrow(RuntimeException::class);
});

test('surat peminjaman DITOLAK saat penandatangan masih placeholder', function () {
    produksi();
    kopBeresPenandatanganPlaceholder();

    expect(fn () => app(PdfRenderer::class)->renderDokumenResmi('pdf.surat-peminjaman'))
        ->toThrow(RuntimeException::class);
});

test('pesan galat menyebut kunci penandatangan mana yang belum diisi', function () {
    produksi();
    kopBeresPenandatanganPlaceholder();

    try {
        app(PdfRenderer::class)->renderDokumenResmi('pdf.surat-peminjaman');
        $this->fail('Seharusnya melempar RuntimeException.');
    } catch (RuntimeException $e) {
        expect($e->getMessage())
            ->toContain('sipinjam.penandatangan.pejabat.nama')
            ->toContain('sipinjam.penandatangan.pejabat.nip')
            ->toContain('tidak dapat diterbitkan')
            ->toContain('config/sipinjam.php');
    }
});

// ── Kop: berlaku untuk SEMUA dokumen ─────────────────────────────────────────

test('kop placeholder memblokir dokumen biasa maupun dokumen resmi', function () {
    produksi();
    isiConfigLengkap();
    config()->set('sipinjam.kop.institusi', '[ISI NAMA INSTITUSI]');

    expect(fn () => app(PdfRenderer::class)->render('reports.peminjaman_pdf'))
        ->toThrow(RuntimeException::class);

    expect(fn () => app(PdfRenderer::class)->renderDokumenResmi('pdf.surat-peminjaman'))
        ->toThrow(RuntimeException::class);
});

test('render() tidak ikut mengeluhkan penandatangan pada pesan galat kop', function () {
    produksi();
    kopBeresPenandatanganPlaceholder();
    config()->set('sipinjam.kop.institusi', '[ISI NAMA INSTITUSI]');

    try {
        app(PdfRenderer::class)->render('reports.peminjaman_pdf');
        $this->fail('Seharusnya melempar RuntimeException.');
    } catch (RuntimeException $e) {
        expect($e->getMessage())
            ->toContain('sipinjam.kop.institusi')
            // Grup penandatangan tidak diperiksa di tingkat ini.
            ->not->toContain('sipinjam.penandatangan');
    }
});

// ── Config lengkap & pengecualian environment ────────────────────────────────

test('config terisi penuh: kedua tingkat lolos di produksi', function () {
    produksi();
    isiConfigLengkap();

    expect(app(PdfRenderer::class)->kunciConfigBelumDiisi())->toBe([]);

    // render() hanya merakit PdfBuilder — Browsershot baru berjalan saat
    // save()/download(), jadi pemanggilan ini aman di dalam test.
    expect(fn () => app(PdfRenderer::class)->render('reports.peminjaman_pdf'))
        ->not->toThrow(RuntimeException::class);

    expect(fn () => app(PdfRenderer::class)->renderDokumenResmi('pdf.surat-peminjaman'))
        ->not->toThrow(RuntimeException::class);
});

test('di lokal/testing, placeholder sengaja dibiarkan lewat', function () {
    isiConfigLengkap();
    config()->set('sipinjam.kop.institusi', '[ISI NAMA INSTITUSI]');
    config()->set('sipinjam.penandatangan.pejabat.nama', '[ISI NAMA PEJABAT]');

    foreach (['local', 'testing'] as $env) {
        app()->detectEnvironment(fn () => $env);

        expect(fn () => app(PdfRenderer::class)->render('reports.peminjaman_pdf'))
            ->not->toThrow(RuntimeException::class);

        expect(fn () => app(PdfRenderer::class)->renderDokumenResmi('pdf.surat-peminjaman'))
            ->not->toThrow(RuntimeException::class);
    }
});

// ── Pemindaian kunci ─────────────────────────────────────────────────────────

test('kunciConfigBelumDiisi menghormati grup yang diminta', function () {
    isiConfigLengkap();
    config()->set('sipinjam.kop.institusi', '[ISI NAMA INSTITUSI]');
    config()->set('sipinjam.penandatangan.pejabat.nama', '[ISI NAMA PEJABAT]');

    $renderer = app(PdfRenderer::class);

    expect($renderer->kunciConfigBelumDiisi(['kop']))
        ->toBe(['sipinjam.kop.institusi']);

    expect($renderer->kunciConfigBelumDiisi(['penandatangan']))
        ->toBe(['sipinjam.penandatangan.pejabat.nama']);

    expect($renderer->kunciConfigBelumDiisi(['kop', 'penandatangan']))
        ->toHaveCount(2);
});

test('config bawaan aplikasi memang masih berisi placeholder (mengapa pagar ini ada)', function () {
    // Membaca config/sipinjam.php apa adanya, tanpa override.
    expect(app(PdfRenderer::class)->kunciConfigBelumDiisi(['penandatangan']))->not->toBeEmpty();
});
