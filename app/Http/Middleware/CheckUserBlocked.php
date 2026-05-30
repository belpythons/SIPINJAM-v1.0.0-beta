<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserBlocked
{
    /**
     * Handle an incoming request.
     * Jika user sedang diblokir, logout dan redirect ke login dengan pesan error.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isBlocked()) {
            $blockedUntil = $user->blocked_until->format('d M Y H:i') . ' WITA';
            $reason = $user->blocked_reason;

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = "Akun Anda telah diblokir hingga {$blockedUntil} karena pelanggaran tata tertib peminjaman.";

            if ($reason) {
                $message .= " Catatan pelanggaran: \"{$reason}\".";
            }

            return redirect()->route('login')->with('error', $message);
        }

        return $next($request);
    }
}
