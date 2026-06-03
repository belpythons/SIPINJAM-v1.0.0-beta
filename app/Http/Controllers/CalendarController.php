<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarRequest;
use App\Models\Calendar;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Spatie\LaravelPdf\Facades\Pdf;

class CalendarController extends Controller
{
    // ── USER: Tampilkan kalender aktif ──────────────────

    public function index()
    {
        $calendar = Calendar::where('is_active', true)->latest()->first();

        return Inertia::render('User/Kalender', [
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

        $pdf = Pdf::view('user.kalender.pdf', [
            'calendar' => $calendar,
            'imagePath' => $base64
        ])
        ->landscape()
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

        return $pdf->download("Kalender_Akademik_STITEK_{$calendar->year}.pdf");
    }

    // ── ADMIN: Kelola kalender ──────────────────────────

    public function adminIndex()
    {
        $calendars = Calendar::orderByDesc('year')->get();

        return Inertia::render('Admin/KelolaKalender', [
            'calendars' => $calendars,
        ]);
    }

    /**
     * Upload gambar/PDF kalender baru.
     */
    public function store(StoreCalendarRequest $request): RedirectResponse
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
    public function setActive(int $id): RedirectResponse
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
    public function destroy(int $id): RedirectResponse
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
