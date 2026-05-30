<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarRequest;
use App\Models\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CalendarController extends Controller
{
    // ── USER: Tampilkan kalender aktif ──────────────────

    public function index()
    {
        $calendar = Calendar::where('is_active', true)->latest()->first();

        return \Inertia\Inertia::render('User/Kalender', [
            'calendar' => $calendar,
        ]);
    }

    /**
     * Export kalender aktif ke PDF.
     * Gambar dikonversi ke Base64 agar DomPDF pasti bisa me-render-nya.
     */
    public function exportPdf(): \Symfony\Component\HttpFoundation\Response
    {
        $calendar = Calendar::where('is_active', true)->latest()->first();

        if (!$calendar) {
            return redirect()->back()->with('error', 'Tidak ada kalender akademik aktif.');
        }

        $absolutePath = storage_path('app/public/' . $calendar->image_path);

        if (!file_exists($absolutePath)) {
            $publicPath = public_path('storage/' . $calendar->image_path);
            if (file_exists($publicPath)) {
                $absolutePath = $publicPath;
            } else {
                return redirect()->back()->with('error', 'Berkas kalender tidak ditemukan di server.');
            }
        }

        if (strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) === 'pdf') {
            return response()->download($absolutePath, "Kalender_Akademik_STITEK_{$calendar->year}.pdf", [
                'Content-Type' => 'application/pdf',
            ]);
        }

        $pdf = Pdf::loadView('user.kalender.pdf', [
            'calendar' => $calendar,
            'imagePath' => $absolutePath
        ])->setPaper('a4', 'portrait');

        return $pdf->download("Kalender_Akademik_STITEK_{$calendar->year}.pdf");
    }

    // ── ADMIN: Kelola kalender ──────────────────────────

    public function adminIndex()
    {
        $calendars = Calendar::orderByDesc('year')->get();

        return \Inertia\Inertia::render('Admin/KelolaKalender', [
            'calendars' => $calendars,
        ]);
    }

    /**
     * Upload gambar/PDF kalender baru.
     */
    public function store(StoreCalendarRequest $request): \Illuminate\Http\RedirectResponse
    {

        try {
            $path = $request->file('image')->store('calendars', 'public');

            Calendar::create([
                'image_path' => $path,
                'year'       => $request->year,
                'is_active'  => false,
            ]);

            return redirect()->back()->with('success', 'Kalender berhasil diunggah!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunggah kalender: ' . $e->getMessage());
        }
    }

    /**
     * Set kalender sebagai aktif (hanya satu yang aktif pada satu waktu).
     */
    public function setActive(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            // Non-aktifkan semua kalender terlebih dahulu
            Calendar::where('is_active', true)->update(['is_active' => false]);

            // Aktifkan yang dipilih
            $calendar = Calendar::findOrFail($id);
            $calendar->update(['is_active' => true]);

            return redirect()->back()->with('success', "Kalender tahun {$calendar->year} berhasil diaktifkan!");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengaktifkan kalender: ' . $e->getMessage());
        }
    }

    /**
     * Hapus kalender.
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $calendar = Calendar::findOrFail($id);

            // Hapus file gambar dari storage
            if (Storage::disk('public')->exists($calendar->image_path)) {
                Storage::disk('public')->delete($calendar->image_path);
            }

            $calendar->delete();

            return redirect()->back()->with('success', 'Kalender berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kalender: ' . $e->getMessage());
        }
    }
}
