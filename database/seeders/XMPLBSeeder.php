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

class XMPLBSeeder extends Seeder
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
            ['email' => 'pancawati.puji@smkn1.sch.id'],
            [
                'name' => 'Pancawati Puji Lestari, A.Md',
                'password' => Hash::make('password123'),
                'role' => 'wali_kelas',
            ]
        );

        $this->command->info('✅ Wali Kelas created: ' . $waliKelas->name);

        // 2. Create Class X MPLB
        $kelas = AttendanceClass::firstOrCreate(
            ['nama_kelas' => 'X MPLB'],
            [
                'tingkat' => 'X',
                'jurusan' => 'MPLB',
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

        // 3. Create Students (29 siswa)
        $students = [
            ['nis' => '2036', 'nama' => 'ALISIA CITTA GOTAMI'],
            ['nis' => '2037', 'nama' => 'ALYSA KHANZA SAFIRA'],
            ['nis' => '2038', 'nama' => 'APPRILLAN TIKA NOFITA PUTRI MAHARANI'],
            ['nis' => '2039', 'nama' => 'APRELIA IVA LATIVA'],
            ['nis' => '2040', 'nama' => 'APRILIA CANDU PAMUNGKAS'],
            ['nis' => '2041', 'nama' => 'AULIA BAROKHATU NIKMA'],
            ['nis' => '2042', 'nama' => 'DENIS VIKY APRILIA'],
            ['nis' => '2043', 'nama' => 'DHINA CANTIK NURAINI'],
            ['nis' => '2044', 'nama' => 'EVA SAPUTRI'],
            ['nis' => '2045', 'nama' => 'KEZIA ANASTASIA RIYANTO'],
            ['nis' => '2046', 'nama' => 'LAILA SELVI'],
            ['nis' => '2047', 'nama' => 'LATISYA PUTRI KIRANA'],
            ['nis' => '2048', 'nama' => 'LIVIA SANTIKA'],
            ['nis' => '2049', 'nama' => 'LUTHFI NADIFA RAMADHANI'],
            ['nis' => '2050', 'nama' => 'MARSYANTI'],
            ['nis' => '2051', 'nama' => 'MESA AYU SAHARANI PUTRI'],
            ['nis' => '2052', 'nama' => 'NABILA RAHMA TANIA'],
            ['nis' => '2053', 'nama' => 'NAYLA PUTRI APRILIA'],
            ['nis' => '2054', 'nama' => 'NAZWA NUR RAMADHANY'],
            ['nis' => '2055', 'nama' => 'NOYA SILYA NUR AZIZAH'],
            ['nis' => '2056', 'nama' => 'ONE FRANSISCA PUTRY'],
            ['nis' => '2057', 'nama' => 'RAFA RIZQI FEBRIAN'],
            ['nis' => '2058', 'nama' => 'SOFIA DWI AMELIA'],
            ['nis' => '2059', 'nama' => 'SUPARMIATI'],
            ['nis' => '2060', 'nama' => 'SYAFA UMI LATIFAH'],
            ['nis' => '2061', 'nama' => 'SYARINA JUNIFA QURRATUN AINI'],
            ['nis' => '2062', 'nama' => 'ULYA ZAHROTUN NAFISAH'],
            ['nis' => '2063', 'nama' => 'WIDIYA'],
            ['nis' => '2064', 'nama' => 'YOSEFA AVELINA PUTRI SETIAWATI'],
        ];
        $nomorHP = '081228745812'; // No HP Ortu X MPLB

        foreach ($students as $index => $studentData) {
            $student = AttendanceStudent::withoutGlobalScope('tahun_ajaran')->firstOrCreate(
                ['nis' => $studentData['nis']],
                [
                    'nama' => $studentData['nama'],
                    'kelas_id' => $kelas->id,
                    'no_hp_ortu' => $nomorHP,
                    'no_hp_ortu2' => '628985411895',
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
                    'no_hp_ortu2' => '628985411895',
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
        $this->command->info('   - Nomor HP Ortu: ' . '08[NIS]');
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
