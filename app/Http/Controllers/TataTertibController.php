<?php

namespace App\Http\Controllers;

use App\Models\TataTertibVersion;
use Inertia\Inertia;

class TataTertibController extends Controller
{
    public function index()
    {
        return Inertia::render('User/TataTertib', [
            'versi' => TataTertibVersion::aktif(),
        ]);
    }
}
