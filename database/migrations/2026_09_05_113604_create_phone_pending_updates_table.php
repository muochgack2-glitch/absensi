<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_pending_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->constrained('attendance_students')
                  ->onDelete('cascade');
            $table->string('no_hp_ortu',  20)->nullable();
            $table->string('no_hp_ortu2', 20)->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->boolean('is_applied')->default(false);
            $table->timestamp('applied_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_pending_updates');
    }
};
