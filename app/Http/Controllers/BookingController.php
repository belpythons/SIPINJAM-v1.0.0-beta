<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Peminjaman;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {}

    public function index(): \Illuminate\View\View
    {
        $bookings = Peminjaman::where('user_id', Auth::id())->latest()->get();

        $stats = [
            'total'     => $bookings->count(),
            'pending'   => $bookings->where('status', 'menunggu')->count(),
            'approved'  => $bookings->where('status', 'sedang_dipinjam')->count(),
            'completed' => $bookings->where('status', 'selesai')->count(),
        ];

        return view('user.bookings.index', compact('bookings', 'stats'));
    }

    public function store(StoreBookingRequest $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $this->bookingService->createBooking($request->validated(), $request->user());
        } catch (\RuntimeException $e) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $e->getMessage()], 422)
                : redirect()->back()->withErrors(['booking' => $e->getMessage()]);
        }

        return $request->wantsJson()
            ? response()->json(['success' => true, 'message' => 'Peminjaman berhasil diajukan!'])
            : redirect()->route('bookings.index')->with('success', 'Peminjaman berhasil diajukan!');
    }

    public function generatePDF(int $id): \Symfony\Component\HttpFoundation\Response
    {
        $booking = Peminjaman::with('user')->findOrFail($id);

        $this->authorize('downloadPdf', $booking);

        $pdf = Pdf::loadView('user.bookings.pdf', compact('booking'));

        return $pdf->download('Bukti-Peminjaman-' . $booking->id . '.pdf');
    }
}