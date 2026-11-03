<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\TrustedDevice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $request->authenticate();
            $request->session()->regenerate();

            $user     = $request->user();
            $homeRoute = $user->isWaliKelas()
                ? route('wali.dashboard', absolute: false)
                : route('dashboard', absolute: false);

            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson()) {
                $response = [
                    'success'  => true,
                    'message'  => 'Login berhasil',
                    'redirect' => $homeRoute,
                ];

                // Jika login dari welcome page (scanner), otomatis register device
                if ($request->input('register_device') && in_array($user->role, ['admin', 'petugas'])) {
                    $token = TrustedDevice::generateToken();
                    $device = TrustedDevice::create([
                        'device_token' => $token,
                        'user_id'      => $user->id,
                        'device_name'  => $request->input('device_name') ?: 'Perangkat ' . now()->format('d/m/Y H:i'),
                        'browser_info' => TrustedDevice::parseBrowserInfo($request->userAgent()),
                        'ip_address'   => $request->ip(),
                        'last_used_at' => now(),
                    ]);

                    $response['device_token'] = $token;
                    $response['device_registered'] = true;
                    $response['redirect'] = null; // Jangan redirect, tetap di welcome page
                }

                return response()->json($response);
            }

            return redirect()->intended($homeRoute);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation error for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            }
            
            // Re-throw for normal requests
            throw $e;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
