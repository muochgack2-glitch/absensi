<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trusted_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_token', 64)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_name')->nullable();       // e.g. "Komputer Gerbang Utama"
            $table->string('browser_info')->nullable();       // User-Agent singkat
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('device_token');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trusted_devices');
    }
};

