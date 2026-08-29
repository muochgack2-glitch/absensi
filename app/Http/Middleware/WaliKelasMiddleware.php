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
     * Petugas: akses terbatas (dashboard, manual, izin, laporan, kamera settings).
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

        // Petugas: bisa akses route bertanda 'petugas' atau 'admin'
        if ($user->isPetugas() && in_array($requiredRole, ['petugas', 'admin'])) {
            return $next($request);
        }

        // Wali kelas: hanya bisa akses route wali_kelas
        if ($user->isWaliKelas() && $requiredRole === 'wali_kelas') {
            return $next($request);
        }

        // Redirect berdasarkan role
        if ($user->isPetugas()) {
            return redirect()->route('attendance.dashboard')
                ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
        }

        if ($user->isWaliKelas()) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Akses ditolak. Anda hanya dapat mengakses data kelas Anda.');
        }

        abort(403, 'Akses ditolak.');
    }
}
