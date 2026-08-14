<?php

namespace App\Console\Commands;

use App\Services\EkaldikHolidayService;
use Illuminate\Console\Command;

class SyncHolidays extends Command
{
    protected $signature = 'holidays:sync';
    protected $description = 'Sync hari libur dari E-Kaldik (SIM Kurikulum)';

    public function handle(EkaldikHolidayService $service): int
    {
        $this->info('Syncing holidays from E-Kaldik...');

        $stats = $service->syncFromEkaldik();

        if (!empty($stats['errors'])) {
            foreach ($stats['errors'] as $error) {
                $this->error("  ✗ {$error}");
            }
            return Command::FAILURE;
        }

        $this->info("  ✓ Ditambahkan: {$stats['added']}");
        $this->info("  ✓ Diperbarui: {$stats['updated']}");
        $this->info("  ✓ Dihapus: {$stats['removed']}");
        $this->info('Sync selesai!');

        return Command::SUCCESS;
    }
}
