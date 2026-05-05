<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TataTertibController extends Controller
{
    public function index()
    {
        return view('user.tata_tertib.index');
    }
}
