<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarRequest;
use App\Models\Calendar;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

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
    public function exportPdf()
    {
        $calendar = Calendar::where('is_active', true)->latest()->first();

        if (!$calendar) {
            return redirect()->back()->with('error', 'Tidak ada kalender akademik aktif.');
        }

        $cleanPath = ltrim($calendar->image_path, '/');

        if (str_starts_with($cleanPath, 'image/')) {
            $absolutePath = public_path($cleanPath);
        } else {
            if (str_starts_with($cleanPath, 'storage/')) {
                $cleanPath = substr($cleanPath, 8);
            }
            $absolutePath = storage_path('app/public/' . $cleanPath);
        }

        if (!file_exists($absolutePath)) {
            $fallbackPath = public_path('storage/' . $cleanPath);
            if (file_exists($fallbackPath)) {
                $absolutePath = $fallbackPath;
            } else {
                if (str_starts_with($cleanPath, 'public/')) {
                    $fallbackPath2 = storage_path('app/' . $cleanPath);
                    if (file_exists($fallbackPath2)) {
                        $absolutePath = $fallbackPath2;
                    } else {
                        return redirect()->back()->with('error', 'Berkas kalender tidak ditemukan di server.');
                    }
                } else {
                    return redirect()->back()->with('error', 'Berkas kalender tidak ditemukan di server.');
                }
            }
        }

        if (strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) === 'pdf') {
            return response()->download($absolutePath, "Kalender_Akademik_STITEK_{$calendar->year}.pdf", [
                'Content-Type' => 'application/pdf',
            ]);
        }

        // Convert image to base64 to ensure it renders correctly in headless Chrome
        $type = pathinfo($absolutePath, PATHINFO_EXTENSION);
        $data = file_get_contents($absolutePath);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $pdf = Pdf::view('user.kalender.pdf', [
            'calendar' => $calendar,
            'imagePath' => $base64
        ])
        ->landscape()
        ->format('a4')
        ->withBrowsershot(fn ($browsershot) => $browsershot
            ->noSandbox()
            ->setNodeBinary('/home/blewah/.local/share/fnm/node-versions/v20.20.2/installation/bin/node')
            ->setNpmBinary('/home/blewah/.local/share/fnm/node-versions/v20.20.2/installation/bin/npm')
        );

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
            $path = null;
            if ($request->hasFile('image_path')) {
                $path = ImageService::cropAndSave($request->file('image_path'), 'calendars');
            }

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

            ImageService::deleteOldImage($calendar->image_path);

            $calendar->delete();

            return redirect()->back()->with('success', 'Kalender berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kalender: ' . $e->getMessage());
        }
    }
}
