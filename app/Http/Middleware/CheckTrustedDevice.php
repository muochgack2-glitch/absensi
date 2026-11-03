<?php

namespace App\Http\Middleware;

use App\Models\TrustedDevice;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTrustedDevice
{
    /**
     * Cek apakah request berasal dari perangkat terpercaya.
     *
     * Device token dikirim via header X-Device-Token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Device-Token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat belum terdaftar. Silakan login terlebih dahulu di halaman scanner.',
                'device_required' => true,
            ], 403);
        }

        $device = TrustedDevice::findByToken($token);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak dikenali atau sudah dicabut aksesnya. Silakan login ulang.',
                'device_required' => true,
            ], 403);
        }

        // Update last_used_at (throttle: hanya update per 5 menit agar tidak berat)
        if (!$device->last_used_at || $device->last_used_at->diffInMinutes(now()) >= 5) {
            $device->touchLastUsed();
        }

        // Simpan device di request agar controller bisa akses
        $request->attributes->set('trusted_device', $device);

        return $next($request);
    }
}

