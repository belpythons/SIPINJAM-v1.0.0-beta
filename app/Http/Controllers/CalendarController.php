<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CalendarController extends Controller
{
    // ── USER: Tampilkan kalender aktif ──────────────────

    public function index(): \Illuminate\View\View
    {
        $calendar = Calendar::where('is_active', true)->latest()->first();

        return view('user.kalender.index', compact('calendar'));
    }

    /**
     * Export kalender aktif ke PDF.
     * Gambar dikonversi ke Base64 agar DomPDF pasti bisa me-render-nya.
     */
    public function exportPdf(): \Symfony\Component\HttpFoundation\Response
    {
        $calendar = Calendar::where('is_active', true)->latest()->firstOrFail();

        // Dapatkan path absolut gambar dan konversi ke Base64
        $absolutePath = storage_path('app/public/' . $calendar->image_path);
        $imageData    = base64_encode(file_get_contents($absolutePath));
        $mimeType     = mime_content_type($absolutePath);
        $base64Image  = "data:{$mimeType};base64,{$imageData}";

        $pdf = Pdf::loadView('user.kalender.pdf', compact('calendar', 'base64Image'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("Kalender_Akademik_STITEK_{$calendar->year}.pdf");
    }

    // ── ADMIN: Kelola kalender ──────────────────────────

    public function adminIndex(): \Illuminate\View\View
    {
        $calendars = Calendar::orderByDesc('year')->get();

        return view('admin.kelola_kalender', compact('calendars'));
    }

    /**
     * Upload gambar kalender baru.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // max 5MB
            'year'  => 'required|integer|min:2020|max:2100',
        ]);

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
