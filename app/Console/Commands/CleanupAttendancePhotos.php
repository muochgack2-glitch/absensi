<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupAttendancePhotos extends Command
{
    protected $signature   = 'attendance:cleanup-photos
                                {--days=90 : Hapus foto lebih tua dari N hari (default: 90)}
                                {--dry-run : Simulasi saja, tidak benar-benar hapus}';

    protected $description = 'Hapus foto absensi lama untuk menghemat disk space';

    public function handle(): int
    {
        $days   = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoff = Carbon::now()->subDays($days);

        $this->info("Cleanup foto absensi lebih tua dari {$days} hari (sebelum {$cutoff->toDateString()})");
        if ($dryRun) {
            $this->warn('   [DRY RUN] Tidak ada yang dihapus.');
        }

        $basePath     = 'attendance/photos';
        $totalDeleted = 0;
        $totalBytes   = 0;

        $nisFolders = Storage::directories($basePath);
        foreach ($nisFolders as $nisFolder) {
            $dateFolders = Storage::directories($nisFolder);
            foreach ($dateFolders as $dateFolder) {
                $datePart = basename($dateFolder);
                try {
                    $folderDate = Carbon::createFromFormat('Y-m-d', $datePart);
                } catch (\Exception $e) {
                    continue;
                }

                if ($folderDate->lt($cutoff)) {
                    $files = Storage::files($dateFolder);
                    foreach ($files as $file) {
                        $totalBytes += Storage::size($file);
                        $totalDeleted++;
                    }

                    if (!$dryRun) {
                        Storage::deleteDirectory($dateFolder);
                    }

                    $this->line("   " . ($dryRun ? '[DRY] ' : 'OK ') . "Hapus: {$dateFolder} ({$folderDate->toDateString()})");
                }
            }
        }

        $totalMB = round($totalBytes / 1024 / 1024, 2);
        $action  = $dryRun ? 'Akan dihapus' : 'Dihapus';
        $this->info("Selesai - {$action}: {$totalDeleted} foto ({$totalMB} MB)");

        return self::SUCCESS;
    }
}
