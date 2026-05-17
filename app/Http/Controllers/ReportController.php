<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Ruangan;
use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ────────────────────────────────────────
    // ADMIN: Laporan Keseluruhan
    // ────────────────────────────────────────
    public function adminIndex(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());

        $query = Peminjaman::with(['user', 'barang', 'ruangan'])
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);

        // Statistik ringkasan
        $stats = [
            'total'          => (clone $query)->count(),
            'pending'        => (clone $query)->where('status', Peminjaman::STATUS_PENDING)->count(),
            'approved'       => (clone $query)->where('status', Peminjaman::STATUS_APPROVED)->count(),
            'rejected'       => (clone $query)->where('status', Peminjaman::STATUS_REJECTED)->count(),
            'done'           => (clone $query)->where('status', Peminjaman::STATUS_DONE)->count(),
            'total_users'    => User::where('role', 'user')->count(),
            'total_ruangan'  => Ruangan::count(),
            'total_barang'   => Barang::count(),
        ];

        // Utilisasi ruangan (top 5)
        $topRuangan = Peminjaman::where('tipe', 'ruangan')
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->selectRaw('ruangan_id, COUNT(*) as total')
            ->groupBy('ruangan_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('ruangan')
            ->get();

        // Utilisasi barang (top 5)
        $topBarang = Peminjaman::where('tipe', 'barang')
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->selectRaw('barang_id, COUNT(*) as total')
            ->groupBy('barang_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('barang')
            ->get();

        $peminjamans = $query->orderBy('created_at', 'desc')->get();

        return view('admin.laporan', compact(
            'stats', 'peminjamans', 'topRuangan', 'topBarang',
            'startDate', 'endDate'
        ));
    }

    /**
     * Export PDF Laporan Admin.
     */
    public function adminExportPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());

        $peminjamans = Peminjaman::with(['user', 'barang', 'ruangan'])
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total'    => $peminjamans->count(),
            'pending'  => $peminjamans->where('status', Peminjaman::STATUS_PENDING)->count(),
            'approved' => $peminjamans->where('status', Peminjaman::STATUS_APPROVED)->count(),
            'rejected' => $peminjamans->where('status', Peminjaman::STATUS_REJECTED)->count(),
            'done'     => $peminjamans->where('status', Peminjaman::STATUS_DONE)->count(),
        ];

        $pdf = Pdf::loadView('reports.peminjaman_pdf', compact(
            'peminjamans', 'stats', 'startDate', 'endDate'
        ))->setPaper('a4', 'landscape');

        $filename = 'Laporan_Peminjaman_' . $startDate . '_' . $endDate . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export Excel (CSV) Laporan Admin.
     * Menggunakan native CSV karena maatwebsite/excel tidak kompatibel PHP 8.5
     */
    public function adminExportExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());

        $peminjamans = Peminjaman::with(['user', 'barang', 'ruangan'])
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'Laporan_Peminjaman_' . $startDate . '_' . $endDate . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($peminjamans) {
            $file = fopen('php://output', 'w');

            // BOM for UTF-8 support in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header Row
            fputcsv($file, [
                'No', 'Tanggal', 'Peminjam', 'Tipe', 'Item',
                'Tanggal Mulai', 'Tanggal Selesai', 'Jam Mulai', 'Jam Selesai',
                'Keterangan', 'Status',
            ]);

            foreach ($peminjamans as $index => $p) {
                $itemName = $p->tipe === 'ruangan'
                    ? ($p->ruangan->nama ?? $p->nama_item ?? '-')
                    : ($p->barang->nama ?? $p->nama_item ?? '-');

                fputcsv($file, [
                    $index + 1,
                    $p->created_at?->format('d/m/Y'),
                    $p->user->name ?? '-',
                    ucfirst($p->tipe ?? '-'),
                    $itemName,
                    $p->tanggal_mulai?->format('d/m/Y') ?? '-',
                    $p->tanggal_selesai?->format('d/m/Y') ?? '-',
                    $p->jam_mulai ?? '-',
                    $p->jam_selesai ?? '-',
                    $p->keterangan ?? '-',
                    ucfirst(str_replace('_', ' ', $p->status ?? '-')),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ────────────────────────────────────────
    // USER: Laporan Pribadi
    // ────────────────────────────────────────
    public function userIndex(Request $request)
    {
        $user = Auth::user();

        $query = Peminjaman::with(['barang', 'ruangan'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $peminjamans = $query->get();

        $stats = [
            'total'    => Peminjaman::where('user_id', $user->id)->count(),
            'pending'  => Peminjaman::where('user_id', $user->id)->where('status', Peminjaman::STATUS_PENDING)->count(),
            'approved' => Peminjaman::where('user_id', $user->id)->where('status', Peminjaman::STATUS_APPROVED)->count(),
            'done'     => Peminjaman::where('user_id', $user->id)->where('status', Peminjaman::STATUS_DONE)->count(),
        ];

        return view('user.laporan', compact('peminjamans', 'stats', 'user'));
    }

    /**
     * Export PDF riwayat pribadi user.
     */
    public function userExportPdf()
    {
        $user = Auth::user();

        $peminjamans = Peminjaman::with(['barang', 'ruangan'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('reports.user_peminjaman_pdf', compact('peminjamans', 'user'))
            ->setPaper('a4', 'portrait');

        $filename = 'Riwayat_Peminjaman_' . str_replace(' ', '_', $user->name) . '.pdf';
        return $pdf->download($filename);
    }
}
