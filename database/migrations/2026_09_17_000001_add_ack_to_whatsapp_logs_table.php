<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            // ID pesan dari Baileys (key.id) — untuk mapping ACK webhook
            $table->string('message_id', 100)->nullable()->after('gateway');

            // Status ACK: 1=terkirim server, 2=diterima HP, 3=dibaca
            // null = belum diketahui, -1 = error
            $table->tinyInteger('ack_status')->nullable()->after('message_id');

            // Index untuk lookup cepat saat ACK webhook masuk
            $table->index('message_id');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->dropIndex(['message_id']);
            $table->dropColumn(['message_id', 'ack_status']);
        });
    }
};