<?php

namespace App\Services;

use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\PengajuanTimeline;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Satu-satunya tempat status pengajuan & baris asetnya boleh berubah.
 *
 * Dua jaminan yang diberikan kelas ini:
 *
 * 1. Transisi yang tidak ada di tabel TRANSISI ditolak — status tidak bisa
 *    melompat sembarangan (mis. langsung `diajukan` → `selesai`).
 * 2. Setiap transisi menulis satu baris `pengajuan_timeline`. Karena hanya
 *    kelas ini yang boleh mengubah status, timeline dijamin utuh: tidak ada
 *    perubahan yang lolos tanpa jejak siapa/kapan/apa/mengapa.
 *
 * Diagram acuan: docs/RENCANA-PENYEMPURNAAN.md §7.1.
 */
class PengajuanStateMachine
{
    /**
     * Transisi sah untuk status header, persis diagram §7.1.
     *
     * @var array<string, list<string>>
     */
    public const TRANSISI = [
        Pengajuan::STATUS_DRAFT => [
            Pengajuan::STATUS_DIAJUKAN,
        ],
        Pengajuan::STATUS_DIAJUKAN => [
            Pengajuan::STATUS_DIVERIFIKASI,
            Pengajuan::STATUS_DITOLAK,
            Pengajuan::STATUS_DIBATALKAN,
            Pengajuan::STATUS_KEDALUWARSA,
        ],
        Pengajuan::STATUS_DIVERIFIKASI => [
            Pengajuan::STATUS_DISETUJUI,
            Pengajuan::STATUS_DITOLAK,
        ],
        Pengajuan::STATUS_DISETUJUI => [
            Pengajuan::STATUS_DISETUJUI_SEBAGIAN,
            Pengajuan::STATUS_BERJALAN,
            Pengajuan::STATUS_DIBATALKAN,
        ],
        Pengajuan::STATUS_DISETUJUI_SEBAGIAN => [
            Pengajuan::STATUS_BERJALAN,
        ],
        Pengajuan::STATUS_BERJALAN => [
            Pengajuan::STATUS_MENUNGGU_PEMERIKSAAN,
        ],
        Pengajuan::STATUS_MENUNGGU_PEMERIKSAAN => [
            Pengajuan::STATUS_SELESAI,
            Pengajuan::STATUS_BERMASALAH,
        ],
        Pengajuan::STATUS_BERMASALAH => [
            Pengajuan::STATUS_SELESAI,
        ],
        // Status tuntas — tidak ada transisi keluar.
        Pengajuan::STATUS_SELESAI => [],
        Pengajuan::STATUS_DITOLAK => [],
        Pengajuan::STATUS_DIBATALKAN => [],
        Pengajuan::STATUS_KEDALUWARSA => [],
    ];

    /**
     * Transisi sah untuk status per baris aset.
     *
     * @var array<string, list<string>>
     */
    public const TRANSISI_ITEM = [
        PengajuanItem::STATUS_DIAJUKAN => [
            PengajuanItem::STATUS_DISETUJUI,
            PengajuanItem::STATUS_DITOLAK,
            PengajuanItem::STATUS_DIBATALKAN,
        ],
        PengajuanItem::STATUS_DISETUJUI => [
            PengajuanItem::STATUS_BERJALAN,
            PengajuanItem::STATUS_DIBATALKAN,
        ],
        PengajuanItem::STATUS_BERJALAN => [
            PengajuanItem::STATUS_SELESAI,
            PengajuanItem::STATUS_BERMASALAH,
        ],
        PengajuanItem::STATUS_BERMASALAH => [
            PengajuanItem::STATUS_SELESAI,
        ],
        PengajuanItem::STATUS_SELESAI => [],
        PengajuanItem::STATUS_DITOLAK => [],
        PengajuanItem::STATUS_DIBATALKAN => [],
    ];

    /**
     * Label status dalam Bahasa Indonesia, untuk pesan galat & timeline.
     */
    public const LABEL = [
        Pengajuan::STATUS_DRAFT => 'Draf',
        Pengajuan::STATUS_DIAJUKAN => 'Diajukan',
        Pengajuan::STATUS_DIVERIFIKASI => 'Diverifikasi',
        Pengajuan::STATUS_DISETUJUI => 'Disetujui',
        Pengajuan::STATUS_DISETUJUI_SEBAGIAN => 'Disetujui sebagian',
        Pengajuan::STATUS_BERJALAN => 'Berjalan',
        Pengajuan::STATUS_MENUNGGU_PEMERIKSAAN => 'Menunggu pemeriksaan',
        Pengajuan::STATUS_SELESAI => 'Selesai',
        Pengajuan::STATUS_BERMASALAH => 'Bermasalah',
        Pengajuan::STATUS_DITOLAK => 'Ditolak',
        Pengajuan::STATUS_DIBATALKAN => 'Dibatalkan',
        Pengajuan::STATUS_KEDALUWARSA => 'Kedaluwarsa',
    ];

    /**
     * Pindahkan pengajuan ke status baru dan catat jejaknya.
     *
     * @param  array<string, mixed>  $meta  Data tambahan; `internal => true`
     *                                      menyembunyikan catatan dari pemohon.
     *
     * @throws DomainException bila transisi tidak sah
     */
    public function transisi(
        Pengajuan $pengajuan,
        string $keStatus,
        ?User $aktor = null,
        ?string $catatan = null,
        array $meta = [],
        ?string $aksi = null,
    ): Pengajuan {
        $dariStatus = $pengajuan->status;

        $this->pastikanTransisiSah($dariStatus, $keStatus);

        return DB::transaction(function () use ($pengajuan, $dariStatus, $keStatus, $aktor, $catatan, $meta, $aksi) {
            $pengajuan->status = $keStatus;
            $pengajuan->save();

            $this->catat($pengajuan, $aksi ?? "Status diubah menjadi {$this->label($keStatus)}",
                $dariStatus, $keStatus, $aktor, $catatan, $meta);

            return $pengajuan;
        });
    }

    /**
     * Pindahkan satu baris aset ke status baru.
     *
     * @throws DomainException bila transisi tidak sah
     */
    public function transisiItem(
        PengajuanItem $item,
        string $keStatus,
        ?User $aktor = null,
        ?string $catatan = null,
        array $meta = [],
    ): PengajuanItem {
        $dariStatus = $item->status_item;

        if ($dariStatus === $keStatus) {
            return $item;
        }

        $sah = self::TRANSISI_ITEM[$dariStatus] ?? null;

        if ($sah === null) {
            throw new DomainException("Status baris aset \"{$dariStatus}\" tidak dikenali.");
        }

        if (! in_array($keStatus, $sah, true)) {
            throw new DomainException(
                "Baris aset tidak dapat berpindah dari \"{$dariStatus}\" ke \"{$keStatus}\"."
            );
        }

        return DB::transaction(function () use ($item, $dariStatus, $keStatus, $aktor, $catatan, $meta) {
            $item->status_item = $keStatus;
            $item->save();

            $namaAset = $item->assetable?->nama ?? 'aset';

            $this->catat(
                $item->pengajuan,
                "Baris aset \"{$namaAset}\" menjadi {$keStatus}",
                $dariStatus,
                $keStatus,
                $aktor,
                $catatan,
                array_merge($meta, ['pengajuan_item_id' => $item->id]),
            );

            return $item;
        });
    }

    /**
     * Apakah perpindahan status header ini diizinkan?
     */
    public function bolehTransisi(string $dariStatus, string $keStatus): bool
    {
        return in_array($keStatus, self::TRANSISI[$dariStatus] ?? [], true);
    }

    /**
     * Status header yang seharusnya, dihitung dari agregat baris aset.
     *
     * Dipakai P3 saat verifikasi per baris: bila sebagian baris ditolak,
     * header menjadi `disetujui_sebagian`, bukan `disetujui`.
     */
    public function statusAgregat(Pengajuan $pengajuan): string
    {
        $items = $pengajuan->items;

        if ($items->isEmpty()) {
            return $pengajuan->status;
        }

        $disetujui = $items->where('status_item', PengajuanItem::STATUS_DISETUJUI)->count();
        $ditolak = $items->where('status_item', PengajuanItem::STATUS_DITOLAK)->count();

        return match (true) {
            $ditolak === $items->count() => Pengajuan::STATUS_DITOLAK,
            $ditolak > 0 && $disetujui > 0 => Pengajuan::STATUS_DISETUJUI_SEBAGIAN,
            $disetujui === $items->count() => Pengajuan::STATUS_DISETUJUI,
            default => $pengajuan->status,
        };
    }

    /**
     * Tulis satu baris timeline. Append-only — tidak pernah diubah/dihapus.
     */
    public function catat(
        Pengajuan $pengajuan,
        string $aksi,
        ?string $dariStatus = null,
        ?string $keStatus = null,
        ?User $aktor = null,
        ?string $catatan = null,
        array $meta = [],
    ): PengajuanTimeline {
        return PengajuanTimeline::create([
            'pengajuan_id' => $pengajuan->id,
            'actor_id' => $aktor?->id,
            'actor_role' => $aktor?->getRoleNames()->first(),
            'aksi' => $aksi,
            'dari_status' => $dariStatus,
            'ke_status' => $keStatus,
            'catatan' => $catatan,
            'meta' => $meta === [] ? null : $meta,
            'ip' => $this->ip(),
            'created_at' => now(),
        ]);
    }

    private function pastikanTransisiSah(string $dariStatus, string $keStatus): void
    {
        if (! array_key_exists($dariStatus, self::TRANSISI)) {
            throw new DomainException("Status \"{$dariStatus}\" tidak dikenali.");
        }

        if ($dariStatus === $keStatus) {
            throw new DomainException(
                "Pengajuan sudah berstatus {$this->label($keStatus)}."
            );
        }

        if (! $this->bolehTransisi($dariStatus, $keStatus)) {
            $tujuan = self::TRANSISI[$dariStatus];

            $penjelasan = $tujuan === []
                ? 'Status itu sudah final dan tidak dapat diubah lagi.'
                : 'Dari status itu hanya dapat berpindah ke: '
                    .implode(', ', array_map(fn ($s) => $this->label($s), $tujuan)).'.';

            throw new DomainException(
                "Pengajuan tidak dapat berpindah dari {$this->label($dariStatus)} "
                ."ke {$this->label($keStatus)}. {$penjelasan}"
            );
        }
    }

    private function label(string $status): string
    {
        return self::LABEL[$status] ?? $status;
    }

    private function ip(): ?string
    {
        // Scheduler & perintah artisan berjalan tanpa request HTTP.
        return app()->runningInConsole() ? null : request()->ip();
    }
}
