<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DriverSessionGuard
{
    /**
     * Handle an incoming request.
     * Ensure only active drivers with registered driver profile can access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('driver.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // Cek jika role bukan driver
        if ($user->role !== 'driver') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Khusus Portal Sopir.'], 403);
            }
            // Arahkan admin/kasir/gm ke dashboard Filament
            return redirect('/admin')->with('warning', 'Halaman tersebut khusus untuk portal sopir.');
        }

        // Cek status keaktifan user
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('driver.login')->withErrors([
                'login' => 'Akun sopir Anda dinonaktifkan.',
            ]);
        }

        // Pastikan relasi driver tersedia
        if (!$user->driver) {
            abort(403, 'Profil data armada sopir tidak ditemukan untuk akun ini.');
        }

        // Cek status keaktifan driver
        if (!$user->driver->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('driver.login')->withErrors([
                'login' => 'Data armada sopir tidak aktif. Silakan hubungi admin.',
            ]);
        }

        return $next($request);
    }
}
