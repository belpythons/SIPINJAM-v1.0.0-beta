<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Peminjaman;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {}

    public function index(): \Inertia\Response
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

    public function store(StoreBookingRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->bookingService->createBooking($request->validated(), $request->user());
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['booking' => $e->getMessage()]);
        }

        return redirect()->route('dashboard')->with('success', 'Peminjaman berhasil diajukan!');
    }
}