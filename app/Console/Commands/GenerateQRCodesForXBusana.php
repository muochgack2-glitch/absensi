<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceStudent;
use App\Models\AttendanceClass;
use Illuminate\Support\Facades\Storage;

class GenerateQRCodesForXBusana extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:generate-xbusana {--format=png : QR Code format (png or svg)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR Codes for X Busana students (NIS 2011-2023)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $format = $this->option('format');
        
        $this->info('🔍 Finding X Busana class...');
        
        $kelas = AttendanceClass::where('nama_kelas', 'X Busana')->first();
        
        if (!$kelas) {
            $this->error('❌ Kelas X Busana tidak ditemukan!');
            $this->info('💡 Jalankan dulu: php artisan db:seed --class=XBusanaSeeder');
            return 1;
        }

        $this->info('✅ Kelas ditemukan: ' . $kelas->nama_kelas);
        $this->info('');

        $students = AttendanceStudent::withoutGlobalScope('tahun_ajaran')
            ->where('kelas_id', $kelas->id)
            ->orderBy('nis')
            ->get();

        if ($students->isEmpty()) {
            $this->error('❌ Tidak ada siswa di kelas X Busana!');
            return 1;
        }

        $this->info('📊 Generating QR Codes for ' . $students->count() . ' students...');
        $this->info('');

        $bar = $this->output->createProgressBar($students->count());
        $bar->start();

        foreach ($students as $student) {
            $this->generateQRCodeAsImage($student, $format);
            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info('');
        $this->info('🎉 QR Codes generated successfully!');
        $this->info('📂 Location: storage/app/public/qr_codes/');
        $this->info('🌐 Public URL: ' . url('storage/qr_codes/'));
        $this->info('');
        $this->info('📋 Students:');
        
        foreach ($students as $student) {
            $this->info('  ✓ ' . $student->nis . ' - ' . $student->nama);
            $this->info('    ' . url('storage/' . $student->qr_code_path));
        }

        return 0;
    }

    /**
     * Generate QR Code as image file using GD (doesn't need imagick)
     */
    private function generateQRCodeAsImage(AttendanceStudent $student, string $format = 'png'): void
    {
        // Use existing command if available
        \Artisan::call('qr:generate', [
            '--student-id' => $student->id,
            '--format' => $format,
        ]);

        // Alternative: Manual generation using simple approach
        // For now, we'll use SVG which is already working
        if ($format === 'svg') {
            $qrContent = $student->nis;
            
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(400)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($qrContent);

            $filename = 'qr_codes/' . $student->nis . '.svg';
            Storage::disk('public')->put($filename, $qrCode);

            $student->update(['qr_code_path' => $filename]);
        }
    }
}

