<?php

namespace App\Jobs;

use App\Models\WhatsAppLog;
use App\Services\AttendanceWhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maksimal percobaan pengiriman.
     * Layer 5: retry 3x dengan backoff eksponensial.
     */
    public int $tries = 3;

    /**
     * Timeout eksekusi job (detik).
     */
    public int $timeout = 60;

    /**
     * Backoff antar retry: 10s -> 30s -> 60s.
     * Layer 5: eksponensial backoff.
     */
    public array $backoff = [10, 30, 60];

    /**
     * Buat instance Job.
     *
     * @param string      $phone      Nomor HP tujuan
     * @param string      $message    Isi pesan WA
     * @param string|null $photoPath  Path foto di storage (nullable)
     * @param string      $type       Tipe notifikasi (check_in, check_out, bk_notify, late_warning, absent, dll)
     * @param int|null    $studentId  ID siswa terkait (untuk log)
     */
    public function __construct(
        public readonly string  $phone,
        public readonly string  $message,
        public readonly ?string $photoPath = null,
        public readonly string  $type      = 'system',
        public readonly ?int    $studentId = null,
    ) {}

    /**
     * Eksekusi job.
     */
    public function handle(AttendanceWhatsAppService $waService): void
    {
        // Layer 4: Smart Timing
        // Jam aman: 05:00 - 22:00 WIB
        // Jika di luar jam aman, tunda hingga 05:00 pagi berikutnya
        $jam = now()->timezone('Asia/Jakarta')->hour;
        if ($jam >= 22 || $jam < 5) {
            $besokPagi = now()->timezone('Asia/Jakarta')
                ->addDay()
                ->startOfDay()
                ->addHours(5);
            $detikTunda = (int) $besokPagi->diffInSeconds(now());

            Log::debug('[WA Queue] Tunda - luar jam aman', [
                'jam'         => $jam,
                'phone'       => $this->phone,
                'type'        => $this->type,
                'tunda_detik' => $detikTunda,
            ]);

            $this->release($detikTunda);
            return;
        }

        // Layer 5: Circuit Breaker
        // Jika >= 5 pengiriman gagal dalam 5 menit terakhir -> anggap gateway down
        // Tunda seluruh antrian 30 menit agar gateway sempat recover
        $recentFailed = WhatsAppLog::where('status', 'failed')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        if ($recentFailed >= 5) {
            Log::warning('[WA Queue] Circuit breaker aktif - gateway down, pause 30 menit', [
                'recent_failed' => $recentFailed,
                'phone'         => $this->phone,
                'type'          => $this->type,
            ]);
            $this->release(1800); // 30 menit
            return;
        }

        // Kirim Notifikasi
        Log::debug('[WA Queue] Mengirim notifikasi', [
            'phone'      => $this->phone,
            'type'       => $this->type,
            'student_id' => $this->studentId,
            'has_photo'  => ! is_null($this->photoPath),
        ]);

        $result = $waService->sendParentNotification(
            $this->phone,
            $this->message,
            $this->photoPath,
            $this->type,
        );

        // Jika gagal -> lempar exception agar Job trigger retry otomatis
        if (! ($result['success'] ?? false)) {
            $errorMsg = $result['message'] ?? $result['error'] ?? 'Unknown error';
            Log::warning('[WA Queue] Pengiriman gagal, akan retry', [
                'phone'   => $this->phone,
                'type'    => $this->type,
                'attempt' => $this->attempts(),
                'error'   => $errorMsg,
            ]);
            throw new \RuntimeException("[WA Job] Send failed: {$errorMsg}");
        }

        Log::info('[WA Queue] Notifikasi terkirim', [
            'phone'  => $this->phone,
            'type'   => $this->type,
            'log_id' => $result['log_id'] ?? null,
        ]);
    }

    /**
     * Tangani job yang sudah habis percobaan (failed).
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[WA Queue] Job gagal permanen setelah ' . $this->tries . ' percobaan', [
            'phone'      => $this->phone,
            'type'       => $this->type,
            'student_id' => $this->studentId,
            'error'      => $exception->getMessage(),
        ]);
    }
}
