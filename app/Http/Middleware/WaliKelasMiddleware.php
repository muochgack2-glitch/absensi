<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WaliKelasMiddleware
{
    /**
     * Role hierarchy:
     * - admin          : akses penuh
     * - waka_kesiswaan : operasional + data siswa/kelas (view/edit)
     * - petugas        : operasional (dashboard, manual, izin, laporan, kamera)
     * - kepala_sekolah : viewer (dashboard + laporan saja)
     * - wali_kelas     : hanya data kelas sendiri
     */
    public function handle(Request $request, Closure $next, string $requiredRole = 'admin'): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin: akses semua
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Waka Kesiswaan: akses petugas + data siswa/kelas
        if ($user->isWakaKesiswaan() && in_array($requiredRole, ['petugas', 'waka_kesiswaan', 'admin'])) {
            return $next($request);
        }

        // Petugas: akses route bertanda 'petugas'
        if ($user->isPetugas() && in_array($requiredRole, ['petugas', 'admin'])) {
            return $next($request);
        }

        // Kepala Sekolah: hanya akses route bertanda 'kepala_sekolah'
        if ($user->isKepalaSekolah() && $requiredRole === 'kepala_sekolah') {
            return $next($request);
        }

        // Wali Kelas: hanya akses route wali_kelas
        if ($user->isWaliKelas() && $requiredRole === 'wali_kelas') {
            return $next($request);
        }

        // Redirect berdasarkan role
        $redirectMap = [
            'kepala_sekolah' => ['route' => 'attendance.dashboard', 'msg' => 'Akses ditolak.'],
            'waka_kesiswaan' => ['route' => 'attendance.dashboard', 'msg' => 'Akses ditolak.'],
            'petugas'        => ['route' => 'attendance.dashboard', 'msg' => 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.'],
            'wali_kelas'     => ['route' => 'wali.dashboard',       'msg' => 'Akses ditolak. Anda hanya dapat mengakses data kelas Anda.'],
        ];

        if (isset($redirectMap[$user->role])) {
            return redirect()->route($redirectMap[$user->role]['route'])
                ->with('error', $redirectMap[$user->role]['msg']);
        }

        abort(403, 'Akses ditolak.');
    }
}
