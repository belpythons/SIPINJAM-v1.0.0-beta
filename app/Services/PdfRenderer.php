<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

/**
 * Satu-satunya tempat konfigurasi Browsershot dirakit.
 *
 * Sebelumnya blok deteksi binary Node/npm disalin di empat controller dan
 * membaca env() secara langsung — nilainya menjadi null begitu
 * `php artisan config:cache` dijalankan di produksi, sehingga selalu jatuh ke
 * tebakan path. Sekarang path dibaca dari config/sipinjam.php.
 */
class PdfRenderer
{
    /**
     * Render dokumen biasa: laporan, ekspor, kalender.
     *
     * Hanya kop surat yang diperiksa. Dokumen jenis ini tidak punya blok tanda
     * tangan, jadi memblokirnya karena `penandatangan` belum diisi hanya akan
     * mematikan fitur yang sebenarnya baik-baik saja.
     *
     * @param  string  $view  Nama view Blade, mis. 'reports.peminjaman_pdf'
     * @param  array  $data  Data yang dikirim ke view
     * @param  string  $format  Ukuran kertas, mis. 'a4'
     */
    public function render(string $view, array $data = [], string $format = 'a4'): PdfBuilder
    {
        $this->assertKonfigurasiLengkap(['kop']);

        return $this->build($view, $data, $format);
    }

    /**
     * Render dokumen resmi yang memuat blok tanda tangan: surat permohonan,
     * surat izin, BAST, berita acara.
     *
     * Kop DAN penanda tangan sama-sama wajib terisi. Tingkatnya ditentukan oleh
     * pemanggil lewat pemilihan metode — sengaja tidak ditebak dari nama view,
     * supaya menambah dokumen baru tidak diam-diam melewati pemeriksaan.
     */
    public function renderDokumenResmi(string $view, array $data = [], string $format = 'a4'): PdfBuilder
    {
        $this->assertKonfigurasiLengkap(['kop', 'penandatangan']);

        return $this->build($view, $data, $format);
    }

    /**
     * Perakitan Browsershot — satu tempat untuk semua jenis dokumen (B-13).
     */
    private function build(string $view, array $data, string $format): PdfBuilder
    {
        $nodeBinary = $this->nodeBinary();
        $npmBinary = $this->npmBinary();
        $noSandbox = (bool) config('sipinjam.pdf.no_sandbox', true);

        return Pdf::view($view, $data)
            ->format($format)
            ->withBrowsershot(function ($browsershot) use ($nodeBinary, $npmBinary, $noSandbox) {
                if ($noSandbox) {
                    $browsershot->noSandbox();
                }

                if ($nodeBinary !== null) {
                    $browsershot->setNodeBinary($nodeBinary);
                }

                if ($npmBinary !== null) {
                    $browsershot->setNpmBinary($npmBinary);
                }
            });
    }

    /**
     * Pagar terakhir sebelum dokumen terbit.
     *
     * Nilai bawaan config/sipinjam.php berupa placeholder mencolok seperti
     * "[ISI NAMA PEJABAT]". Bila instalasi produksi lupa mengisinya, surat akan
     * terbit dengan tanda tangan kosong — cacat pada dokumen resmi, dan persis
     * jenis kesalahan yang tidak ketahuan sampai surat sudah beredar.
     *
     * Dikecualikan di 'local' dan 'testing' supaya pengembangan tidak terhambat.
     *
     * @param  list<string>  $grup  Grup config yang wajib lengkap
     *
     * @throws \RuntimeException bila masih ada nilai placeholder
     */
    public function assertKonfigurasiLengkap(array $grup): void
    {
        if (app()->environment(['local', 'testing'])) {
            return;
        }

        $belumDiisi = $this->kunciConfigBelumDiisi($grup);

        if ($belumDiisi === []) {
            return;
        }

        throw new \RuntimeException(
            'Dokumen tidak dapat diterbitkan: identitas surat belum dikonfigurasi. '
            .'Isi kunci berikut di config/sipinjam.php (atau lewat berkas .env): '
            .implode(', ', $belumDiisi).'. '
            .'Nilai bawaannya masih berupa placeholder "[ISI ...]".'
        );
    }

    /**
     * Kunci config yang nilainya masih placeholder pada grup yang diminta.
     *
     * @param  list<string>  $grup
     * @return list<string>
     */
    public function kunciConfigBelumDiisi(array $grup = ['kop', 'penandatangan']): array
    {
        $belumDiisi = [];

        foreach ($grup as $namaGrup) {
            foreach (Arr::dot((array) config("sipinjam.{$namaGrup}", [])) as $kunci => $nilai) {
                if (is_string($nilai) && str_contains($nilai, '[ISI')) {
                    $belumDiisi[] = "sipinjam.{$namaGrup}.{$kunci}";
                }
            }
        }

        return $belumDiisi;
    }

    /**
     * Path binary Node: dari config bila diisi, selebihnya tebakan per OS.
     */
    public function nodeBinary(): ?string
    {
        $configured = config('sipinjam.pdf.node_binary');

        if (! empty($configured)) {
            return $configured;
        }

        return $this->isWindows()
            ? 'C:\\Program Files\\nodejs\\node.exe'
            : '/usr/bin/node';
    }

    /**
     * Path binary npm: dari config bila diisi, selebihnya tebakan per OS.
     */
    public function npmBinary(): ?string
    {
        $configured = config('sipinjam.pdf.npm_binary');

        if (! empty($configured)) {
            return $configured;
        }

        return $this->isWindows()
            ? 'C:\\Program Files\\nodejs\\npm.cmd'
            : '/usr/bin/npm';
    }

    private function isWindows(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }
}
