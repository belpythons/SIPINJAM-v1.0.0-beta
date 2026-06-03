<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Peminjaman;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelPdf\Facades\Pdf;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {}

    public function index(): Response
    {
        // Fix N+1 — eager-load relasi barang & ruangan
        $bookings = Peminjaman::with(['barang', 'ruangan'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $stats = [
            'total'     => $bookings->count(),
            'pending'   => $bookings->where('status', Peminjaman::STATUS_PENDING)->count(),
            'approved'  => $bookings->where('status', Peminjaman::STATUS_APPROVED)->count(),
            'completed' => $bookings->where('status', Peminjaman::STATUS_DONE)->count(),
        ];

        return Inertia::render('User/RiwayatPeminjaman', [
            'bookings' => $bookings,
            'stats'    => $stats,
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $this->bookingService->createBooking($request->validated(), $request->user());
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['booking' => $e->getMessage()]);
        }

        return redirect()->route('dashboard')->with('success', 'Peminjaman berhasil diajukan!');
    }

    /**
     * Generate & download surat peminjaman PDF.
     *
     * Validasi:
     * - Hanya user pemilik booking yang bisa download
     * - Status harus "sedang_dipinjam" (Approved)
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

        // Guard: hanya status "Approved" (sedang_dipinjam) yang bisa cetak surat
        if ($peminjaman->status !== Peminjaman::STATUS_APPROVED) {
            abort(403, 'Surat hanya dapat diunduh untuk peminjaman yang telah disetujui.');
        }

        // Ensure nomor_surat is generated
        if (empty($peminjaman->nomor_surat)) {
            $peminjaman->nomor_surat = Peminjaman::generateNomorSurat();
            $peminjaman->save();
        }

        $nodeBinary = env('NODE_BINARY_PATH');
        $npmBinary = env('NPM_BINARY_PATH');

        if (empty($nodeBinary) || empty($npmBinary)) {
            $isWindows = PHP_OS_FAMILY === 'Windows' || stristr(PHP_OS, 'WIN');
            if ($isWindows) {
                $nodeBinary = $nodeBinary ?: 'C:\\Program Files\\nodejs\\node.exe';
                $npmBinary = $npmBinary ?: 'C:\\Program Files\\nodejs\\npm.cmd';
            } else {
                $nodeBinary = $nodeBinary ?: '/usr/bin/node';
                $npmBinary = $npmBinary ?: '/usr/bin/npm';
            }
        }

        // Render PDF
        $pdf = Pdf::view('pdf.surat-peminjaman', [
            'peminjaman' => $peminjaman,
            'user'       => $peminjaman->user,
            'asset'      => $peminjaman->tipe === 'ruangan'
                                ? $peminjaman->ruangan
                                : $peminjaman->barang,
        ])
        ->format('a4')
        ->withBrowsershot(function ($browsershot) use ($nodeBinary, $npmBinary) {
            $browsershot->noSandbox();
            if (!empty($nodeBinary)) {
                $browsershot->setNodeBinary($nodeBinary);
            }
            if (!empty($npmBinary)) {
                $browsershot->setNpmBinary($npmBinary);
            }
        });

        // Slugified filename
        $safeNomor = str_replace(['/', '\\'], '-', $peminjaman->nomor_surat);
        $filename = "surat-peminjaman-{$safeNomor}.pdf";

        // Download response
        return $pdf->download($filename);
    }
}