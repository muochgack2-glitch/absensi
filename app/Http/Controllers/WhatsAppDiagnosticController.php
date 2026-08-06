<?php

namespace App\Http\Controllers;

use App\Services\AttendanceWhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WhatsAppDiagnosticController extends Controller
{
    protected AttendanceWhatsAppService $whatsappService;

    public function __construct(AttendanceWhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Test send message with full diagnostic
     */
    public function testSend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $phone = $request->phone;
        $message = $request->message ?? 'Test message dari WhatsApp Diagnostic Tool - ' . now()->format('Y-m-d H:i:s');

        // Get server status first
        $statusCheck = $this->whatsappService->getStatus();

        // Attempt to send
        $sendResult = $this->whatsappService->send($phone, $message, [
            'type' => 'diagnostic_test',
            'sent_by' => auth()->id(),
        ]);

        // Get current server URL
        $currentServer = $this->whatsappService->getCurrentServerUrl();

        return response()->json([
            'success' => true,
            'diagnostic' => [
                'test_phone' => $phone,
                'test_message' => $message,
                'timestamp' => now()->toIso8601String(),
                'user' => auth()->user()->name,
                
                'gateway_status' => [
                    'server_url' => $currentServer,
                    'status_check' => $statusCheck,
                ],
                
                'send_result' => $sendResult,
                
                'analysis' => [
                    'gateway_connected' => $statusCheck['success'] ?? false,
                    'send_api_success' => $sendResult['success'] ?? false,
                    'has_message_id' => $sendResult['has_message_id'] ?? false,
                    'has_error' => isset($sendResult['error']) || isset($sendResult['data']['error']),
                    'log_id' => $sendResult['log_id'] ?? null,
                ],
                
                'recommendations' => $this->getRecommendations($statusCheck, $sendResult),
            ]
        ]);
    }

    /**
     * Get recommendations based on test results
     */
    private function getRecommendations(array $statusCheck, array $sendResult): array
    {
        $recommendations = [];

        // Check 1: Gateway connection
        if (!($statusCheck['success'] ?? false)) {
            $recommendations[] = [
                'issue' => 'Gateway tidak terhubung',
                'severity' => 'critical',
                'action' => 'Pastikan WhatsApp Gateway (PM2) berjalan dan scan QR code',
            ];
        }

        // Check 2: Send result
        if (!($sendResult['success'] ?? false)) {
            $recommendations[] = [
                'issue' => 'Pesan gagal terkirim',
                'severity' => 'high',
                'action' => 'Cek error message: ' . ($sendResult['message'] ?? 'Unknown error'),
            ];
        }

        // Check 3: No messageId (false positive)
        if (($sendResult['success'] ?? false) && !($sendResult['has_message_id'] ?? false)) {
            $recommendations[] = [
                'issue' => 'Tidak ada messageId dari gateway',
                'severity' => 'medium',
                'action' => 'Gateway return success tapi tidak ada proof (messageId). Pesan mungkin tidak benar-benar terkirim.',
            ];
        }

        if (empty($recommendations)) {
            $recommendations[] = [
                'issue' => 'Tidak ada masalah terdeteksi',
                'severity' => 'info',
                'action' => 'Cek HP penerima untuk konfirmasi pesan diterima',
            ];
        }

        return $recommendations;
    }
}
