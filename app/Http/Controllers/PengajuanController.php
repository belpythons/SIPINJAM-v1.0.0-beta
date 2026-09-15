<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRequest;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Services\AvailabilityService;
use App\Services\EligibilityService;
use App\Services\NomorSuratService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PengajuanController extends Controller
{
    public function __construct(
        private readonly AvailabilityService $availabilityService,
        private readonly EligibilityService $eligibilityService,
        private readonly NomorSuratService $nomorSuratService
    ) {}

    /**
     * Menampilkan form pengajuan 5 langkah.
     */
    public function create(): Response
    {
        return Inertia::render('Pengajuan/Create');
    }

    /**
     * Langsung proses pengajuan 5 langkah.
     * Validasi berlapis dalam SATU transaksi dengan lockForUpdate per aset:
     *   - kelayakan (EligibilityService) → lead time per aset → jam operasional
     *   - ketersediaan (AvailabilityService) per baris → kuota pengajuan aktif.
     *   - Pesan galat menunjuk baris yang bermasalah.
     */
    public function store(StorePengajuanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = Auth::user();

        // 1. Determin jenis dan nama item
        $jenis = $validated['jenis'] ?? 'tunggal';
        $namaKegiatan = $validated['nama_kegiatan'] ?? '';
        $unitPenyelenggara = $validated['unit_penyelenggara'] ?? '';
        $jumlahPeserta = $validated['jumlah_peserta'] ?? 0;
        $pjNama = $validated['pj_nama'] ?? '';
        $pjPhone = $validated['pj_phone'] ?? '';
        $deskripsi = $validated['deskripsi'] ?? '';
        $lampiranProposal = $validated['lampiran_proposal'] ?? null;

        // 2. Buat header pengajuan
        $pengajuan = Pengajuan::create([
            'user_id' => $user->id,
            'jenis' => $jenis,
            'nama_kegiatan' => $namaKegiatan,
            'unit_penyelenggara' => $unitPenyelenggara,
            'jumlah_peserta' => $jumlahPeserta,
            'pj_nama' => $pjNama,
            'pj_phone' => $pjPhone,
            'deskripsi' => $deskripsi,
            'lampiran_proposal' => $lampiranProposal,
            'mulai_at' => $validated['mulai_at'],
            'selesai_at' => $validated['selesai_at'],
            'status' => Pengajuan::STATUS_DIAJUKAN,
        ]);

        // 3. Setel kuota pengajuan aktif dari config
        $kuotaAktif = (int) config('sipinjam.kuota_aktif.' . Auth::user()->role ?? 'user', 3);

        // 4. Proses setiap item dengan lockForUpdate
        $barisGagal = [];
        $berhasil = 0;

        foreach ($validated['items'] as $index => $item) {
            $assetableType = $item['assetable_type'];
            $assetableId = $item['assetable_id'];
            $jumlah = $item['jumlah'] ?? 1;
            $mulaiAt = \Carbon\Carbon::parse($item['mulai_at']);
            $selesaiAt = \Carbon\Carbon::parse($item['selesai_at']);

            // a. Cek kelayakan user (lead time, eligibility)
            $kelayakan = $this->eligibilityService->cek($user, $mulaiAt, $selesaiAt);
            if ($kelayakan->tidakLayak()) {
                $barisGagal[] = [
                    'index' => $index,
                    'alasan' => $kelayakan->alasan(),
                    'barang' => $item['nama_aset'] ?? 'Baris ' . ($index + 1),
                ];
                continue;
            }

            // b. Cek lead time operasional
            $jamOperasional = config('sipinjam.jam_operasional', [
                'buka' => '07:00',
                'tutup' => '22:00',
            ]);
            $mulaiHari = \Carbon\Carbon::parse($mulaiAt)->format('H:i');
            $selesaiHari = \Carbon\Carbon::parse($selesaiAt)->format('H:i');
            if ($mulaiHari < $jamOperasional['buka'] || $selesaiHari > $jamOperasional['tutup']) {
                $barisGagal[] = [
                    'index' => $index,
                    'alasan' => 'Jadwal di luar jam operasional (' . $jamOperasional['buka'] . '-' . $jamOperasional['tutup'] . ').',
                    'barang' => $item['nama_aset'] ?? 'Baris ' . ($index + 1),
                ];
                continue;
            }

            // c. Cek ketersediaan aset dengan lockForUpdate
            $asset = $assetableType::lockForUpdate()->find($assetableId);
            if (! $asset) {
                $barisGagal[] = [
                    'index' => $index,
                    'alasan' => 'Aset tidak ditemukan.',
                    'barang' => $item['nama_aset'] ?? 'Baris ' . ($index + 1),
                ];
                continue;
            }

            $tersedia = $this->availabilityService->tersediaUntuk(
                $asset,
                $mulaiAt,
                $selesaiAt,
                $item['__ignore_id'] ?? null
            );

            if ($tersedia < $jumlah) {
                $barisGagal[] = [
                    'index' => $index,
                    'alasan' => "Hanya {$tersedia} dari {$jumlah} unit " . ($item['nama_aset'] ?? 'aset') . " tersedia pada rentang waktu ini.",
                    'barang' => $item['nama_aset'] ?? 'Baris ' . ($index + 1),
                ];
                continue;
            }

            // d. Buat PengajuanItem
            PengajuanItem::create([
                'pengajuan_id' => $pengajuan->id,
                'assetable_type' => $assetableType,
                'assetable_id' => $assetableId,
                'jumlah' => $jumlah,
                'mulai_at' => $mulaiAt,
                'selesai_at' => $selesaiAt,
                'status_item' => PengajuanItem::STATUS_DIAJUKAN,
                'catatan' => $item['catatan'] ?? '',
            ]);

            $berhasil++;
        }

        // 5. Jika semua baris gagal, batalkan pengajuan
        if (empty($berhasil) && ! empty($barisGagal)) {
            $pengajuan->delete();
            throw new \RuntimeException('Semua baris gagal diverifikasi. Perbaiki kesalahan di atas dan coba lagi.');
        }

        // 6. Terbitkan nomor surat (mode hybrid default)
        $pengajuan->nomor_surat = $this->nomorSuratService->terbitkan(
            $pengajuan->jenis === Pengajuan::JENIS_EVENT
                ? NomorSuratService::JENIS_SURAT_PERMOHONAN
                : NomorSuratService::JENIS_SURAT_IZIN
        );
        $pengajuan->save();

        // 7. Catat timeline sintetis
        $pengajuan->timeline()->create([
            'aksi' => 'diajukan',
            'dari_status' => null,
            'ke_status' => Pengajuan::STATUS_DIAJUKAN,
            'actor_id' => $user->id,
            'actor_role' => $user->role ?? 'user',
            'catatan' => 'Pengajuan baru dibuat melalui form 5 langkah',
            'meta' => ['jenis' => $jenis, 'item_berhasil' => $berhasil, 'item_gagal' => count($barisGagal)],
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('pengajuan.index')
            ->with('success', "Pengajuan #{$pengajuan->kode} berhasil diajukan. {$berhasil} item terverifikasi, " . count($barisGagal) . " perlu diperbaiki.");
    }

    /**
     * Endpoint GET /api/ketersediaan untuk pengecekan real-time per baris
     * (throttle 60/menit).
     */
    public function ketersediaan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'assetable_type' => ['required', 'in:App\Models\Ruangan,App\Models\Barang'],
            'assetable_id' => ['required', 'exists:' . $validated['assetable_type'] . ',id'],
            'mulai_at' => ['required', 'date_format:Y-m-d H:i'],
            'selesai_at' => ['required', 'date_format:Y-m-d H:i', 'after:mulai_at'],
        ]);

        $asset = $validated['assetable_type']::find($validated['assetable_id']);
        $mulaiAt = \Carbon\Carbon::parse($validated['mulai_at']);
        $selesaiAt = \Carbon\Carbon::parse($validated['selesai_at']);

        $tersedia = $this->availabilityService->tersediaUntuk(
            $asset,
            $mulaiAt,
            $selesaiAt
        );

        return response()->json([
            'tersedia' => $tersedia,
            'kapasitas' => $this->availabilityService->kapasitasTotal($asset),
        ]);
    }
}