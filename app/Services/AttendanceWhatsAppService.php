<?php

namespace App\Services;

use App\Models\WhatsAppLog;
use App\Models\WhatsAppTemplate;
use App\Models\WhatsAppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AttendanceWhatsAppService
{
    /**
     * WhatsApp server URL
     */
    protected string $serverUrl;

    /**
     * Connection timeout
     */
    protected int $timeout;

    /**
     * Retry attempts
     */
    protected int $retryAttempts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->serverUrl = $this->getActiveServerUrl();
        $this->timeout = WhatsAppSetting::getTimeout();
        $this->retryAttempts = WhatsAppSetting::getRetryAttempts();
    }

    /**
     * Get active server URL with failover support
     * 
     * @param string|null $context Context type
     * @return string
     */
    protected function getActiveServerUrl(?string $context = null): string
    {
        $primary = WhatsAppSetting::get('wa_server_url', 'http://localhost:3001');
        $backup = WhatsAppSetting::get('wa_server_url_backup');
        $failoverEnabled = WhatsAppSetting::get('wa_failover_enabled', false);

        // If failover not enabled or no backup configured, always use primary
        if (!$failoverEnabled || !$backup) {
            return $primary;
        }

        // Check primary health
        if ($this->checkServerHealth($primary)) {
            return $primary;
        }

        // Primary unhealthy, failover to backup
        Log::warning('Primary WhatsApp gateway unhealthy, failover to backup', [
            'primary' => $primary,
            'backup' => $backup,
            'context' => $context ?? 'default',
        ]);

        return $backup;
    }

    /**
     * Get current active server URL (public method for UI display)
     * 
     * @return string
     */
    public function getCurrentServerUrl(): string
    {
        return $this->getActiveServerUrl();
    }

    /**
     * Check if server is healthy
     * 
     * @param string $url
     * @return bool
     */
    protected function checkServerHealth(string $url): bool
    {
        try {
            $timeout = WhatsAppSetting::get('wa_failover_timeout', 5);
            
            $response = Http::timeout($timeout)->get("{$url}/status");
            
            if (!$response->successful()) {
                return false;
            }

            $data = $response->json();
            return isset($data['status']) && $data['status'] === 'connected';
            
        } catch (Exception $e) {
            Log::debug('Server health check failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get WhatsApp server status
     * 
     * @return array
     */
    public function getStatus(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->serverUrl}/status");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get status',
                'error' => $response->body(),
            ];
        } catch (Exception $e) {
            Log::error('WhatsApp status check failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get server health metrics
     * 
     * @return array
     */
    public function getHealth(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->serverUrl}/health");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get health metrics',
                'error' => $response->body(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get QR code for WhatsApp authentication
     * 
     * @return array
     */
    public function getQRCode(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->serverUrl}/qr");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get QR code',
                'error' => $response->body(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send single WhatsApp message
     * 
     * @param string $phone Phone number
     * @param string $message Message content
     * @param array $options Additional options (student_id, template_id, sent_by, type)
     * @return array
     */
    public function send(string $phone, string $message, array $options = []): array
    {
        // Get appropriate gateway URL based on context
        $serverUrl = $this->getActiveServerUrl($options['type'] ?? null);
        
        // Create log entry
        $log = WhatsAppLog::create([
            'phone' => $phone,
            'message' => $message,
            'status' => 'pending',
            'type' => $options['type'] ?? 'manual',
            'student_id' => $options['student_id'] ?? null,
            'template_id' => $options['template_id'] ?? null,
            'sent_by' => $options['sent_by'] ?? auth()->id(),
        ]);

        try {
            Log::info('Attempting to send WhatsApp message', [
                'phone' => $phone,
                'message_length' => strlen($message),
                'log_id' => $log->id,
                'server_url' => $serverUrl,
            ]);

            $response = Http::timeout($this->timeout)
                ->retry($this->retryAttempts, 1000)
                ->post("{$serverUrl}/send", [
                    'phone' => $phone,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Check if server actually sent the message
                if (isset($responseData['success']) && $responseData['success'] === false) {
                    $errorMessage = $responseData['message'] ?? $responseData['error'] ?? 'Message failed on WhatsApp server';
                    $log->markAsFailed($errorMessage, $responseData);

                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'log_id' => $log->id,
                        'debug' => $responseData,
                    ];
                }
                
                // Check if messageId exists (proof of sending)
                $hasMessageId = isset($responseData['messageId']) || isset($responseData['message_id']) || isset($responseData['data']['messageId']);
                
                if (!$hasMessageId && isset($responseData['success']) && $responseData['success'] === true) {
                    Log::warning('WhatsApp gateway returned success without messageId', [
                        'phone' => $phone,
                        'log_id' => $log->id,
                    ]);
                }
                
                // Mark as sent
                $log->markAsSent($responseData);

                return [
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'data' => $responseData,
                    'log_id' => $log->id,
                    'has_message_id' => $hasMessageId,
                ];
            }

            // Mark as failed
            $errorMessage = $response->json()['message'] ?? 'Failed to send message';
            $log->markAsFailed($errorMessage, $response->json());

            return [
                'success' => false,
                'message' => $errorMessage,
                'log_id' => $log->id,
            ];
        } catch (Exception $e) {
            $log->markAsFailed($e->getMessage());

            Log::error('WhatsApp message send exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
                'log_id' => $log->id,
            ]);

            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'log_id' => $log->id,
            ];
        }
    }

    /**
     * Send parent notification — the bridge method used by AttendanceNotificationService
     * 
     * @param string $phone Parent phone number
     * @param string $message Notification message
     * @param string|null $photoPath Optional photo path (not used in current WA gateway)
     * @return array
     */
    public function sendParentNotification(string $phone, string $message, ?string $photoPath = null): array
    {
        return $this->send($phone, $message, [
            'type' => 'check_in',
            'sent_by' => null, // System-generated
        ]);
    }

    /**
     * Send message using template
     * 
     * @param string $phone Phone number
     * @param string $templateName Template name
     * @param array $data Data for template variables
     * @param array $options Additional options
     * @return array
     */
    public function sendWithTemplate(string $phone, string $templateName, array $data, array $options = []): array
    {
        try {
            // Get template
            $template = WhatsAppTemplate::where('name', $templateName)
                ->where('is_active', true)
                ->firstOrFail();

            // Parse template with data
            $message = $template->parse($data);

            // Increment template usage
            $template->incrementUsage();

            // Send message
            $options['template_id'] = $template->id;
            return $this->send($phone, $message, $options);

        } catch (Exception $e) {
            Log::error('WhatsApp template send failed', [
                'phone' => $phone,
                'template' => $templateName,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Template not found or inactive',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send bulk messages
     * 
     * @param array $messages Array of ['phone' => '...', 'message' => '...']
     * @param array $options Additional options
     * @return array
     */
    public function sendBulk(array $messages, array $options = []): array
    {
        $results = [];
        $successCount = 0;
        $failedCount = 0;

        $delay = WhatsAppSetting::get('wa_broadcast_delay', 2);

        foreach ($messages as $item) {
            $phone = $item['phone'] ?? null;
            $message = $item['message'] ?? null;
            $studentId = $item['student_id'] ?? null;

            if (!$phone || !$message) {
                $results[] = [
                    'phone' => $phone,
                    'success' => false,
                    'error' => 'Phone and message are required',
                ];
                $failedCount++;
                continue;
            }

            // Merge student_id into options
            $messageOptions = array_merge($options, [
                'student_id' => $studentId,
            ]);

            $result = $this->send($phone, $message, $messageOptions);
            
            $results[] = [
                'phone' => $phone,
                'success' => $result['success'],
                'log_id' => $result['log_id'] ?? null,
                'error' => $result['error'] ?? null,
            ];

            if ($result['success']) {
                $successCount++;
            } else {
                $failedCount++;
            }

            // Delay between messages to avoid rate limiting
            if (count($messages) > 1) {
                sleep($delay);
            }
        }

        return [
            'success' => true,
            'message' => "Sent {$successCount} messages, {$failedCount} failed",
            'total' => count($messages),
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'results' => $results,
        ];
    }

    /**
     * Check if WhatsApp server is connected
     * 
     * @return bool
     */
    public function isConnected(): bool
    {
        $status = $this->getStatus();
        
        if (!$status['success']) {
            return false;
        }

        return ($status['data']['status'] ?? '') === 'connected';
    }

    /**
     * Check if auto send is enabled
     * 
     * @return bool
     */
    public function isAutoSendEnabled(): bool
    {
        return WhatsAppSetting::isAutoSendEnabled();
    }

    /**
     * Logout from WhatsApp
     * 
     * @return array
     */
    public function logout(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->serverUrl}/logout");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Logged out successfully',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to logout',
                'error' => $response->body(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Restart WhatsApp server
     * 
     * @return array
     */
    public function restart(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->serverUrl}/restart");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Server is restarting...',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to restart server',
                'error' => $response->body(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get statistics
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total_sent' => WhatsAppLog::sent()->count(),
            'total_failed' => WhatsAppLog::failed()->count(),
            'total_pending' => WhatsAppLog::pending()->count(),
            'sent_today' => WhatsAppLog::sent()->today()->count(),
            'failed_today' => WhatsAppLog::failed()->today()->count(),
            'total_templates' => WhatsAppTemplate::count(),
            'active_templates' => WhatsAppTemplate::active()->count(),
        ];
    }
}
