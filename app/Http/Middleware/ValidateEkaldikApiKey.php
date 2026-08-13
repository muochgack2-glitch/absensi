<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateEkaldikApiKey
{
    /**
     * Validate API key for E-Kaldik integration endpoints.
     *
     * Expects header: X-API-Key: {EKALDIK_API_KEY}
     * If EKALDIK_API_KEY is not set in .env, endpoint is open (dev mode).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = config('services.ekaldik.api_key');

        // If no API key configured, allow access (dev mode)
        if (empty($configuredKey)) {
            return $next($request);
        }

        $providedKey = $request->header('X-API-Key');

        if (!$providedKey || $providedKey !== $configuredKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid API key',
            ], 401);
        }

        return $next($request);
    }
}
