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

class XBusanaSeeder extends Seeder
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
            ['email' => 'marista.bela@smkn1.sch.id'],
            [
                'name' => 'Marista Bela Octaviana, S.Pd',
                'password' => Hash::make('password123'),
                'role' => 'wali_kelas',
            ]
        );

        $this->command->info('✅ Wali Kelas created: ' . $waliKelas->name);

        // 2. Create Class X Busana
        $kelas = AttendanceClass::firstOrCreate(
            ['nama_kelas' => 'X Busana'],
            [
                'tingkat' => 'X',
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

        // 3. Create Students (13 siswa)
        $students = [
            ['nis' => '2011', 'nama' => 'ADELIA MAYLATHULHUSNA UJIYANI'],
            ['nis' => '2012', 'nama' => 'ASIH MAHARANI'],
            ['nis' => '2013', 'nama' => 'AURELIA MARETA A'],
            ['nis' => '2014', 'nama' => 'ELFA DAMIYANTI'],
            ['nis' => '2015', 'nama' => 'FATIMAH AZZAHRA'],
            ['nis' => '2016', 'nama' => 'JASYINTA PUTRI NIKA'],
            ['nis' => '2017', 'nama' => 'MUHAMMAD RAMADHAN'],
            ['nis' => '2018', 'nama' => 'NADIA HASNA RAHMATUL LAILI'],
            ['nis' => '2019', 'nama' => 'NADIA NUR\'AINI AULIA'],
            ['nis' => '2020', 'nama' => 'OKTAVIA ANGGRAINI'],
            ['nis' => '2021', 'nama' => 'RIZKA ARIFATUN NISA'],
            ['nis' => '2022', 'nama' => 'SHAFA NIA RAMADHANI'],
            ['nis' => '2023', 'nama' => 'SIVA LIANA SARI'],
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
