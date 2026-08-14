<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('type', 100)->nullable();
            $table->enum('source', ['ekaldik', 'manual'])->default('manual');
            $table->unsignedBigInteger('ekaldik_activity_id')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['start_date', 'end_date']);
            $table->index('ekaldik_activity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
