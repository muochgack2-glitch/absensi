<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ganti footer hardcode di semua template WA menjadi variabel {footer}.
     *
     * Sebelum: "_Pesan otomatis dari sistem absensi_"
     * Sesudah: "{footer}"
     *
     * {footer} di-inject otomatis oleh WhatsAppTemplate::parse()
     * dengan variasi berbeda per siswa berdasarkan crc32(NIS).
     */
    public function up(): void
    {
        // 1. Ganti footer hardcode -> {footer} di kolom message semua template
        DB::table('whatsapp_templates')
            ->update([
                'message'    => DB::raw("REPLACE(message, '_Pesan otomatis dari sistem absensi_', '{footer}')"),
                'updated_at' => now(),
            ]);

        // 2. Tambahkan 'footer' ke array variables di setiap template jika belum ada
        DB::table('whatsapp_templates')->get()->each(function ($template) {
            $vars = json_decode($template->variables, true) ?? [];
            if (! in_array('footer', $vars, true)) {
                $vars[] = 'footer';
                DB::table('whatsapp_templates')
                    ->where('id', $template->id)
                    ->update([
                        'variables'  => json_encode($vars),
                        'updated_at' => now(),
                    ]);
            }
        });
    }

    /**
     * Kembalikan {footer} ke teks hardcode semula.
     */
    public function down(): void
    {
        DB::table('whatsapp_templates')
            ->update([
                'message'    => DB::raw("REPLACE(message, '{footer}', '_Pesan otomatis dari sistem absensi_')"),
                'updated_at' => now(),
            ]);

        DB::table('whatsapp_templates')->get()->each(function ($template) {
            $vars = json_decode($template->variables, true) ?? [];
            $vars = array_values(array_filter($vars, fn($v) => $v !== 'footer'));
            DB::table('whatsapp_templates')
                ->where('id', $template->id)
                ->update([
                    'variables'  => json_encode($vars),
                    'updated_at' => now(),
                ]);
        });
    }
};
