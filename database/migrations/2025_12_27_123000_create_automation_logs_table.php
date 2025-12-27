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
        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'trial_reminder', 'inactivity_nudge', 'payment_success', 'payment_failure'
            $table->string('subtype')->nullable(); // For trial reminders: '7_days', '3_days', 'expired'
            $table->timestamp('sent_at')->useCurrent();
            $table->json('metadata')->nullable(); // Additional context
            $table->timestamps();

            // Indexes for efficient queries
            $table->index(['tenant_id', 'type', 'sent_at']);
            $table->index(['type', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_logs');
    }
};

