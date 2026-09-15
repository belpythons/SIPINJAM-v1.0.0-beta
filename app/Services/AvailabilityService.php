<?php

namespace App\Services;

use App\Models\AssetBlackout;
use App\Models\Barang;
use App\Models\Pelanggaran;
use App\Models\PengajuanItem;
use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Ketersediaan aset berbasis WAKTU, menggantikan penghitung `stok_tersedia`.
 *
 * Masalah penghitung lama: ia satu angka untuk seluruh masa depan, dipotong
 * saat approve dan dikembalikan saat selesai. Akibatnya sistem menolak
 * peminjaman bulan depan hanya karena stok habis hari ini (B-02).
 *
 * Rumus di sini (§5.3.3) selalu terikat rentang waktu tertentu:
 *
 *   tersedia = stok_total
 *            − Σ item beririsan berstatus {disetujui, berjalan}
 *            − Σ yang tercatat rusak/hilang dan belum diganti
 *            − Σ yang sedang dalam blackout
 *
 * Seluruh kueri memakai indeks komposit (assetable_type, assetable_id,
 * mulai_at, selesai_at); tidak ada CONCAT dan tidak ada percabangan driver.
 */
class AvailabilityService
{
    /**
     * Berapa unit aset ini yang masih bisa dipinjam pada rentang tersebut?
     *
     * @param  int|null  $abaikanItemId  Baris yang sedang diproses — dikecualikan
     *                                   agar tidak menghitung dirinya sendiri.
     */
    public function tersediaUntuk(
        Model $asset,
        Carbon $mulai,
        Carbon $selesai,
        ?int $abaikanItemId = null,
    ): int {
        [$mulai, $selesai] = $this->denganBuffer($asset, $mulai, $selesai);

        if ($this->sedangBlackout($asset, $mulai, $selesai)) {
            return 0;
        }

        $total = $this->kapasitasTotal($asset);
        $terpakai = $this->jumlahTerpakai($asset, $mulai, $selesai, $abaikanItemId);

        return max(0, $total - $terpakai);
    }

    /**
     * Cukupkah untuk permintaan sebanyak $jumlah?
     */
    public function cukup(
        Model $asset,
        Carbon $mulai,
        Carbon $selesai,
        int $jumlah = 1,
        ?int $abaikanItemId = null,
    ): bool {
        return $this->tersediaUntuk($asset, $mulai, $selesai, $abaikanItemId) >= $jumlah;
    }

    /**
     * Jadwal yang sudah terpakai pada sebuah aset — untuk kalender & UI.
     *
     * @return Collection<int, PengajuanItem>
     */
    public function jadwalTerpakai(Model $asset, Carbon $mulai, Carbon $selesai): Collection
    {
        return PengajuanItem::query()
            ->untukAset($asset::class, $asset->getKey())
            ->memesanSlot()
            ->beririsan($mulai, $selesai)
            ->with('pengajuan:id,kode,nama_kegiatan,unit_penyelenggara,status')
            ->orderBy('mulai_at')
            ->get();
    }

    /**
     * Kapasitas total aset pada satu waktu.
     *
     * Ruangan selalu 1 — sebuah ruangan tidak bisa dipakai dua kegiatan
     * sekaligus. Barang memakai stok_total, bukan stok_tersedia: pengurangannya
     * dihitung dari irisan waktu, bukan dari penghitung yang bergerak.
     */
    public function kapasitasTotal(Model $asset): int
    {
        return match (true) {
            $asset instanceof Ruangan => 1,
            $asset instanceof Barang => (int) $asset->stok_total,
            default => 0,
        };
    }

    /**
     * Jumlah unit yang sudah dipesan pada rentang tersebut.
     */
    private function jumlahTerpakai(
        Model $asset,
        Carbon $mulai,
        Carbon $selesai,
        ?int $abaikanItemId,
    ): int {
        $terpesan = (int) PengajuanItem::query()
            ->untukAset($asset::class, $asset->getKey())
            ->memesanSlot()
            ->beririsan($mulai, $selesai)
            ->when($abaikanItemId !== null, fn ($q) => $q->where('id', '!=', $abaikanItemId))
            ->sum('jumlah');

        return $terpesan + $this->jumlahRusakAtauHilang($asset);
    }

    /**
     * Unit yang tercatat rusak/hilang dan belum diganti.
     *
     * Berlaku lintas waktu (bukan per rentang) — barang yang hilang tidak
     * tersedia kapan pun sampai ganti ruginya selesai.
     */
    private function jumlahRusakAtauHilang(Model $asset): int
    {
        if (! $asset instanceof Barang) {
            return 0;
        }

        return (int) Pelanggaran::query()
            ->whereIn('jenis', ['rusak_berat', 'hilang'])
            ->belumLunas()
            ->whereHas('item', fn ($q) => $q->untukAset($asset::class, $asset->getKey()))
            ->count();
    }

    /**
     * Apakah aset sedang dalam masa blackout (pemeliharaan/libur/acara internal)?
     */
    public function sedangBlackout(Model $asset, Carbon $mulai, Carbon $selesai): bool
    {
        return AssetBlackout::query()
            ->untukAset($asset::class, $asset->getKey())
            ->beririsan($mulai, $selesai)
            ->exists();
    }

    /**
     * Lebarkan rentang dengan buffer bersih-bersih untuk ruangan.
     *
     * Tanpa ini dua kegiatan bisa menempel persis, dan petugas tidak punya
     * waktu merapikan ruangan di antaranya.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function denganBuffer(Model $asset, Carbon $mulai, Carbon $selesai): array
    {
        if (! $asset instanceof Ruangan) {
            return [$mulai, $selesai];
        }

        $menit = $asset->buffer_menit ?? (int) config('sipinjam.buffer_menit', 30);

        if ($menit <= 0) {
            return [$mulai, $selesai];
        }

        return [
            $mulai->copy()->subMinutes($menit),
            $selesai->copy()->addMinutes($menit),
        ];
    }
}
