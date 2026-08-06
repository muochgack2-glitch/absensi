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
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20);
            $table->string('phone_normalized', 20)->nullable()->index();
            $table->text('message');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending')->index();
            $table->string('type', 50)->default('manual')->index();
            // check_in, check_out, absent, manual, broadcast, diagnostic_test
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->string('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('student_id')
                ->references('id')
                ->on('attendance_students')
                ->onDelete('set null');

            $table->foreign('template_id')
                ->references('id')
                ->on('whatsapp_templates')
                ->onDelete('set null');

            $table->foreign('sent_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Indexes
            $table->index('created_at');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
