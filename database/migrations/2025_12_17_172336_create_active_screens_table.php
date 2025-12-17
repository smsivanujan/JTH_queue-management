<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates active_screens table to track second screens (queue and OPD Lab displays)
     * Replaces unreliable session-based tracking with database persistence
     */
    public function up(): void
    {
        Schema::create('active_screens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('clinic_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('screen_type', 20); // 'queue' or 'opd_lab'
            $table->string('screen_token', 64)->unique(); // Unique token for each screen session
            $table->timestamp('last_heartbeat_at')->nullable(); // Last heartbeat timestamp
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['tenant_id', 'screen_type']);
            $table->index('last_heartbeat_at'); // For cleaning up expired screens
            $table->index('screen_token'); // For heartbeat lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_screens');
    }
};
