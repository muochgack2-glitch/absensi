<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WaliKelasMiddleware
{
    /**
     * Batasi akses berdasarkan role.
     * Admin: akses penuh.
     * Wali Kelas: hanya akses data kelas sendiri.
     */
    public function handle(Request $request, Closure $next, string $requiredRole = 'admin'): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin bisa akses semua
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Wali kelas: hanya bisa akses route wali_kelas
        if ($user->isWaliKelas() && $requiredRole === 'wali_kelas') {
            return $next($request);
        }

        // Wali kelas mencoba akses halaman admin
        if ($user->isWaliKelas()) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Akses ditolak. Anda hanya dapat mengakses data kelas Anda.');
        }

        abort(403, 'Akses ditolak.');
    }
}
