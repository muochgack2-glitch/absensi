<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('tahun', 9)->unique()->comment('Format: 2026/2027');
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->date('started_at')->nullable();
            $table->date('closed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status', 'idx_ta_status');
        });

        // Seed tahun ajaran aktif saat ini
        \DB::table('tahun_ajaran')->insert([
            'tahun'      => '2026/2027',
            'status'     => 'active',
            'started_at' => '2026-07-01',
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simpan setting tahun ajaran aktif
        \DB::table('attendance_settings')->updateOrInsert(
            ['key' => 'active_tahun_ajaran'],
            [
                'value'      => '2026/2027',
                'group_name' => 'system',
                'description'=> 'Tahun ajaran yang sedang aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');

        \DB::table('attendance_settings')
            ->where('key', 'active_tahun_ajaran')
            ->delete();
    }
};
