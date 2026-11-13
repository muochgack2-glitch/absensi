<?php

namespace App\Imports;

use App\Models\AttendanceStudent;
use App\Models\AttendanceClass;
use App\Models\AttendanceSetting;
use App\Services\QRCodeService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Facades\Log;

class AttendanceStudentImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    private int $successCount = 0;
    private int $failedCount = 0;
    private array $customErrors = [];

    public function __construct(
        private QRCodeService $qrCodeService
    ) {}

    /**
     * Prepare / sanitize each row before running validation.
     * Mengonversi angka (integer/float) dari Excel menjadi string bersih.
     */
    public function prepareForValidation($data, $index)
    {
        // Pastikan NIS selalu berupa string bersih
        if (isset($data['nis'])) {
            $data['nis'] = trim((string) $data['nis']);
        }

        // Pastikan Nama selalu berupa string bersih
        if (isset($data['nama'])) {
            $data['nama'] = trim((string) $data['nama']);
        }

        // Pastikan Kelas ID berupa integer jika numerik
        if (isset($data['kelas_id'])) {
            $data['kelas_id'] = is_numeric($data['kelas_id']) ? (int) $data['kelas_id'] : $data['kelas_id'];
        }

        // Sanitasi no_hp_ortu
        if (isset($data['no_hp_ortu'])) {
            $val = trim((string) $data['no_hp_ortu']);
            $data['no_hp_ortu'] = $val !== '' ? $val : null;
        }

        if (isset($data['no_hp_ortu2'])) {
            $val = trim((string) $data['no_hp_ortu2']);
            $data['no_hp_ortu2'] = $val !== '' ? $val : null;
        }

        return $data;
    }

    /**
     * Transform each row into a model.
     */
    public function model(array $row)
    {
        $nis      = trim((string) ($row['nis'] ?? ''));
        $nama     = trim((string) ($row['nama'] ?? ''));
        $kelasId  = isset($row['kelas_id']) && is_numeric($row['kelas_id']) ? (int) $row['kelas_id'] : null;
        $noHpOrtu = !empty($row['no_hp_ortu']) ? trim((string) $row['no_hp_ortu']) : null;
        $noHpOrtu2= !empty($row['no_hp_ortu2']) ? trim((string) $row['no_hp_ortu2']) : null;

        try {
            // Check if student with this NIS already exists
            $existing = AttendanceStudent::where('nis', $nis)->first();
            if ($existing) {
                $this->failedCount++;
                $this->customErrors[] = "NIS {$nis} sudah ada";
                return null;
            }

            // Validate kelas_id exists
            $class = AttendanceClass::find($kelasId);
            if (!$class) {
                $this->failedCount++;
                $this->customErrors[] = "Kelas ID {$kelasId} tidak ditemukan untuk NIS {$nis}";
                return null;
            }

            $activeTahunAjaran = AttendanceSetting::get('active_tahun_ajaran');

            // Create student
            $student = AttendanceStudent::create([
                'nis'          => $nis,
                'nama'         => $nama,
                'kelas_id'     => $kelasId,
                'no_hp_ortu'   => $noHpOrtu,
                'no_hp_ortu2'  => $noHpOrtu2,
                'is_active'    => true,
                'tahun_ajaran' => $activeTahunAjaran,
            ]);

            // Generate QR Code
            try {
                $qrPath = $this->qrCodeService->generateQRCode($student->nis);
                $student->update(['qr_code_path' => $qrPath]);
            } catch (\Exception $e) {
                Log::error("Failed to generate QR for NIS {$student->nis}: " . $e->getMessage());
                // Student is still created, just without QR Code
            }

            $this->successCount++;
            return $student;

        } catch (\Exception $e) {
            $this->failedCount++;
            $this->customErrors[] = "Error pada NIS {$nis}: " . $e->getMessage();
            Log::error("Import error for row: " . json_encode($row) . " | Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'nis'         => 'required|max:50',
            'nama'        => 'required|max:255',
            'kelas_id'    => 'required|integer|exists:attendance_classes,id',
            'no_hp_ortu'  => 'nullable|max:20',
            'no_hp_ortu2' => 'nullable|max:20',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function customValidationMessages()
    {
        return [
            'nis.required'      => 'NIS wajib diisi',
            'nis.max'           => 'NIS maksimal 50 karakter',
            'nama.required'     => 'Nama wajib diisi',
            'nama.max'          => 'Nama maksimal 255 karakter',
            'kelas_id.required' => 'Kelas ID wajib diisi',
            'kelas_id.integer'  => 'Kelas ID harus berupa angka',
            'kelas_id.exists'   => 'Kelas ID tidak valid / tidak ditemukan di sistem',
            'no_hp_ortu.max'    => 'No HP Ortu maksimal 20 digit',
            'no_hp_ortu2.max'   => 'No HP Ortu 2 maksimal 20 digit',
        ];
    }

    /**
     * Get import results.
     */
    public function getResults(): array
    {
        return [
            'success' => $this->successCount,
            'failed'  => $this->failedCount,
            'errors'  => $this->customErrors,
        ];
    }

    /**
     * Handle errors during import.
     */
    public function onError(\Throwable $e)
    {
        $this->failedCount++;
        $this->customErrors[] = $e->getMessage();
        Log::error("Import error: " . $e->getMessage());
    }
}
