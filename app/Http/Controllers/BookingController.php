<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Dokumen;
use App\Models\Peminjaman;
use App\Services\BookingService;
use App\Services\NomorSuratService;
use App\Services\PdfRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly PdfRenderer $pdfRenderer
    ) {}

    public function index(): Response
    {
        $status = request('status');

        // Fix N+1 — eager-load relasi barang & ruangan
        // B-20: dipaginasi agar tidak memuat seluruh riwayat ke memori.
        $bookings = Peminjaman::with(['barang', 'ruangan'])
            ->where('user_id', Auth::id())
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Statistik dihitung lewat kueri agregat, bukan dari koleksi halaman
        // saat ini — kalau tidak, angkanya hanya mencerminkan 20 baris teratas.
        $hitung = fn (?string $s) => Peminjaman::where('user_id', Auth::id())
            ->when($s !== null, fn ($q) => $q->where('status', $s))
            ->count();

        $stats = [
            'total' => $hitung(null),
            'pending' => $hitung(Peminjaman::STATUS_PENDING),
            'approved' => $hitung(Peminjaman::STATUS_APPROVED),
            'completed' => $hitung(Peminjaman::STATUS_DONE),
        ];

        return Inertia::render('User/RiwayatPeminjaman', [
            'bookings' => $bookings,
            'stats' => $stats,
            'filters' => ['status' => $status],
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $peminjaman = $this->bookingService->createBooking($request->validated(), $request->user());
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['booking' => $e->getMessage()]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Peminjaman berhasil diajukan!')
            ->with('booking_created_id', $peminjaman->id);
    }

    /**
     * Generate & download surat peminjaman PDF.
     *
     * Validasi:
     * - Hanya user pemilik booking yang bisa download
     * - Bisa diunduh sejak status "menunggu" (self-service), kecuali sudah "ditolak"
     *
     * Alur:
     * 1. Jika nomor_surat belum ada → generate & simpan
     * 2. Render Blade template → PDF via DomPDF
     * 3. Simpan fisik ke storage/app/public/surat/
     * 4. Return download response
     */
    public function generatePDF(int $id)
    {
        $peminjaman = Peminjaman::with(['user', 'ruangan', 'barang'])
            ->findOrFail($id);

        // Guard: hanya pemilik yang bisa download
        if ($peminjaman->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke surat ini.');
        }

        // Guard: surat bisa diunduh sejak diajukan (self-service), kecuali sudah ditolak
        if ($peminjaman->status === Peminjaman::STATUS_REJECTED) {
            abort(403, 'Surat tidak tersedia karena peminjaman ini telah ditolak.');
        }

        // Ensure nomor_surat is generated
        if (empty($peminjaman->nomor_surat)) {
            $peminjaman->nomor_surat = app(NomorSuratService::class)->terbitkan(Dokumen::JENIS_SURAT_IZIN);
            $peminjaman->save();
        }

        // Render PDF
        $pdf = $this->pdfRenderer->renderDokumenResmi('pdf.surat-peminjaman', [
            'peminjaman' => $peminjaman,
            'user' => $peminjaman->user,
            'asset' => $peminjaman->tipe === 'ruangan'
                                ? $peminjaman->ruangan
                                : $peminjaman->barang,
        ]);

        // Slugified filename
        $safeNomor = str_replace(['/', '\\'], '-', $peminjaman->nomor_surat);
        $filename = "surat-peminjaman-{$safeNomor}.pdf";

        // Download response
        return $pdf->download($filename);
    }
}
