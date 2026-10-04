<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TataTertibController extends Controller
{
    public function index()
    {
        return Inertia::render('User/TataTertib');
    }
}
