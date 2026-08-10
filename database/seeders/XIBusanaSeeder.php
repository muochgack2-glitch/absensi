<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceClass;
use App\Models\AttendanceStudent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\AttendanceSetting;

class XIBusanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active tahun ajaran
        $activeTahunAjaran = AttendanceSetting::get('active_tahun_ajaran', '2024/2025');

        // 1. Create Wali Kelas
        $waliKelas = User::firstOrCreate(
            ['email' => 'debby.fury@smkn1.sch.id'],
            [
                'name' => 'Debby Fury Wijayanti, S.Pd',
                'password' => Hash::make('password123'),
                'role' => 'wali_kelas',
            ]
        );

        $this->command->info('✅ Wali Kelas created: ' . $waliKelas->name);

        // 2. Create Class XI Busana
        $kelas = AttendanceClass::firstOrCreate(
            ['nama_kelas' => 'XI Busana'],
            [
                'tingkat' => 'XI',
                'jurusan' => 'Busana',
                'wali_kelas_id' => $waliKelas->id,
                'is_active' => true,
            ]
        );

        // Update wali_kelas_id jika kelas sudah ada
        if ($kelas->wali_kelas_id !== $waliKelas->id) {
            $kelas->update(['wali_kelas_id' => $waliKelas->id]);
        }

        // Update kelas_id di user wali kelas
        $waliKelas->update(['kelas_id' => $kelas->id]);

        $this->command->info('✅ Kelas created: ' . $kelas->nama_kelas);

        // 3. Create Students
        $students = [
            ['nis' => '1986', 'nama' => 'DIAN TIKA ARINI'],
            ['nis' => '1987', 'nama' => 'EVINA DWI JULIASARI'],
            ['nis' => '1988', 'nama' => 'FENI LISTIANTI'],
            ['nis' => '1989', 'nama' => 'ICHA NOVA RIYANTI'],
            ['nis' => '2008', 'nama' => 'INTAN PUTRI WIDYASTUTI'],
            ['nis' => '1990', 'nama' => 'MELINDA AZZAHWA'],
            ['nis' => '1991', 'nama' => 'NATHANIA ZULFA DAMAYANTI'],
            ['nis' => '1992', 'nama' => 'NAZUA MAULIDA ANATASYA'],
            ['nis' => '1994', 'nama' => 'NURUL RISMAWATI'],
            ['nis' => '1995', 'nama' => 'RESTIANA MEISYAROH'],
            ['nis' => '1997', 'nama' => 'SABRINA YUNIAR PERMATA RAMADHANI'],
            ['nis' => '1998', 'nama' => 'SITI FAIQQOTUL HIMMAH'],
            ['nis' => '1999', 'nama' => 'SITI NUR AFIDAH'],
            ['nis' => '2000', 'nama' => 'TIRTA ATMA NIRMAYA'],
            ['nis' => '2001', 'nama' => 'VERI NOFIANA'],
        ];

        $nomorHP = '085216343400'; // Nomor HP sama untuk semua siswa

        foreach ($students as $index => $studentData) {
            $student = AttendanceStudent::withoutGlobalScope('tahun_ajaran')->firstOrCreate(
                ['nis' => $studentData['nis']],
                [
                    'nama' => $studentData['nama'],
                    'kelas_id' => $kelas->id,
                    'no_hp_ortu' => $nomorHP,
                    'is_active' => true,
                    'tahun_ajaran' => $activeTahunAjaran,
                ]
            );

            // Update data jika siswa sudah ada
            if ($student->wasRecentlyCreated === false) {
                $student->update([
                    'nama' => $studentData['nama'],
                    'kelas_id' => $kelas->id,
                    'no_hp_ortu' => $nomorHP,
                    'is_active' => true,
                    'tahun_ajaran' => $activeTahunAjaran,
                ]);
            }

            // Generate QR Code
            $this->generateQRCode($student);

            $this->command->info('  ✓ Siswa #' . ($index + 1) . ': ' . $student->nama . ' (NIS: ' . $student->nis . ')');
        }

        $this->command->info('');
        $this->command->info('🎉 Seeder completed successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Wali Kelas: ' . $waliKelas->name);
        $this->command->info('   - Email: ' . $waliKelas->email);
        $this->command->info('   - Password: password123');
        $this->command->info('   - Kelas: ' . $kelas->nama_kelas);
        $this->command->info('   - Total Siswa: ' . count($students));
        $this->command->info('   - Nomor HP Ortu: ' . $nomorHP);
        $this->command->info('   - Tahun Ajaran: ' . $activeTahunAjaran);
    }

    /**
     * Generate QR Code for student
     */
    private function generateQRCode(AttendanceStudent $student): void
    {
        // Skip if QR code already exists
        if ($student->qr_code_path && Storage::disk('public')->exists($student->qr_code_path)) {
            return;
        }

        try {
            // QR Code content = NIS siswa
            $qrContent = $student->nis;

            // Generate QR Code image using SVG (doesn't need imagick)
            $qrCode = QrCode::format('svg')
                ->size(400)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($qrContent);

            // Save QR Code to storage
            $filename = 'qr_codes/' . $student->nis . '.svg';
            Storage::disk('public')->put($filename, $qrCode);

            // Update student record with QR code path
            $student->update([
                'qr_code_path' => $filename,
            ]);
        } catch (\Exception $e) {
            $this->command->warn('    ⚠️  Failed to generate QR Code: ' . $e->getMessage());
            $this->command->warn('    You can generate QR codes later from the admin panel.');
        }
    }
}
