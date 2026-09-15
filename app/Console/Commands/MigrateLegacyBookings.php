<?php

namespace App\Console\Commands;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\PengajuanTimeline;
use App\Models\Ruangan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Migrasikan `peminjamans` lama menjadi `pengajuan` + `pengajuan_items`.
 *
 * Satu baris lama → satu header (jenis=tunggal) + satu baris aset.
 *
 * IDEMPOTEN. Kunci idempotensinya adalah `pengajuan.kode` yang diturunkan
 * secara deterministik dari id baris lama (LGC-000123) dan dijaga unique index.
 * Menjalankan perintah ini dua kali tidak menggandakan apa pun — itu penting,
 * karena tabel lama MASIH HIDUP selama P1–P2 dan perintah ini akan dijalankan
 * ulang di P3 untuk menyusul data yang lahir setelahnya.
 *
 * Tabel lama sengaja TIDAK di-rename di sini: 22 berkas masih mengonsumsi model
 * Peminjaman, dan kriteria P1 menuntut UI lama tetap berfungsi. Rename dilakukan
 * di P3 saat jalur baru benar-benar menggantikannya.
 */
class MigrateLegacyBookings extends Command
{
    protected $signature = 'sipinjam:migrate-legacy {--dry-run : Tampilkan ringkasan tanpa menulis apa pun}';

    protected $description = 'Migrasikan data peminjamans lama ke struktur pengajuan + pengajuan_items';

    /** Pemetaan status header (§P1 butir 6). */
    public const PETA_STATUS = [
        Peminjaman::STATUS_PENDING => Pengajuan::STATUS_DIAJUKAN,
        Peminjaman::STATUS_APPROVED => Pengajuan::STATUS_BERJALAN,
        Peminjaman::STATUS_DONE => Pengajuan::STATUS_SELESAI,
        Peminjaman::STATUS_REJECTED => Pengajuan::STATUS_DITOLAK,
    ];

    /** Pemetaan status baris aset, sejalan dengan header. */
    public const PETA_STATUS_ITEM = [
        Peminjaman::STATUS_PENDING => PengajuanItem::STATUS_DIAJUKAN,
        Peminjaman::STATUS_APPROVED => PengajuanItem::STATUS_BERJALAN,
        Peminjaman::STATUS_DONE => PengajuanItem::STATUS_SELESAI,
        Peminjaman::STATUS_REJECTED => PengajuanItem::STATUS_DITOLAK,
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('MODE PRATINJAU — tidak ada satu pun data yang ditulis.');
        }

        $ringkasan = ['dibuat' => 0, 'dilewati' => 0, 'tanpa_aset' => 0, 'per_status' => []];

        // chunkById, bukan get(): tabel peminjamans bisa berisi ribuan baris dan
        // memuat semuanya ke memori adalah cacat yang sama dengan B-04.
        Peminjaman::query()
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($dryRun, &$ringkasan) {
                foreach ($rows as $lama) {
                    $this->prosesSatu($lama, $dryRun, $ringkasan);
                }
            });

        $this->tampilkanRingkasan($ringkasan, $dryRun);

        return self::SUCCESS;
    }

    private function prosesSatu(Peminjaman $lama, bool $dryRun, array &$ringkasan): void
    {
        $kode = $this->kodeUntuk($lama);

        if (Pengajuan::withTrashed()->where('kode', $kode)->exists()) {
            $ringkasan['dilewati']++;

            return;
        }

        [$assetableType, $assetableId] = $this->asetDari($lama);

        if ($assetableType === null) {
            // Baris lama tanpa relasi aset — tidak dapat direpresentasikan
            // sebagai baris pengajuan. Dilaporkan, tidak didiamkan.
            $ringkasan['tanpa_aset']++;

            return;
        }

        $statusBaru = self::PETA_STATUS[$lama->status] ?? Pengajuan::STATUS_SELESAI;
        $ringkasan['per_status'][$statusBaru] = ($ringkasan['per_status'][$statusBaru] ?? 0) + 1;
        $ringkasan['dibuat']++;

        if ($dryRun) {
            return;
        }

        DB::transaction(function () use ($lama, $kode, $assetableType, $assetableId, $statusBaru) {
            $mulai = $lama->mulai_at ?? $lama->tanggal_mulai;
            $selesai = $lama->selesai_at ?? $lama->tanggal_selesai;

            $pengajuan = Pengajuan::create([
                'kode' => $kode,
                'user_id' => $lama->user_id,
                'jenis' => Pengajuan::JENIS_TUNGGAL,
                'deskripsi' => $lama->keterangan,
                'mulai_at' => $mulai,
                'selesai_at' => $selesai,
                'status' => $statusBaru,
                'nomor_surat' => $lama->nomor_surat,
                'submitted_at' => $lama->created_at,
                'approved_at' => $lama->approved_at,
                'alasan_penolakan' => $lama->alasan_sistem,
                'created_at' => $lama->created_at,
                'updated_at' => $lama->updated_at,
            ]);

            PengajuanItem::create([
                'pengajuan_id' => $pengajuan->id,
                'assetable_type' => $assetableType,
                'assetable_id' => $assetableId,
                'jumlah' => $lama->jumlah ?? 1,
                'mulai_at' => $mulai,
                'selesai_at' => $selesai,
                'status_item' => self::PETA_STATUS_ITEM[$lama->status] ?? PengajuanItem::STATUS_SELESAI,
                'catatan' => $lama->keterangan,
            ]);

            PengajuanTimeline::create([
                'pengajuan_id' => $pengajuan->id,
                'actor_id' => null,
                'actor_role' => null,
                'aksi' => 'Dimigrasikan dari data lama',
                'dari_status' => null,
                'ke_status' => $statusBaru,
                'catatan' => "Berasal dari peminjamans #{$lama->id} (status lama: {$lama->status}).",
                'meta' => ['sumber' => 'peminjamans', 'peminjaman_id' => $lama->id],
                'created_at' => $lama->created_at ?? now(),
            ]);
        });
    }

    /**
     * Kode deterministik — inilah yang membuat perintah ini idempoten.
     */
    public function kodeUntuk(Peminjaman $lama): string
    {
        return 'LGC-'.str_pad((string) $lama->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * @return array{0: ?string, 1: ?int}
     */
    private function asetDari(Peminjaman $lama): array
    {
        if ($lama->tipe === 'ruangan' && $lama->ruangan_id) {
            return [Ruangan::class, (int) $lama->ruangan_id];
        }

        if ($lama->tipe === 'barang' && $lama->barang_id) {
            return [Barang::class, (int) $lama->barang_id];
        }

        return [null, null];
    }

    private function tampilkanRingkasan(array $ringkasan, bool $dryRun): void
    {
        $this->newLine();
        $this->info($dryRun ? 'Ringkasan pratinjau:' : 'Migrasi selesai:');

        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                [$dryRun ? 'Akan dibuat' : 'Dibuat', $ringkasan['dibuat']],
                ['Dilewati (sudah pernah dimigrasikan)', $ringkasan['dilewati']],
                ['Dilewati (tanpa relasi aset)', $ringkasan['tanpa_aset']],
            ],
        );

        if ($ringkasan['per_status'] !== []) {
            $this->line('Pemetaan status:');
            foreach ($ringkasan['per_status'] as $status => $jumlah) {
                $this->line("  {$status}: {$jumlah}");
            }
        }

        if ($ringkasan['tanpa_aset'] > 0) {
            $this->warn(
                "{$ringkasan['tanpa_aset']} baris lama tidak punya relasi ruangan/barang "
                .'dan tidak dapat dimigrasikan. Periksa manual sebelum rilis.'
            );
        }
    }
}
