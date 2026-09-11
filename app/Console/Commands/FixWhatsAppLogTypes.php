<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WhatsAppLog;

class FixWhatsAppLogTypes extends Command
{
    protected $signature   = 'whatsapp:fix-log-types {--dry-run : Preview perubahan tanpa menyimpan}';
    protected $description = 'Perbaiki kolom type pada whatsapp_logs yang salah tersimpan sebagai check_in';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('');
        $this->info('=== Fix WhatsApp Log Types ===');
        if ($dryRun) {
            $this->warn('Mode DRY-RUN — tidak ada yang disimpan');
        }
        $this->info('');

        // Kata kunci per tipe pesan
        $rules = [
            'check_out' => [
                'Notifikasi Pulang',
                'Pulang Normal',
                'Pulang Lebih Awal',
                'Pulang Lebih awal',
                'pulang_cepat',
                'jam pulang',
                'Jam Pulang',
                'check_out',
            ],
            'absent' => [
                'Notifikasi Alpha',
                'tidak hadir',
                'Tidak Hadir',
                'ALPHA',
                'Absen',
            ],
        ];

        $totalFixed = 0;

        foreach ($rules as $targetType => $keywords) {
            // Cari record dengan type = check_in yang isinya sesuai keyword
            $query = WhatsAppLog::where('type', 'check_in');

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('message', 'like', "%{$kw}%");
                }
            });

            $count = $query->count();

            if ($count === 0) {
                $this->line("  <info>✓</info> Tidak ada log salah untuk tipe <comment>{$targetType}</comment>");
                continue;
            }

            $this->line("  Ditemukan <comment>{$count}</comment> log yang akan diubah → <info>{$targetType}</info>");

            if ($this->getOutput()->isVerbose()) {
                $query->take(5)->get()->each(function ($log) use ($targetType) {
                    $this->line("    [{$log->id}] {$log->created_at->format('d/m H:i')} - " . substr($log->message, 0, 60) . '...');
                });
                if ($count > 5) {
                    $this->line("    ... dan " . ($count - 5) . " lainnya");
                }
            }

            if (!$dryRun) {
                $updated = WhatsAppLog::where('type', 'check_in')
                    ->where(function ($q) use ($keywords) {
                        foreach ($keywords as $kw) {
                            $q->orWhere('message', 'like', "%{$kw}%");
                        }
                    })
                    ->update(['type' => $targetType]);

                $this->info("  ✅ {$updated} record diupdate ke '{$targetType}'");
                $totalFixed += $updated;
            } else {
                $totalFixed += $count;
            }
        }

        $this->info('');
        if ($dryRun) {
            $this->warn("Total yang akan difix: {$totalFixed} record (gunakan tanpa --dry-run untuk menyimpan)");
        } else {
            $this->info("Total difix: {$totalFixed} record ✅");
        }
        $this->info('');

        return self::SUCCESS;
    }
}
