<?php

namespace App\Services;

use App\Models\Pengajuan;
use App\Models\Ruangan;
use App\Models\TataTertibVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Hasil pemeriksaan kelayakan — daftar alasan, bukan sekadar boolean.
 *
 * UI perlu MENJELASKAN kenapa seseorang tidak boleh mengajukan; "false" saja
 * memaksa pengguna menebak.
 */
class HasilKelayakan
{
    /** @param  list<string>  $alasan */
    public function __construct(public readonly array $alasan = []) {}

    public function layak(): bool
    {
        return $this->alasan === [];
    }

    /** @return list<string> */
    public function alasan(): array
    {
        return $this->alasan;
    }

    public function pesan(): string
    {
        return implode(' ', $this->alasan);
    }
}

/**
 * Aturan kelayakan meminjam (§5.3.1), seluruhnya dari config/sipinjam.php.
 *
 * Tiga aturan bergantung pada fase yang belum berjalan (verifikasi WA di P2,
 * verifikasi email di P2, persetujuan tata tertib di P7), jadi masing-masing
 * punya sakelar di config dan dimatikan dulu — kalau tidak, seluruh pengguna
 * terkunci sebelum alurnya dibangun.
 */
class EligibilityService
{
    public function __construct(
        private readonly AvailabilityService $availability,
    ) {}

    /**
     * Periksa apakah user boleh mengajukan peminjaman.
     */
    public function periksa(User $user): HasilKelayakan
    {
        $alasan = [];

        // Aturan 2 — tidak sedang kena sanksi aktif.
        if ($user->isBlocked()) {
            $sanksi = $user->sanksiAktif();
            $sampai = $sanksi?->sampai_at ?? $user->blocked_until;

            $alasan[] = $sampai
                ? 'Akun Anda sedang diblokir sampai '.Carbon::parse($sampai)->translatedFormat('d F Y').'.'
                : 'Akun Anda sedang diblokir. Hubungi admin untuk penyelesaiannya.';
        }

        // Aturan 1 — akun terverifikasi.
        if (config('sipinjam.kelayakan.wajib_email_terverifikasi') && $user->email_verified_at === null) {
            $alasan[] = 'Email kampus Anda belum terverifikasi.';
        }

        if (config('sipinjam.kelayakan.wajib_phone_terverifikasi') && $user->phone_verified_at === null) {
            $alasan[] = 'Nomor WhatsApp Anda belum terverifikasi.';
        }

        // Aturan 3 — kuota pengajuan aktif bersamaan.
        $kuota = $this->kuotaUntuk($user);
        $aktif = $this->jumlahPengajuanAktif($user);

        if ($aktif >= $kuota) {
            $alasan[] = "Anda sudah punya {$aktif} pengajuan yang masih berjalan "
                ."(batas {$kuota}). Selesaikan salah satunya terlebih dahulu.";
        }

        // Aturan 4 — tidak punya tunggakan pengembalian/ganti rugi.
        if ($this->punyaTunggakan($user)) {
            $alasan[] = 'Masih ada pengembalian atau ganti rugi yang belum Anda selesaikan.';
        }

        // Aturan 6 — sudah menyetujui tata tertib versi berlaku.
        if (config('sipinjam.kelayakan.wajib_tata_tertib_disetujui') && ! $this->sudahSetujuTataTertib($user)) {
            $alasan[] = 'Anda belum menyetujui tata tertib versi terbaru.';
        }

        return new HasilKelayakan($alasan);
    }

    /**
     * Periksa kelayakan satu baris aset pada rentang waktu tertentu.
     *
     * Menggabungkan aturan 5 (aset boleh diakses peran tersebut), lead time,
     * jam operasional, dan ketersediaan.
     */
    public function periksaAset(
        User $user,
        Model $asset,
        Carbon $mulai,
        Carbon $selesai,
        int $jumlah = 1,
        ?int $abaikanItemId = null,
    ): HasilKelayakan {
        $alasan = [];
        $nama = $asset->nama ?? 'Aset';

        if ($selesai->lessThanOrEqualTo($mulai)) {
            $alasan[] = "{$nama}: waktu selesai harus setelah waktu mulai.";

            return new HasilKelayakan($alasan);
        }

        // Aturan 5 — pembatasan peran per aset.
        $peranDiizinkan = $asset->role_diizinkan ?? null;

        if (is_array($peranDiizinkan) && $peranDiizinkan !== []
            && ! $user->hasAnyRole($peranDiizinkan)) {
            $alasan[] = "{$nama}: tidak tersedia untuk peran Anda.";
        }

        // Lead time minimum.
        $jamMinimum = $this->leadTimeJam($asset);

        if ($jamMinimum > 0 && now()->diffInHours($mulai, false) < $jamMinimum) {
            $hari = (int) ceil($jamMinimum / 24);
            $alasan[] = "{$nama}: pengajuan paling lambat H-{$hari} sebelum kegiatan.";
        }

        // Jam operasional.
        if ($pesanJam = $this->langgarJamOperasional($asset, $mulai, $selesai)) {
            $alasan[] = "{$nama}: {$pesanJam}";
        }

        // Ketersediaan pada rentang tersebut.
        $tersedia = $this->availability->tersediaUntuk($asset, $mulai, $selesai, $abaikanItemId);

        if ($tersedia < $jumlah) {
            $alasan[] = $tersedia === 0
                ? "{$nama}: tidak tersedia pada ".$this->rentang($mulai, $selesai).'.'
                : "{$nama}: hanya tersedia {$tersedia} dari {$jumlah} unit pada ".$this->rentang($mulai, $selesai).'.';
        }

        return new HasilKelayakan($alasan);
    }

    /**
     * Kuota pengajuan aktif bersamaan untuk peran user.
     */
    public function kuotaUntuk(User $user): int
    {
        $kuota = (array) config('sipinjam.kuota_aktif', []);

        foreach ($user->getRoleNames() as $peran) {
            if (isset($kuota[$peran])) {
                return (int) $kuota[$peran];
            }
        }

        return (int) ($kuota['default'] ?? 3);
    }

    public function jumlahPengajuanAktif(User $user): int
    {
        return $user->pengajuan()
            ->aktif()
            ->where('status', '!=', Pengajuan::STATUS_DRAFT)
            ->count();
    }

    /**
     * Tunggakan: pelanggaran yang tindak lanjutnya belum lunas.
     */
    private function punyaTunggakan(User $user): bool
    {
        return $user->pelanggaran()->belumLunas()->exists();
    }

    private function sudahSetujuTataTertib(User $user): bool
    {
        $versi = TataTertibVersion::aktif();

        if ($versi === null) {
            return true; // Belum ada tata tertib terbit — tidak ada yang dilanggar.
        }

        return $user->agreements()
            ->where('tata_tertib_version_id', $versi->id)
            ->exists();
    }

    /**
     * Lead time minimum aset dalam jam: dari kolom aset bila diisi, selebihnya
     * dari config sesuai jenisnya.
     */
    private function leadTimeJam(Model $asset): int
    {
        if (! empty($asset->min_lead_time_jam)) {
            return (int) $asset->min_lead_time_jam;
        }

        $hari = $asset instanceof Ruangan
            ? (int) config('sipinjam.lead_time.ruangan_hari', 2)
            : (int) config('sipinjam.lead_time.barang_hari', 1);

        return $hari * 24;
    }

    /**
     * Apakah rentang keluar dari jam operasional?
     */
    private function langgarJamOperasional(Model $asset, Carbon $mulai, Carbon $selesai): ?string
    {
        $buka = $asset->jam_buka ?? config('sipinjam.jam_operasional.buka', '07:00');
        $tutup = $asset->jam_tutup ?? config('sipinjam.jam_operasional.tutup', '22:00');

        $jamMulai = $mulai->format('H:i');
        $jamSelesai = $selesai->format('H:i');

        $bukaJam = substr((string) $buka, 0, 5);
        $tutupJam = substr((string) $tutup, 0, 5);

        if ($jamMulai < $bukaJam || ($mulai->isSameDay($selesai) && $jamSelesai > $tutupJam)) {
            return "hanya dapat dipakai pada pukul {$bukaJam}–{$tutupJam}.";
        }

        return null;
    }

    private function rentang(Carbon $mulai, Carbon $selesai): string
    {
        return $mulai->translatedFormat('d M Y H:i').'–'.$selesai->format('H:i');
    }
}
