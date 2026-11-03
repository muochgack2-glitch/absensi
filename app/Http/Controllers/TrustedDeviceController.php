<?php

namespace App\Http\Controllers;

use App\Models\TrustedDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrustedDeviceController extends Controller
{
    /**
     * Validasi apakah device token masih aktif.
     * Dipanggil saat welcome page load (tanpa auth session).
     *
     * POST /api/device/validate
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'device_token' => 'required|string|size:64',
        ]);

        $device = TrustedDevice::findByToken($request->device_token);

        if (!$device) {
            return response()->json([
                'valid' => false,
                'message' => 'Perangkat tidak terdaftar atau sudah dicabut.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'device_name' => $device->device_name,
            'authorized_by' => $device->user->name ?? '-',
            'registered_at' => $device->created_at->format('d M Y H:i'),
        ]);
    }

    /**
     * Register perangkat baru setelah login berhasil.
     * Butuh session auth (user harus sudah login via /api/auth/login).
     *
     * POST /api/device/register
     */
    public function register(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }

        // Hanya admin dan petugas yang boleh register device
        if (!in_array($user->role, ['admin', 'petugas'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin atau petugas yang dapat mendaftarkan perangkat.',
            ], 403);
        }

        $request->validate([
            'device_name' => 'nullable|string|max:100',
        ]);

        // Generate token unik
        $token = TrustedDevice::generateToken();

        // Buat record device
        $device = TrustedDevice::create([
            'device_token' => $token,
            'user_id'      => $user->id,
            'device_name'  => $request->device_name ?: 'Perangkat ' . now()->format('d/m/Y H:i'),
            'browser_info' => TrustedDevice::parseBrowserInfo($request->userAgent()),
            'ip_address'   => $request->ip(),
            'last_used_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil didaftarkan sebagai perangkat terpercaya.',
            'device_token' => $token,
            'device' => [
                'id'          => $device->id,
                'name'        => $device->device_name,
                'browser'     => $device->browser_info,
                'registered'  => $device->created_at->format('d M Y H:i'),
            ],
        ]);
    }

    /**
     * Daftar semua trusted devices (admin panel).
     *
     * GET /api/devices
     */
    public function index(): JsonResponse
    {
        $devices = TrustedDevice::with('user:id,name,role')
            ->orderByDesc('last_used_at')
            ->get()
            ->map(fn ($d) => [
                'id'            => $d->id,
                'device_name'   => $d->device_name,
                'browser_info'  => $d->browser_info,
                'ip_address'    => $d->ip_address,
                'is_active'     => $d->is_active,
                'authorized_by' => $d->user->name ?? '-',
                'role'          => $d->user->role ?? '-',
                'last_used_at'  => $d->last_used_at?->format('d M Y H:i') ?? '-',
                'created_at'    => $d->created_at->format('d M Y H:i'),
            ]);

        return response()->json([
            'success' => true,
            'devices' => $devices,
        ]);
    }

    /**
     * Revoke (nonaktifkan) device.
     *
     * POST /api/devices/{device}/revoke
     */
    public function revoke(TrustedDevice $device): JsonResponse
    {
        $device->revoke();

        return response()->json([
            'success' => true,
            'message' => "Perangkat \"{$device->device_name}\" berhasil dicabut aksesnya.",
        ]);
    }

    /**
     * Aktifkan kembali device yang pernah di-revoke.
     *
     * POST /api/devices/{device}/activate
     */
    public function activate(TrustedDevice $device): JsonResponse
    {
        $device->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => "Perangkat \"{$device->device_name}\" berhasil diaktifkan kembali.",
        ]);
    }

    /**
     * Hapus device permanen.
     *
     * DELETE /api/devices/{device}
     */
    public function destroy(TrustedDevice $device): JsonResponse
    {
        $name = $device->device_name;
        $device->delete();

        return response()->json([
            'success' => true,
            'message' => "Perangkat \"{$name}\" berhasil dihapus.",
        ]);
    }
}

