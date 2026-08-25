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

class XIMPLBSeeder extends Seeder
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
            ['email' => 'ade.rua@smkn1.sch.id'],
            [
                'name' => 'Ade Rua Nur Lemoniar, S.Pd',
                'password' => Hash::make('password123'),
                'role' => 'wali_kelas',
            ]
        );

        $this->command->info('✅ Wali Kelas created: ' . $waliKelas->name);

        // 2. Create Class XI MPLB
        $kelas = AttendanceClass::firstOrCreate(
            ['nama_kelas' => 'XI MPLB'],
            [
                'tingkat' => 'XI',
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

        // 3. Create Students
        $students = [
            ['nis' => '1956', 'nama' => 'AGUSTINA AJENG RAMADHANI'],
            ['nis' => '1957', 'nama' => 'ARTIKA NUR ROHMAH'],
            ['nis' => '1959', 'nama' => 'BUNGA SARI RAHMANDANI'],
            ['nis' => '1960', 'nama' => 'CHELIZA DEWI SAPUTRI'],
            ['nis' => '1961', 'nama' => 'CITRA KHARISA'],
            ['nis' => '1962', 'nama' => 'DESIANE SAPUTRI'],
            ['nis' => '1963', 'nama' => 'DINDA WULAN SAFITRI'],
            ['nis' => '1964', 'nama' => 'DINI YULIANA'],
            ['nis' => '1965', 'nama' => 'ERLIANA AZIZAS MITA'],
            ['nis' => '1966', 'nama' => 'FINDY FARELLA'],
            ['nis' => '1967', 'nama' => 'INTAN SEKAR WULANDARI'],
            ['nis' => '1968', 'nama' => 'INTAN TAKSA'],
            ['nis' => '1969', 'nama' => 'LEVIANA PUTRI AUDI ASTUTI'],
            ['nis' => '2009', 'nama' => 'JIHAN JAUHAROTUN NISA\''],
            ['nis' => '1970', 'nama' => 'LILIS'],
            ['nis' => '1971', 'nama' => 'MANDA SETIA KINASIH'],
            ['nis' => '1972', 'nama' => 'NAISILA ZAIMATUL RIZKIYAH'],
            ['nis' => '1973', 'nama' => 'NAURA AZALIA'],
            ['nis' => '2010', 'nama' => 'NUR CAHYA ISWANTI'],
            ['nis' => '1974', 'nama' => 'OKTAVIA DWI ANDRIANI'],
            ['nis' => '1975', 'nama' => 'OLIVIA SEPTIAN PERMATA HERMANSYAH'],
            ['nis' => '1976', 'nama' => 'RATNA DWI MEILYA SALSABILA'],
            ['nis' => '1978', 'nama' => 'REZKY OKTVIANA PUTRI'],
            ['nis' => '1979', 'nama' => 'RINDU ARDELIA CANDRANINGTIYAS'],
            ['nis' => '1980', 'nama' => 'RIYANA'],
            ['nis' => '1981', 'nama' => 'SAFIRA CAHYA INDRIYANI'],
            ['nis' => '1982', 'nama' => 'SEPTIANA NADIA RAMADHA'],
            ['nis' => '1984', 'nama' => 'SYIFA FAUZIYAH'],
            ['nis' => '1985', 'nama' => 'VIDYANA VEGA AULIA NURANI PUTRI'],
        ];
        $nomorHP = '082332003323'; // No HP Ortu XI MPLB

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
