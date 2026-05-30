<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// SLA Auto-Reject: tolak otomatis peminjaman pending > 48 jam
Schedule::command('booking:auto-reject')->hourly();

// Sanction: blokir user overtime & unblock user yang masa blokirnya habis
Schedule::command('sanction:apply')->dailyAt('00:00');
