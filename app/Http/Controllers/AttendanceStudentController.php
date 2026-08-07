<?php

namespace App\Http\Controllers;

use App\Models\AttendanceStudent;
use App\Models\AttendanceClass;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
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

        $students = $query->orderBy('nama')->paginate(15);

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
            'nis' => 'required|string|max:50|unique:attendance_students,nis',
            'nama' => 'required|string|max:255',
            'kelas_id' => 'required|exists:attendance_classes,id',
            'no_hp_ortu' => 'nullable|string|max:20',
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

        return view('attendance.students.show', compact('student'));
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
            'nis' => 'required|string|max:50|unique:attendance_students,nis,' . $student->id,
            'nama' => 'required|string|max:255',
            'kelas_id' => 'required|exists:attendance_classes,id',
            'no_hp_ortu' => 'nullable|string|max:20',
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
        $classId   = $request->input('class_id');
        $isActive  = $request->input('is_active', ''); // '' = semua, '1' = aktif, '0' = nonaktif

        $query = AttendanceStudent::with('kelas')->orderBy('nama');
        if ($classId)       $query->where('kelas_id', $classId);
        if ($isActive !== '') $query->where('is_active', $isActive);

        $students = $query->get();

        // Build array data
        $rows   = [['No', 'NIS', 'Nama', 'Kelas', 'No HP Ortu', 'Status', 'Tgl Daftar']];
        foreach ($students as $i => $s) {
            $rows[] = [
                $i + 1,
                $s->nis,
                $s->nama,
                $s->kelas->nama_kelas ?? '-',
                $s->no_hp_ortu ?? '',
                $s->is_active ? 'Aktif' : 'Nonaktif',
                $s->created_at->format('d/m/Y'),
            ];
        }

        // Generate Excel manual (tanpa class export terpisah)
        $filename = 'data-siswa-' . now()->format('Ymd_His') . '.csv';
        $handle   = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
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
}
