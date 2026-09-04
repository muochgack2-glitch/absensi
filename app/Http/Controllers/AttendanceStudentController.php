<?php

namespace App\Http\Controllers;

use App\Models\AttendanceStudent;
use App\Models\AttendanceClass;
use App\Models\AttendanceSetting;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceStudentController extends Controller
{
    public function __construct(
        private QRCodeService $qrCodeService
    ) {}

    /**
     * Display a listing of students.
     * 
     * GET /attendance/students
     */
    public function index(Request $request)
    {
        $query = AttendanceStudent::with('kelas');

        // Search by nama or nis
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Filter by class
        if ($classId = $request->input('kelas_id')) {
            $query->where('kelas_id', $classId);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $isActive = ($status === 'active');
            $query->where('is_active', $isActive);
        }

        // Filter by QR Code
        if ($qr = $request->input('qr')) {
            if ($qr === 'has_qr') {
                $query->whereNotNull('qr_code_path');
            } elseif ($qr === 'no_qr') {
                $query->whereNull('qr_code_path');
            }
        }

        // Filter by tingkat
        if ($tingkat = $request->input('tingkat')) {
            $query->whereHas('kelas', function ($q) use ($tingkat) {
                $q->where('tingkat', $tingkat);
            });
        }

        // Sortable columns
        $sortable = ['nis', 'nama', 'kelas_id'];
        $sortBy = in_array($request->input('sort_by'), $sortable) ? $request->input('sort_by') : 'nama';
        $sortDir = $request->input('sort_dir') === 'desc' ? 'desc' : 'asc';

        $perPage = $request->input('per_page', 15);
        $students = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

        return view('attendance.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     * 
     * GET /attendance/students/create
     */
    public function create()
    {
        $classes = AttendanceClass::where('is_active', true)
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return view('attendance.students.create', compact('classes'));
    }

    /**
     * Store a newly created student.
     * 
     * POST /attendance/students
     */
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'nis' => [
                'required', 'string', 'max:50',
                Rule::unique('attendance_students', 'nis')
                    ->where('tahun_ajaran', AttendanceSetting::get('active_tahun_ajaran')),
            ],
            'nama' => 'required|string|max:255',
            'kelas_id' => 'required|exists:attendance_classes,id',
            'no_hp_ortu'  => 'nullable|string|max:20',
            'no_hp_ortu2' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        // Handle foto profil upload
        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')
                ->store('attendance/profiles', 'public');
            $validated['foto_profil'] = $path;
        }

        // Create student
        $student = AttendanceStudent::create($validated);

        // Generate QR Code
        $qrPath = $this->qrCodeService->generateQRCode($student->nis);
        $student->update(['qr_code_path' => $qrPath]);

        return redirect()->route('attendance.students.index')
            ->with('success', 'Siswa berhasil ditambahkan dan QR Code telah di-generate');
    }

    /**
     * Display the specified student.
     * 
     * GET /attendance/students/{id}
     */
    public function show(AttendanceStudent $student)
    {
        $student->load(['kelas', 'attendanceRecords' => function ($query) {
            $query->orderBy('date', 'desc')->limit(10);
        }]);

        $schoolName = \App\Models\AttendanceSetting::get('school_name', 'SMK PGRI Blora');

        return view('attendance.students.show', compact('student', 'schoolName'));
    }

    /**
     * Show the form for editing the specified student.
     * 
     * GET /attendance/students/{id}/edit
     */
    public function edit(AttendanceStudent $student)
    {
        $classes = AttendanceClass::where('is_active', true)
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return view('attendance.students.edit', compact('student', 'classes'));
    }

    /**
     * Update the specified student.
     * 
     * PUT/PATCH /attendance/students/{id}
     */
    public function update(Request $request, AttendanceStudent $student)
    {
        // Validate request
        $validated = $request->validate([
            'nis' => [
                'required', 'string', 'max:50',
                Rule::unique('attendance_students', 'nis')
                    ->where('tahun_ajaran', AttendanceSetting::get('active_tahun_ajaran'))
                    ->ignore($student->id),
            ],
            'nama' => 'required|string|max:255',
            'kelas_id' => 'required|exists:attendance_classes,id',
            'no_hp_ortu'  => 'nullable|string|max:20',
            'no_hp_ortu2' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        // Handle foto profil upload
        if ($request->hasFile('foto_profil')) {
            // Delete old photo if exists
            if ($student->foto_profil) {
                \Storage::disk('public')->delete($student->foto_profil);
            }

            $path = $request->file('foto_profil')
                ->store('attendance/profiles', 'public');
            $validated['foto_profil'] = $path;
        }

        // Update student
        $student->update($validated);

        return redirect()->route('attendance.students.index')
            ->with('success', 'Data siswa berhasil diperbarui');
    }

    /**
     * Remove the specified student.
     * 
     * DELETE /attendance/students/{id}
     */
    public function destroy(AttendanceStudent $student)
    {
        // Delete QR Code
        if ($student->qr_code_path) {
            $this->qrCodeService->deleteQRCode($student->nis);
        }

        // Delete foto profil
        if ($student->foto_profil) {
            \Storage::disk('public')->delete($student->foto_profil);
        }

        // Delete student (attendance records will cascade delete)
        $student->delete();

        return redirect()->route('attendance.students.index')
            ->with('success', 'Siswa berhasil dihapus');
    }

    /**
     * Show Excel import form.
     * 
     * GET /attendance/students/import
     */
    public function importForm()
    {
        return view('attendance.students.import');
    }

    /**
     * Download Excel template.
     * 
     * GET /attendance/students/export/template
     */
    public function exportTemplate()
    {
        // Generate and download template directly without storing
        return Excel::download(
            new \App\Exports\StudentTemplateExport(), 
            'Template-Import-Siswa.xlsx'
        );
    }

    /**
     * Import students from Excel file.
     * 
     * POST /attendance/students/import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $import = new \App\Imports\AttendanceStudentImport($this->qrCodeService);
            Excel::import($import, $request->file('file'));

            $results = $import->getResults();

            return redirect()->route('attendance.students.index')
                ->with('success', "Import berhasil! {$results['success']} siswa ditambahkan, {$results['failed']} gagal");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    // ================================================================
    // EXPORT SISWA KE EXCEL
    // ================================================================

    public function exportExcel(Request $request)
    {
        $classId = $request->get('class') ?? $request->get('class_id');
        $status = $request->get('status') ?? $request->get('is_active');

        // Map status values
        if ($status === '1') $status = 'active';
        if ($status === '0') $status = 'inactive';

        $fileName = 'data_siswa_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\StudentsExport($classId, $status),
            $fileName
        );
    }

    // ================================================================
    // BULK ACTION SISWA
    // ================================================================

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action'      => 'required|in:activate,deactivate,delete',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:attendance_students,id',
        ]);

        $ids    = $validated['student_ids'];
        $action = $validated['action'];
        $count  = count($ids);

        switch ($action) {
            case 'activate':
                AttendanceStudent::whereIn('id', $ids)->update(['is_active' => true]);
                $msg = "{$count} siswa berhasil diaktifkan.";
                break;
            case 'deactivate':
                AttendanceStudent::whereIn('id', $ids)->update(['is_active' => false]);
                $msg = "{$count} siswa berhasil dinonaktifkan.";
                break;
            case 'delete':
                AttendanceStudent::whereIn('id', $ids)->delete();
                $msg = "{$count} siswa berhasil dihapus.";
                break;
        }

        return redirect()->route('attendance.students.index')->with('success', $msg ?? 'Aksi berhasil.');
    }

    /**
     * Bulk upload foto profil siswa.
     * Nama file harus = NIS siswa (contoh: 12345.jpg)
     *
     * POST /attendance/students/bulk-foto
     */
    public function bulkFoto(Request $request)
    {
        $request->validate([
            'fotos'   => 'required|array|min:1|max:200',
            'fotos.*' => 'required|image|max:3072', // max 3MB per file
        ], [
            'fotos.required'   => 'Pilih minimal 1 foto.',
            'fotos.*.image'    => 'Semua file harus berupa gambar (JPG, PNG, GIF).',
            'fotos.*.max'      => 'Ukuran setiap foto maksimal 3MB.',
        ]);

        $berhasil = [];
        $gagal    = [];

        foreach ($request->file('fotos') as $file) {
            // Ambil NIS dari nama file (tanpa ekstensi)
            $nis = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $student = AttendanceStudent::where('nis', $nis)->first();

            if (!$student) {
                $gagal[] = ['file' => $file->getClientOriginalName(), 'reason' => 'NIS tidak ditemukan'];
                continue;
            }

            try {
                // Hapus foto lama jika ada
                if ($student->foto_profil && \Storage::disk('public')->exists($student->foto_profil)) {
                    \Storage::disk('public')->delete($student->foto_profil);
                }

                // Simpan foto baru
                $path = $file->store('students/foto', 'public');
                $student->update(['foto_profil' => $path]);

                $berhasil[] = ['nama' => $student->nama, 'nis' => $nis, 'file' => $file->getClientOriginalName()];
            } catch (\Exception $e) {
                $gagal[] = ['file' => $file->getClientOriginalName(), 'reason' => 'Error: ' . $e->getMessage()];
            }
        }

        $total = count($berhasil) + count($gagal);
        return redirect()
            ->route('attendance.students.index')
            ->with('bulk_foto_result', [
                'total'    => $total,
                'berhasil' => $berhasil,
                'gagal'    => $gagal,
            ]);
    }

    /**
     * Bulk download kartu QR siswa sebagai ZIP (PHP GD).
     * GET /attendance/students/bulk-qr-cards?class_id=&format=png
     */
    public function bulkQrCards(Request $request)
    {
        set_time_limit(300); // 5 menit untuk proses banyak siswa

        $classId    = $request->input('class_id');
        $schoolName = \App\Models\AttendanceSetting::get('school_name', 'SMK PGRI Blora');

        $query = AttendanceStudent::with('kelas')
            ->where('is_active', true)
            ->whereNotNull('qr_code_path');

        if ($classId) {
            $query->where('kelas_id', $classId);
        }

        $students = $query->orderBy('nama')->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa dengan QR code untuk kelas yang dipilih.');
        }

        // Nama file ZIP
        $className = $classId
            ? (AttendanceClass::find($classId)?->nama_kelas ?? 'kelas')
            : 'semua';
        $zipName   = 'kartu_qr_' . \Str::slug($className) . '_' . now()->format('Ymd') . '.zip';
        $zipPath   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        foreach ($students as $student) {
            $card = $this->generateQrCard($student, $schoolName);
            if (!$card) continue;

            ob_start();
            imagepng($card);
            $imgData = ob_get_clean();
            imagedestroy($card);

            $filename = $student->nis . '_' . \Str::slug($student->nama) . '.png';
            $zip->addFromString($filename, $imgData);
        }

        $zip->close();

        return response()->download($zipPath, $zipName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Generate satu kartu QR siswa menggunakan PHP GD.
     */
    private function generateQrCard(AttendanceStudent $student, string $schoolName): ?\GdImage
    {
        $W = 480; $H = 640;
        $img = imagecreatetruecolor($W, $H);
        if (!$img) return null;

        // Warna
        $white   = imagecolorallocate($img, 255, 255, 255);
        $purple  = imagecolorallocate($img, 79, 70, 229);
        $purple2 = imagecolorallocate($img, 124, 58, 237);
        $gray    = imagecolorallocate($img, 107, 114, 128);
        $dark    = imagecolorallocate($img, 17, 24, 39);
        $lightbg = imagecolorallocate($img, 249, 250, 251);
        $border  = imagecolorallocate($img, 229, 231, 235);

        // Background putih
        imagefill($img, 0, 0, $white);

        // Header solid purple (GD tidak support gradient murni)
        imagefilledrectangle($img, 0, 0, $W, 90, $purple);

        // Nama sekolah di header
        // GD default font: 1-5 (built-in bitmap fonts)
        $font = 5; // ukuran terbesar built-in
        $tw   = imagefontwidth($font) * strlen($schoolName);
        $tx   = max(0, ($W - $tw) / 2);
        imagestring($img, $font, (int)$tx, 20, $schoolName, $white);
        $sub  = 'Kartu Absensi Siswa';
        $tw2  = imagefontwidth(3) * strlen($sub);
        imagestring($img, 3, (int)(($W - $tw2) / 2), 50, $sub, $white);
        $yr   = date('Y');
        $tw3  = imagefontwidth(2) * strlen($yr);
        imagestring($img, 2, (int)(($W - $tw3) / 2), 72, $yr, $white);

        // Foto profil (lingkaran simulasi — GD tidak mendukung arc filled dgn foto, pakai kotak rounded)
        $qrY = 110;
        if ($student->foto_profil && \Storage::disk('public')->exists($student->foto_profil)) {
            $fotoPath = storage_path('app/public/' . $student->foto_profil);
            $ext      = strtolower(pathinfo($fotoPath, PATHINFO_EXTENSION));
            $fotoSrc  = match($ext) {
                'jpg','jpeg' => @imagecreatefromjpeg($fotoPath),
                'png'        => @imagecreatefrompng($fotoPath),
                'gif'        => @imagecreatefromgif($fotoPath),
                default      => false,
            };
            if ($fotoSrc) {
                $fSize = 90;
                $fx    = (int)(($W - $fSize) / 2);
                $fy    = 100;
                // Resize foto ke $fSize x $fSize
                $resized = imagecreatetruecolor($fSize, $fSize);
                imagecopyresampled($resized, $fotoSrc, 0, 0, 0, 0,
                    $fSize, $fSize, imagesx($fotoSrc), imagesy($fotoSrc));
                // Border kotak foto
                imagefilledrectangle($img, $fx - 3, $fy - 3, $fx + $fSize + 2, $fy + $fSize + 2, $purple);
                imagecopy($img, $resized, $fx, $fy, 0, 0, $fSize, $fSize);
                imagedestroy($resized);
                imagedestroy($fotoSrc);
                $qrY = 210;
            }
        }

        // QR Code — generate via BaconQrCode matrix + GD manual draw (tanpa Imagick/chillerlan)
        try {
            $qrService = app(\App\Services\QRCodeService::class);
            $qrContent = $qrService->buildQRToken($student->nis);

            $matrix = \BaconQrCode\Encoder\Encoder::encode(
                $qrContent,
                \BaconQrCode\Common\ErrorCorrectionLevel::L(),
                'ISO-8859-1'
            )->getMatrix();

            $mWidth  = $matrix->getWidth();
            $margin  = 4;
            $total   = $mWidth + ($margin * 2);
            $qrSize  = 220;
            $scale   = max(1, (int) floor($qrSize / $total));
            $imgSize = $total * $scale;
            $qrX     = (int)(($W - $imgSize) / 2);

            // Background putih QR
            imagefilledrectangle($img, $qrX - 8, $qrY - 8, $qrX + $imgSize + 8, $qrY + $imgSize + 8, $lightbg);

            $black = imagecolorallocate($img, 0, 0, 0);
            for ($y = 0; $y < $mWidth; $y++) {
                for ($x = 0; $x < $mWidth; $x++) {
                    if ($matrix->get($x, $y) === 1) {
                        $px = $qrX + ($x + $margin) * $scale;
                        $py = $qrY + ($y + $margin) * $scale;
                        imagefilledrectangle($img, $px, $py, $px + $scale - 1, $py + $scale - 1, $black);
                    }
                }
            }
        } catch (\Throwable $e) {
            // QR gagal di-render — lanjut tanpa QR
        }

        // Identitas teks
        $textY = $qrY + 230 + 10;

        // Nama (potong jika terlalu panjang)
        $nama  = mb_strlen($student->nama) > 28 ? mb_substr($student->nama, 0, 26) . '..' : $student->nama;
        $tw    = imagefontwidth(5) * strlen($nama);
        imagestring($img, 5, (int)(($W - $tw) / 2), $textY, $nama, $dark);

        // NIS
        $nis   = 'NIS: ' . $student->nis;
        $tw2   = imagefontwidth(3) * strlen($nis);
        imagestring($img, 3, (int)(($W - $tw2) / 2), $textY + 28, $nis, $gray);

        // Kelas
        $kelas = $student->kelas?->nama_kelas ?? '-';
        $tw3   = imagefontwidth(4) * strlen($kelas);
        imagestring($img, 4, (int)(($W - $tw3) / 2), $textY + 50, $kelas, $purple);

        // Footer
        imagefilledrectangle($img, 0, $H - 44, $W, $H, $lightbg);
        $foot  = 'Scan QR Code ini untuk absensi harian';
        $tw4   = imagefontwidth(2) * strlen($foot);
        imagestring($img, 2, (int)(($W - $tw4) / 2), $H - 36, $foot, $gray);

        // Border kartu
        imagerectangle($img, 0, 0, $W - 1, $H - 1, $border);

        return $img;
    }

    /**
     * Tampilkan form bulk edit nomor HP orang tua siswa.
     * GET /attendance/students/phones
     */
    public function phonesForm(Request $request)
    {
        $classes  = AttendanceClass::orderBy('nama_kelas')->get();
        $kelasId  = $request->input('kelas_id');
        $students = collect();

        if ($kelasId) {
            $students = AttendanceStudent::where('kelas_id', $kelasId)
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();
        }

        return view('attendance.students.phones', compact('classes', 'students', 'kelasId'));
    }

    /**
     * Simpan bulk update nomor HP orang tua siswa.
     * POST /attendance/students/phones
     */
    public function phonesSave(Request $request)
    {
        $phones = $request->input('phones', []);
        $count  = 0;

        foreach ($phones as $id => $data) {
            $student = AttendanceStudent::find((int)$id);
            if (!$student) continue;

            $student->no_hp_ortu  = $data['no_hp_ortu']  ?? $student->no_hp_ortu;
            $student->no_hp_ortu2 = $data['no_hp_ortu2'] ?? $student->no_hp_ortu2;
            $student->save();
            $count++;
        }

        return redirect()
            ->route('attendance.students.phones', ['kelas_id' => $request->input('kelas_id')])
            ->with('success', "✅ {$count} nomor HP berhasil disimpan.");
    }
}
