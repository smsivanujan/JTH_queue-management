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
        Schema::create('service_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('label'); // e.g., "Urine Test", "Customer Service", "Order #1"
            $table->string('color')->default('blue'); // Color for display (white, red, green, blue, etc.)
            $table->json('translations')->nullable(); // Multi-language support: {en: "Urine Test", ta: "சிறுநீர் பரிசோதனை", si: "..."}
            $table->integer('sort_order')->default(0); // Order for display
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['service_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_labels');
    }
};
