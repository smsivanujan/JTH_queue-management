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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Basic', 'Professional', 'Enterprise'
            $table->string('slug')->unique(); // e.g., 'basic', 'professional', 'enterprise'
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0); // Monthly price
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            $table->integer('max_clinics')->default(10); // -1 for unlimited
            $table->integer('max_users')->default(5); // -1 for unlimited
            $table->json('features')->nullable(); // Available features
            $table->integer('trial_days')->default(0); // Trial period in days
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // For display ordering
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

