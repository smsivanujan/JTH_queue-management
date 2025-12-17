<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates screen_usage_logs table to track second screen usage analytics
     * Write-only analytics - does not affect live screen behavior
     */
    public function up(): void
    {
        Schema::create('screen_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('clinic_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('screen_type', 20); // 'queue' or 'opd_lab'
            $table->string('screen_token', 64); // Reference to active_screens.token (not foreign key for flexibility)
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->nullable(); // Calculated when ended_at is set
            $table->timestamps();
            
            // Indexes for analytics queries
            $table->index(['tenant_id', 'started_at']);
            $table->index(['tenant_id', 'screen_type', 'started_at']);
            $table->index('screen_token'); // For linking to active screens
            $table->index(['ended_at', 'started_at']); // For cleanup queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screen_usage_logs');
    }
};
