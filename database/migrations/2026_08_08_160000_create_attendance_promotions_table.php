<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel riwayat naik kelas (adopsi dari E-Kaldik ClassPromotion).
     * Menyimpan detail perubahan per siswa agar bisa di-rollback.
     */
    public function up(): void
    {
        Schema::create('attendance_promotions', function (Blueprint $table) {
            $table->id();
            $table->string('from_tahun_ajaran', 9);
            $table->string('to_tahun_ajaran', 9);
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->integer('total_promoted')->default(0);
            $table->integer('total_graduated')->default(0);
            $table->json('promotion_summary')->nullable()->comment('Ringkasan per kelas');
            $table->json('student_details')->nullable()->comment('Detail perubahan per siswa untuk rollback');
            $table->text('notes')->nullable();
            $table->boolean('is_rolled_back')->default(false);
            $table->timestamp('rolled_back_at')->nullable();
            $table->unsignedBigInteger('rolled_back_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('from_tahun_ajaran');
            $table->index('to_tahun_ajaran');
            $table->index('is_rolled_back');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_promotions');
    }
};
