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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'churn_risk', 'payment_risk', 'system_health'
            $table->string('severity'); // 'low', 'medium', 'high', 'critical'
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade'); // null for system-wide alerts
            $table->string('title');
            $table->text('message');
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamps();

            // Indexes for efficient queries
            $table->index(['type', 'severity']);
            $table->index('tenant_id');
            $table->index('resolved_at');
            $table->index('last_triggered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
