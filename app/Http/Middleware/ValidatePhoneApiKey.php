<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePhoneApiKey
{
    /**
     * Validate API key for phone number update endpoint.
     *
     * Expects header: X-API-Key: {PHONE_API_KEY}
     * If PHONE_API_KEY is not set in .env, endpoint is open (dev mode).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = config('services.phone_api.api_key');

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

        // Add CORS headers for cross-domain access
        $response = $next($request);
        $response->headers->set('Access-Control-Allow-Origin', config('services.phone_api.allowed_origin', '*'));
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-API-Key');

        return $response;
    }
}
