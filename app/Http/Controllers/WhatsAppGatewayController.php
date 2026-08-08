<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsAppSetting;

class WhatsAppGatewayController extends Controller
{
    /**
     * Get gateway configurations (dynamic from database)
     */
    protected function getGateways()
    {
        return [
            'primary' => [
                'name' => 'Gateway Utama (Absensi)',
                'url' => WhatsAppSetting::get('wa_server_url', 'http://localhost:3001'),
                'purpose' => 'Primary - Absensi System (Port 3001)',
            ],
            'backup' => [
                'name' => 'Gateway Backup (SPMB)',
                'url' => WhatsAppSetting::get('wa_server_url_backup', 'http://localhost:3000'),
                'purpose' => 'Backup / Failover - SPMB System (Port 3000)',
            ],
        ];
    }

    /**
     * Show gateway management dashboard
     */
    public function index()
    {
        $gateways = $this->getGateways();
        $statuses = [];
        
        foreach ($gateways as $key => $gateway) {
            if (empty($gateway['url'])) {
                $statuses[$key] = [
                    'info' => $gateway,
                    'status' => null,
                    'health' => null,
                    'online' => false,
                    'error' => 'URL tidak dikonfigurasi',
                ];
                continue;
            }

            try {
                $response = Http::timeout(5)->get("{$gateway['url']}/status");
                $health = Http::timeout(5)->get("{$gateway['url']}/health");
                
                $statuses[$key] = [
                    'info' => $gateway,
                    'status' => $response->successful() ? $response->json() : null,
                    'health' => $health->successful() ? $health->json() : null,
                    'online' => $response->successful(),
                ];
            } catch (\Exception $e) {
                $statuses[$key] = [
                    'info' => $gateway,
                    'status' => null,
                    'health' => null,
                    'online' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        $failoverSettings = [
            'enabled' => WhatsAppSetting::get('wa_failover_enabled', false),
            'timeout' => WhatsAppSetting::get('wa_failover_timeout', 5),
        ];
        
        return view('whatsapp.gateway', compact('statuses', 'failoverSettings'));
    }

    /**
     * Get QR code from gateway
     */
    public function getQRCode($gateway)
    {
        $gateways = $this->getGateways();
        $url = $gateways[$gateway]['url'] ?? null;
        if (!$url) {
            return response()->json(['success' => false, 'message' => 'Gateway not found'], 404);
        }
        
        try {
            $response = Http::timeout(10)->get("{$url}/qr");
            
            if ($response->successful() && $response->json('success')) {
                return response()->json([
                    'success' => true,
                    'qr' => $response->json('qr'),
                    'message' => $response->json('message'),
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => $response->json('message', 'QR not available'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch QR: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restart gateway
     */
    public function restart($gateway)
    {
        $gateways = $this->getGateways();
        $url = $gateways[$gateway]['url'] ?? null;
        if (!$url) {
            return response()->json(['success' => false, 'message' => 'Gateway not found'], 404);
        }
        
        try {
            Http::timeout(10)->post("{$url}/restart");
            
            Log::info('Gateway restart requested', [
                'gateway' => $gateway,
                'url' => $url,
                'user' => auth()->user()->name ?? 'Unknown',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Gateway sedang restart... Tunggu 5-10 detik.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal restart: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout from gateway
     */
    public function logout($gateway)
    {
        $gateways = $this->getGateways();
        $url = $gateways[$gateway]['url'] ?? null;
        if (!$url) {
            return response()->json(['success' => false, 'message' => 'Gateway not found'], 404);
        }
        
        try {
            Http::timeout(10)->post("{$url}/logout");
            
            Log::info('Gateway logout requested', [
                'gateway' => $gateway,
                'user' => auth()->user()->name ?? 'Unknown',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil. QR baru akan di-generate...',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal logout: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get gateway logs (PM2)
     */
    public function getLogs($gateway)
    {
        $processName = $gateway === 'primary' ? 'whatsapp-gateway-absensi' : 'whatsapp-gateway-backup';
        
        try {
            $logs = shell_exec("pm2 logs {$processName} --lines 50 --nostream 2>&1");
            
            if (empty($logs)) {
                $logs = "Tidak bisa mengambil log. PM2 mungkin tidak tersedia.";
            }
            
            return response()->json(['success' => true, 'logs' => $logs]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'logs' => "Error: " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get real-time gateway statuses (AJAX endpoint)
     */
    public function getStatuses()
    {
        $gateways = $this->getGateways();
        $statuses = [];
        
        foreach ($gateways as $key => $gateway) {
            if (empty($gateway['url'])) {
                $statuses[$key] = [
                    'info' => $gateway,
                    'online' => false,
                    'error' => 'URL tidak dikonfigurasi',
                    'last_check' => now()->format('Y-m-d H:i:s'),
                ];
                continue;
            }

            try {
                $response = Http::timeout(5)->get("{$gateway['url']}/status");
                $health = Http::timeout(5)->get("{$gateway['url']}/health");
                
                $statuses[$key] = [
                    'info' => $gateway,
                    'status' => $response->successful() ? $response->json() : null,
                    'health' => $health->successful() ? $health->json() : null,
                    'online' => $response->successful(),
                    'last_check' => now()->format('Y-m-d H:i:s'),
                ];
            } catch (\Exception $e) {
                $statuses[$key] = [
                    'info' => $gateway,
                    'online' => false,
                    'error' => $e->getMessage(),
                    'last_check' => now()->format('Y-m-d H:i:s'),
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'statuses' => $statuses,
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Toggle failover setting
     */
    public function toggleFailover(Request $request)
    {
        try {
            $enabled = $request->input('enabled', false);
            
            WhatsAppSetting::set('wa_failover_enabled', $enabled);
            WhatsAppSetting::clearCache();
            
            Log::info('Failover setting changed', [
                'enabled' => $enabled,
                'user' => auth()->user()->name ?? 'Unknown',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $enabled ? 'Auto failover diaktifkan' : 'Auto failover dinonaktifkan',
                'enabled' => $enabled,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah failover: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset & Reconnect gateway
     */
    public function resetGateway($gateway)
    {
        $gateways = $this->getGateways();
        $gatewayInfo = $gateways[$gateway] ?? null;
        
        if (!$gatewayInfo || empty($gatewayInfo['url'])) {
            return response()->json(['success' => false, 'message' => 'Gateway not found'], 404);
        }
        
        try {
            Log::info('Gateway hard reset requested', [
                'gateway' => $gateway,
                'user' => auth()->user()->name ?? 'Unknown',
            ]);
            
            // Step 1: Logout via API
            try {
                Http::timeout(5)->post("{$gatewayInfo['url']}/logout");
            } catch (\Exception $e) {
                Log::warning('Logout API call failed (continuing with reset)', ['error' => $e->getMessage()]);
            }
            
            // Step 2: Wait
            sleep(2);
            
            return response()->json([
                'success' => true,
                'message' => 'Gateway di-reset. Tunggu 10-15 detik lalu klik "Lihat QR" untuk QR baru.',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal reset gateway: ' . $e->getMessage(),
            ], 500);
        }
    }
}
