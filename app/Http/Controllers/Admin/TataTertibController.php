<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TataTertibVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TataTertibController extends Controller
{
    public function index(): Response
    {
        $versions = TataTertibVersion::orderByDesc('berlaku_sejak')->get();

        return Inertia::render('Admin/KelolaTataTertib', [
            'versions' => $versions,
            'aktifId' => TataTertibVersion::aktif()?->id,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'versi' => ['required', 'string', 'max:50', 'unique:tata_tertib_versions,versi'],
            'konten' => ['required', 'string'],
            'berlaku_sejak' => ['nullable', 'date'],
        ]);

        // Selalu buat versi baru (append) — jangan edit in-place, jejak revisi terjaga.
        TataTertibVersion::create([
            'versi' => $data['versi'],
            'konten' => $data['konten'],
            'berlaku_sejak' => $data['berlaku_sejak'] ?? now(),
            'dibuat_oleh' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Versi tata tertib baru berhasil dibuat.');
    }
}
