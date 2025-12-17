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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "OPD Lab", "Customer Service", "Order Pickup"
            $table->string('type')->default('range'); // 'range' or 'sequential' - range uses start/end, sequential uses single numbers
            $table->string('password_hash')->nullable(); // Hashed password for service access
            $table->timestamp('password_migrated_at')->nullable(); // Track password migration
            $table->json('settings')->nullable(); // Store service-specific settings (colors, display options, etc.)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
