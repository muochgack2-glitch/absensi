<?php

namespace App\Services;

use App\Models\AttendanceStudent;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSetting;
use App\Models\AttendanceLog;
use App\Models\WhatsAppTemplate;
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
            'hadir'     => '✅ Hadir',
            'terlambat' => '⚠️ Terlambat',
            'alpha'     => '❌ Alpha',
            'izin'      => '📝 Izin',
            default     => ucfirst($record->status),
        };

        $time        = \Carbon\Carbon::parse($record->check_in_time)->format('H:i');
        $tanggal     = \Carbon\Carbon::parse($record->date)->format('d/m/Y');
        $hariTanggal = \Carbon\Carbon::parse($record->date)->locale('id')->translatedFormat('l, d/m/Y');

        // Setting waktu
        $jamResmiMasuk    = AttendanceSetting::get('check_in_time', '07:00');
        $toleransiMenit   = (int) AttendanceSetting::get('tolerance_minutes', 15);
        $checkInCarbon    = \Carbon\Carbon::parse($record->check_in_time);
        $jamResmiCarbon   = \Carbon\Carbon::parse($jamResmiMasuk);

        // Hitung menit setelah jam resmi (untuk hadir-toleransi dan terlambat)
        $menitSetelahResmi = max(0, $checkInCarbon->diffInMinutes($jamResmiCarbon, false));

        // Deteksi hadir dalam toleransi (masuk setelah jam resmi tapi masih dalam toleransi)
        $isDalamToleransi = $record->status === 'hadir' && $menitSetelahResmi > 0;

        $data = [
            'sekolah'         => $schoolName,
            'nama'            => $student->nama,
            'kelas'           => $student->kelas->nama_kelas,
            'waktu'           => $time,
            'status'          => $statusLabel,
            'tanggal'         => $tanggal,
            'hari_tanggal'    => $hariTanggal,
            'terlambat'       => $menitSetelahResmi,   // menit setelah jam resmi
            'toleransi'       => $toleransiMenit,       // dari setting (misal: 15)
            'jam_resmi_masuk' => $jamResmiMasuk,        // misal: 07:00
        ];

        // Pilih template berdasarkan kondisi
        if ($record->status === 'terlambat') {
            $templateName = 'check_in_terlambat';
        } elseif ($record->status === 'izin') {
            $templateName = 'check_in_izin';
        } elseif ($isDalamToleransi) {
            $templateName = 'check_in_toleransi';
        } else {
            $templateName = 'check_in_hadir';
        }

        // Coba pakai template dari DB
        $message = $this->resolveTemplateByName($templateName, $data);
        if ($message) {
            return $message;
        }

        // Fallback ke format hardcode
        $message  = "🏫 *{$schoolName}*\n";
        $message .= "📍 Notifikasi Absensi\n\n";
        $message .= "Siswa: *{$student->nama}*\n";
        $message .= "Kelas: {$student->kelas->nama_kelas}\n";
        $message .= "Hari/Tgl: {$hariTanggal}\n";
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
        $time          = \Carbon\Carbon::parse($record->check_out_time)->format('H:i');
        $tanggal       = \Carbon\Carbon::parse($record->date)->format('d/m/Y');
        $hariTanggal   = \Carbon\Carbon::parse($record->date)->locale('id')->translatedFormat('l, d/m/Y');
        $isPulangCepat = $record->check_out_status === 'pulang_cepat';
        $statusLabel   = $isPulangCepat ? '⚠️ Pulang Lebih Awal' : '✅ Pulang Normal';
        $jamResmi      = \App\Models\AttendanceSetting::get('check_out_time', '15:00');
        $peringatan    = $isPulangCepat
            ? "⚠️ _Siswa meninggalkan sekolah sebelum jam pulang ({$jamResmi})_"
            : '';

        $data = [
            'sekolah'      => $schoolName,
            'nama'         => $student->nama,
            'kelas'        => $student->kelas->nama_kelas,
            'waktu'        => $time,
            'status'       => $statusLabel,
            'tanggal'      => $tanggal,
            'hari_tanggal' => $hariTanggal,
            'jam_resmi'    => $jamResmi,
            'peringatan'   => $peringatan,
        ];

        // Pilih template berdasarkan kondisi pulang
        $templateName = $isPulangCepat ? 'check_out_cepat' : 'check_out_normal';

        // Coba pakai template dari DB
        $message = $this->resolveTemplateByName($templateName, $data);
        if ($message) {
            return $message;
        }

        // Fallback ke format hardcode
        $icon  = $isPulangCepat ? '⚠️' : '🏫';
        $label = $isPulangCepat ? 'Notifikasi Pulang Lebih Awal' : 'Notifikasi Pulang';

        $message  = "{$icon} *{$schoolName}*\n";
        $message .= "📍 {$label}\n\n";
        $message .= "Siswa: *{$student->nama}*\n";
        $message .= "Kelas: {$student->kelas->nama_kelas}\n";
        $message .= "Hari/Tgl: {$hariTanggal}\n";
        $message .= "Waktu Pulang: *{$time}*\n";

        if ($isPulangCepat) {
            $message .= "{$peringatan}\n";
        }

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
        $tanggal     = \Carbon\Carbon::parse($record->date)->format('d/m/Y');
        $hariTanggal = \Carbon\Carbon::parse($record->date)->locale('id')->translatedFormat('l, d/m/Y');

        $data = [
            'sekolah'      => $schoolName,
            'nama'         => $student->nama,
            'kelas'        => $student->kelas->nama_kelas,
            'tanggal'      => $tanggal,
            'hari_tanggal' => $hariTanggal,
        ];

        // Coba pakai template dari DB
        $message = $this->resolveTemplateByName('absent_notification', $data);
        if (!$message) {
            // Fallback ke format hardcode
            $message  = "🏫 *{$schoolName}*\n";
            $message .= "⚠️ *Notifikasi Ketidakhadiran*\n\n";
            $message .= "Siswa: *{$student->nama}*\n";
            $message .= "Kelas: {$student->kelas->nama_kelas}\n";
            $message .= "Hari/Tgl: {$hariTanggal}\n";
            $message .= "Status: ❌ *Alpha (Tidak Hadir)*\n\n";
            $message .= "Mohon segera menghubungi pihak sekolah.\n";
            $message .= "\n_Pesan otomatis dari sistem absensi_";
        }

        // Send notification ke semua nomor
        $result = ['success' => false];
        foreach ($phones as $phone) {
            $result = $this->whatsAppService->sendParentNotification($phone, $message);
        }

        // Log notification attempt
        $this->logNotification($student->id, 'absent', $result);
    }

    /**
     * Resolve message from active DB template by exact name, fallback to empty string.
     */
    private function resolveTemplateByName(string $name, array $data): string
    {
        try {
            $template = WhatsAppTemplate::where('name', $name)
                ->where('is_active', true)
                ->where('auto_send', true)
                ->first();
            if ($template) {
                $template->incrementUsage();
                return $template->parse($data);
            }
        } catch (\Exception $e) {
            Log::warning("WhatsAppTemplate resolve failed for name={$name}: " . $e->getMessage());
        }
        return '';
    }

    /**
     * Resolve message by type (legacy fallback, kept for compatibility).
     */
    private function resolveTemplate(string $type, array $data): string
    {
        try {
            $template = WhatsAppTemplate::active()->autoSend()->type($type)->first();
            if ($template) {
                $template->incrementUsage();
                return $template->parse($data);
            }
        } catch (\Exception $e) {
            Log::warning("WhatsAppTemplate resolve failed for type={$type}: " . $e->getMessage());
        }
        return '';
    }

    /**
     * Kirim notifikasi koreksi ke orang tua saat admin mengubah status
     * absensi dari alpha ke status lain via Input Manual.
     *
     * @param AttendanceStudent $student
     * @param AttendanceRecord  $record
     * @param string            $statusLama  Status sebelum diubah (harus 'alpha')
     * @param string            $statusBaru  Status setelah diubah
     * @param string|null       $keterangan  Notes dari kolom keterangan input manual
     * @param string            $date        Tanggal absensi yang dikoreksi (Y-m-d)
     */
    public function notifyManualCorrection(
        AttendanceStudent $student,
        AttendanceRecord  $record,
        string            $statusLama,
        string            $statusBaru,
        ?string           $keterangan,
        string            $date
    ): void {
        // Skip jika tidak ada nomor HP ortu
        $phones = $student->getParentPhones();
        if (empty($phones)) {
            Log::debug("Manual correction notif skipped — no parent phone", ['nis' => $student->nis]);
            return;
        }

        // Label emoji per status
        $statusLabel = [
            'hadir'     => '✅ Hadir',
            'terlambat' => '⚠️ Terlambat',
            'izin'      => '📝 Izin',
            'sakit'     => '🤒 Sakit',
            'alpha'     => '❌ Alpha',
        ];

        $waktuMasuk = $record->check_in_time
            ? \Carbon\Carbon::parse($record->check_in_time)->format('H:i')
            : '-';

        $data = [
            'sekolah'         => AttendanceSetting::get('school_name', 'Sekolah'),
            'nama'            => $student->nama,
            'kelas'           => $student->kelas->nama_kelas ?? '-',
            'tanggal_absensi' => \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d/m/Y'),
            'tanggal_koreksi' => \Carbon\Carbon::today()->format('d/m/Y'),
            'status_lama'     => $statusLabel[$statusLama] ?? ucfirst($statusLama),
            'status_baru'     => $statusLabel[$statusBaru] ?? ucfirst($statusBaru),
            'waktu_masuk'     => $waktuMasuk,
            'keterangan'      => $keterangan ?: 'Koreksi oleh admin',
        ];

        $message = $this->resolveTemplateByName('manual_correction', $data);
        if (!$message) {
            Log::debug("Manual correction template not found or inactive");
            return;
        }

        $result = ['success' => false];
        foreach ($phones as $phone) {
            $result = $this->whatsAppService->sendParentNotification($phone, $message);
        }

        $this->logNotification($student->id, 'manual_correction', $result);

        Log::info("Manual correction notif sent", [
            'student_id'  => $student->id,
            'status_lama' => $statusLama,
            'status_baru' => $statusBaru,
            'date'        => $date,
        ]);
    }

    /**
     * Kirim WA ke orang tua saat admin menginput absensi PERTAMA KALI via Input Manual
     * (bukan koreksi — record sebelumnya tidak ada).
     * Menggantikan fungsi WA QR scan yang tidak bisa dilakukan siswa.
     *
     * @param AttendanceStudent $student
     * @param AttendanceRecord  $record
     * @param string            $statusBaru  hadir|terlambat|izin|sakit
     * @param string|null       $keterangan  Kolom keterangan dari form
     * @param string            $date        Tanggal absensi (Y-m-d)
     */
    public function notifyManualFirstEntry(
        AttendanceStudent $student,
        AttendanceRecord  $record,
        string            $statusBaru,
        ?string           $keterangan,
        string            $date
    ): void {
        // Skip alpha & skip — tidak ada WA
        if (in_array($statusBaru, ['alpha', 'skip'])) return;

        $phones = $student->getParentPhones();
        if (empty($phones)) {
            Log::debug("Manual first entry notif skipped — no parent phone", ['nis' => $student->nis]);
            return;
        }

        $jamResmi   = AttendanceSetting::get('jam_masuk', '07:00');
        $toleransi  = (int) AttendanceSetting::get('toleransi_menit', '15');
        $hariTanggal = \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y');
        $schoolName  = AttendanceSetting::get('school_name', 'Sekolah');

        $waktuMasuk = $record->check_in_time
            ? \Carbon\Carbon::parse($record->check_in_time)->format('H:i') : '-';

        $selisihMenit = ($record->check_in_time && $jamResmi)
            ? (int) \Carbon\Carbon::parse($jamResmi)
                ->diffInMinutes(\Carbon\Carbon::parse($record->check_in_time), false)
            : 0;

        // Pilih template berdasarkan status & waktu
        $templateName = match($statusBaru) {
            'terlambat' => 'manual_terlambat',
            'izin'      => 'manual_izin',
            'sakit'     => 'manual_sakit',
            'hadir'     => $this->resolveManualHadirTemplate($selisihMenit, $toleransi),
            default     => null,
        };

        if (!$templateName) return;

        $data = [
            'sekolah'         => $schoolName,
            'nama'            => $student->nama,
            'kelas'           => $student->kelas->nama_kelas ?? '-',
            'hari_tanggal'    => $hariTanggal,
            'tanggal'         => $date,
            'waktu'           => $waktuMasuk,
            'terlambat'       => max(0, $selisihMenit),
            'toleransi'       => $toleransi,
            'jam_resmi_masuk' => $jamResmi,
            'keterangan'      => $keterangan ?: '-',
        ];

        $message = $this->resolveTemplateByName($templateName, $data);
        if (!$message) {
            Log::debug("Manual first entry template not found or inactive", ['template' => $templateName]);
            return;
        }

        $result = ['success' => false];
        foreach ($phones as $phone) {
            $result = $this->whatsAppService->sendParentNotification($phone, $message);
        }

        $this->logNotification($student->id, 'manual_first_entry', $result);

        Log::info("Manual first entry notif sent", [
            'student_id' => $student->id,
            'status'     => $statusBaru,
            'template'   => $templateName,
            'date'       => $date,
        ]);
    }

    /**
     * Tentukan template hadir berdasarkan selisih waktu masuk vs jam resmi.
     */
    private function resolveManualHadirTemplate(int $selisihMenit, int $toleransi): string
    {
        return ($selisihMenit > 0 && $selisihMenit <= $toleransi)
            ? 'manual_toleransi'
            : 'manual_hadir';
    }

    /**
     * Kirim WA saat terjadi perubahan status SIGNIFIKAN antar grup (hadir↔absen)
     * yang bukan koreksi dari alpha dan bukan first entry.
     * Contoh: terlambat → sakit, sakit → terlambat, hadir → izin, izin → hadir.
     *
     * @param AttendanceStudent $student
     * @param AttendanceRecord  $record
     * @param string            $statusLama  Status sebelum diubah
     * @param string            $statusBaru  Status setelah diubah
     * @param string|null       $keterangan
     * @param string            $date        Tanggal absensi (Y-m-d)
     */
    public function notifyManualStatusChange(
        AttendanceStudent $student,
        AttendanceRecord  $record,
        string            $statusLama,
        string            $statusBaru,
        ?string           $keterangan,
        string            $date
    ): void {
        $phones = $student->getParentPhones();
        if (empty($phones)) {
            Log::debug("Manual status change notif skipped — no parent phone", ['nis' => $student->nis]);
            return;
        }

        $hariTanggal   = \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y');
        $schoolName    = AttendanceSetting::get('school_name', 'Sekolah');

        $labelMap = [
            'hadir'     => '✅ Hadir',
            'terlambat' => '⚠️ Terlambat',
            'izin'      => '📝 Izin',
            'sakit'     => '🤒 Sakit',
        ];

        $data = [
            'sekolah'         => $schoolName,
            'nama'            => $student->nama,
            'kelas'           => $student->kelas->nama_kelas ?? '-',
            'hari_tanggal'    => $hariTanggal,
            'tanggal_koreksi' => now()->format('d/m/Y'),
            'status_lama'     => $labelMap[$statusLama] ?? $statusLama,
            'status_baru'     => $labelMap[$statusBaru] ?? $statusBaru,
            'keterangan'      => $keterangan ?: '-',
        ];

        $message = $this->resolveTemplateByName('manual_status_change', $data);
        if (!$message) {
            Log::debug("manual_status_change template not found or inactive");
            return;
        }

        $result = ['success' => false];
        foreach ($phones as $phone) {
            $result = $this->whatsAppService->sendParentNotification($phone, $message);
        }

        $this->logNotification($student->id, 'manual_status_change', $result);

        Log::info("Manual status change notif sent", [
            'student_id'  => $student->id,
            'status_lama' => $statusLama,
            'status_baru' => $statusBaru,
            'date'        => $date,
        ]);
    }
}

