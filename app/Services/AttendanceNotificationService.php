<?php

namespace App\Services;

use App\Models\AttendanceStudent;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSetting;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\Log;

class AttendanceNotificationService
{
    public function __construct(
        private AttendanceWhatsAppService $whatsAppService
    ) {}

    /**
     * Send check-in notification to parent.
     *
     * @param AttendanceStudent $student
     * @param AttendanceRecord $record
     * @return void
     */
    public function notifyCheckIn(AttendanceStudent $student, AttendanceRecord $record): void
    {
        // Check if parent notification is enabled
        $enabled = AttendanceSetting::get('enable_parent_notification', 'true');
        if ($enabled !== 'true' && $enabled !== '1' && $enabled !== 1) {
            Log::debug("Parent notification disabled", ['enabled' => $enabled]);
            return;
        }

        // Check if student has parent phone number
        $phones = $student->getParentPhones();
        if (empty($phones)) {
            Log::warning("No parent phone number for student {$student->nis}");
            return;
        }

        // Get school name
        $schoolName = AttendanceSetting::get('school_name', 'Sekolah');

        // Format message
        $message = $this->formatCheckInMessage($student, $record, $schoolName);

        // Check if photo should be included
        $includePhoto = AttendanceSetting::get('include_photo_in_notification', 'true');
        $shouldIncludePhoto = in_array($includePhoto, ['true', '1', 1, true], true);
        $photoPath = $shouldIncludePhoto ? $record->check_in_photo : null;

        // Send notification ke semua nomor
        $result = ['success' => false];
        foreach ($phones as $phone) {
            $result = $this->whatsAppService->sendParentNotification($phone, $message, $photoPath);
        }

        // Log notification attempt
        $this->logNotification($student->id, 'check_in', $result);

        // Check if late warning should be sent
        if ($record->status === 'terlambat') {
            $this->checkAndSendLateWarning($student, $record);
        }
    }

    /**
     * Check and send late warning notification if conditions are met.
     *
     * @param AttendanceStudent $student
     * @param AttendanceRecord $record
     * @return void
     */
    private function checkAndSendLateWarning(AttendanceStudent $student, AttendanceRecord $record): void
    {
        // Check if late warning is enabled
        $enabled = AttendanceSetting::get('late_warning_enabled', 'false');
        if ($enabled !== 'true' && $enabled !== '1' && $enabled !== 1) {
            Log::debug("Late warning disabled");
            return;
        }

        // Check if student has parent phone number
        if (empty($student->no_hp_ortu)) {
            return;
        }

        // Get threshold settings
        $thresholdMinutes = (int) AttendanceSetting::get('late_warning_threshold_minutes', 30);
        $minCount = (int) AttendanceSetting::get('late_warning_min_count', 3);

        // Calculate how late the student is
        $checkInTime = \Carbon\Carbon::parse($record->check_in_time);
        $targetTime = \Carbon\Carbon::parse(AttendanceSetting::get('check_in_time', '07:00:00'));
        $minutesLate = $checkInTime->diffInMinutes($targetTime, false);

        // If not late enough, skip
        if ($minutesLate < $thresholdMinutes) {
            Log::debug("Student not late enough for warning", [
                'minutes_late' => $minutesLate,
                'threshold' => $thresholdMinutes
            ]);
            return;
        }

        // Count late records in current month
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        $endOfMonth = \Carbon\Carbon::now()->endOfMonth();

        $lateRecords = AttendanceRecord::where('student_id', $student->id)
            ->where('status', 'terlambat')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $lateCount = $lateRecords->count();

        // If not enough late occurrences, skip
        if ($lateCount < $minCount) {
            Log::debug("Student not late enough times for warning", [
                'late_count' => $lateCount,
                'min_count' => $minCount
            ]);
            return;
        }

        // Calculate statistics
        $totalMinutesLate = 0;
        $lateMinutesByDate = [];

        foreach ($lateRecords as $lateRecord) {
            $lateCheckIn = \Carbon\Carbon::parse($lateRecord->check_in_time);
            $target = \Carbon\Carbon::parse($lateRecord->date->format('Y-m-d') . ' ' . AttendanceSetting::get('check_in_time', '07:00:00'));
            $minutes = $lateCheckIn->diffInMinutes($target, false);
            
            if ($minutes > 0) {
                $totalMinutesLate += $minutes;
                $lateMinutesByDate[$lateRecord->date->format('Y-m-d')] = $minutes;
            }
        }

        // Calculate trend (comparing first half vs second half of the month)
        $trend = $this->calculateLateTrend($lateMinutesByDate);

        // Format and send warning message
        $schoolName = AttendanceSetting::get('school_name', 'Sekolah');
        $message = $this->formatLateWarningMessage(
            $student,
            $schoolName,
            $lateCount,
            $totalMinutesLate,
            $trend
        );

        // Send notification ke semua nomor
        $result = ['success' => false];
        foreach ($student->getParentPhones() as $phone) {
            $result = $this->whatsAppService->sendParentNotification($phone, $message);
        }

        // Log notification attempt
        $this->logNotification($student->id, 'late_warning', $result);

        Log::info("Late warning sent", [
            'student_id' => $student->id,
            'late_count' => $lateCount,
            'total_minutes' => $totalMinutesLate,
            'trend' => $trend
        ]);
    }

    /**
     * Calculate late trend based on dates.
     *
     * @param array $lateMinutesByDate
     * @return string
     */
    private function calculateLateTrend(array $lateMinutesByDate): string
    {
        if (count($lateMinutesByDate) < 2) {
            return 'stable';
        }

        // Sort by date
        ksort($lateMinutesByDate);
        $dates = array_keys($lateMinutesByDate);
        $values = array_values($lateMinutesByDate);

        // Split into two halves
        $midpoint = (int) floor(count($values) / 2);
        $firstHalf = array_slice($values, 0, $midpoint);
        $secondHalf = array_slice($values, $midpoint);

        if (empty($firstHalf) || empty($secondHalf)) {
            return 'stable';
        }

        $avgFirst = array_sum($firstHalf) / count($firstHalf);
        $avgSecond = array_sum($secondHalf) / count($secondHalf);

        // Calculate percentage change
        $percentageChange = (($avgSecond - $avgFirst) / $avgFirst) * 100;

        if ($percentageChange > 20) {
            return 'meningkat';
        } elseif ($percentageChange < -20) {
            return 'menurun';
        } else {
            return 'stabil';
        }
    }

    /**
     * Format late warning message.
     *
     * @param AttendanceStudent $student
     * @param string $schoolName
     * @param int $lateCount
     * @param int $totalMinutes
     * @param string $trend
     * @return string
     */
    private function formatLateWarningMessage(
        AttendanceStudent $student,
        string $schoolName,
        int $lateCount,
        int $totalMinutes,
        string $trend
    ): string {
        $trendEmoji = match ($trend) {
            'meningkat' => '📈',
            'menurun' => '📉',
            default => '➡️',
        };

        $trendLabel = match ($trend) {
            'meningkat' => 'Meningkat',
            'menurun' => 'Menurun',
            default => 'Stabil',
        };

        $message = "🏫 *{$schoolName}*\n";
        $message .= "⚠️ *PERINGATAN KETERLAMBATAN*\n\n";
        $message .= "Siswa: *{$student->nama}*\n";
        $message .= "Kelas: {$student->kelas->nama_kelas}\n\n";
        $message .= "📊 *Statistik Bulan Ini:*\n";
        $message .= "• Total Terlambat: *{$lateCount}x*\n";
        $message .= "• Akumulasi Waktu: *{$totalMinutes} menit*\n";
        $message .= "• Trend: {$trendEmoji} *{$trendLabel}*\n\n";
        $message .= "⚠️ Mohon perhatian lebih untuk kedisiplinan waktu.\n";
        $message .= "Keterlambatan berulang dapat mempengaruhi prestasi belajar.\n\n";
        $message .= "_Pesan otomatis dari sistem absensi_";

        return $message;
    }

    /**
     * Send check-out notification to parent.
     *
     * @param AttendanceStudent $student
     * @param AttendanceRecord $record
     * @return void
     */
    public function notifyCheckOut(AttendanceStudent $student, AttendanceRecord $record): void
    {
        // Check if parent notification is enabled
        $enabled = AttendanceSetting::get('enable_parent_notification', 'true');
        if ($enabled !== 'true' && $enabled !== '1' && $enabled !== 1) {
            return;
        }

        // Check if student has parent phone number
        $phones = $student->getParentPhones();
        if (empty($phones)) {
            return;
        }

        // Get school name
        $schoolName = AttendanceSetting::get('school_name', 'Sekolah');

        // Format message
        $message = $this->formatCheckOutMessage($student, $record, $schoolName);

        // Check if photo should be included
        $includePhoto = AttendanceSetting::get('include_photo_in_notification', 'true');
        $shouldIncludePhoto = in_array($includePhoto, ['true', '1', 1, true], true);
        $photoPath = $shouldIncludePhoto ? $record->check_out_photo : null;

        // Send notification ke semua nomor
        $result = ['success' => false];
        foreach ($phones as $phone) {
            $result = $this->whatsAppService->sendParentNotification($phone, $message, $photoPath);
        }

        // Log notification attempt
        $this->logNotification($student->id, 'check_out', $result);
    }

    /**
     * Format check-in message.
     */
    private function formatCheckInMessage(AttendanceStudent $student, AttendanceRecord $record, string $schoolName): string
    {
        $statusLabel = match ($record->status) {
            'hadir' => '✅ Hadir',
            'terlambat' => '⚠️ Terlambat',
            'alpha' => '❌ Alpha',
            'izin' => '📝 Izin',
            default => ucfirst($record->status),
        };

        $time = \Carbon\Carbon::parse($record->check_in_time)->format('H:i');

        $message = "🏫 *{$schoolName}*\n";
        $message .= "📍 Notifikasi Absensi\n\n";
        $message .= "Siswa: *{$student->nama}*\n";
        $message .= "Kelas: {$student->kelas->nama_kelas}\n";
        $message .= "Waktu Masuk: *{$time}*\n";
        $message .= "Status: {$statusLabel}\n";
        $message .= "\n_Pesan otomatis dari sistem absensi_";

        return $message;
    }

    /**
     * Format check-out message.
     */
    private function formatCheckOutMessage(AttendanceStudent $student, AttendanceRecord $record, string $schoolName): string
    {
        $time = \Carbon\Carbon::parse($record->check_out_time)->format('H:i');

        $message = "🏫 *{$schoolName}*\n";
        $message .= "📍 Notifikasi Pulang\n\n";
        $message .= "Siswa: *{$student->nama}*\n";
        $message .= "Kelas: {$student->kelas->nama_kelas}\n";
        $message .= "Waktu Pulang: *{$time}*\n";
        $message .= "\n_Pesan otomatis dari sistem absensi_";

        return $message;
    }

    /**
     * Log notification attempt.
     */
    private function logNotification(int $studentId, string $action, array $result): void
    {
        try {
            AttendanceLog::create([
                'student_id' => $studentId,
                'action' => 'notification',
                'message' => "WhatsApp notification for {$action}",
                'response' => json_encode($result),
                'status' => $result['success'] ? 'success' : 'failed',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log notification: ' . $e->getMessage());
        }
    }

    /**
     * Send absent notification to parent.
     *
     * @param AttendanceStudent $student
     * @param AttendanceRecord $record
     * @return void
     */
    public function notifyAbsent(AttendanceStudent $student, AttendanceRecord $record): void
    {
        // Check if parent notification is enabled
        $enabled = AttendanceSetting::get('enable_parent_notification', 'true');
        if ($enabled !== 'true' && $enabled !== '1' && $enabled !== 1) {
            return;
        }

        // Check if student has parent phone number
        $phones = $student->getParentPhones();
        if (empty($phones)) {
            return;
        }

        // Get school name
        $schoolName = AttendanceSetting::get('school_name', 'Sekolah');

        // Format message
        $message = "🏫 *{$schoolName}*\n";
        $message .= "⚠️ *Notifikasi Ketidakhadiran*\n\n";
        $message .= "Siswa: *{$student->nama}*\n";
        $message .= "Kelas: {$student->kelas->nama_kelas}\n";
        $message .= "Tanggal: " . $record->date->format('d/m/Y') . "\n";
        $message .= "Status: ❌ *Alpha (Tidak Hadir)*\n\n";
        $message .= "Mohon segera menghubungi pihak sekolah.\n";
        $message .= "\n_Pesan otomatis dari sistem absensi_";

        // Send notification ke semua nomor
        $result = ['success' => false];
        foreach ($phones as $phone) {
            $result = $this->whatsAppService->sendParentNotification($phone, $message);
        }

        // Log notification attempt
        $this->logNotification($student->id, 'absent', $result);
    }
}
