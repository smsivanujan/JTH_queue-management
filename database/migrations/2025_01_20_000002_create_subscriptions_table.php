<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('plan_name'); // e.g., 'basic', 'professional', 'enterprise'
            $table->string('status')->default('active'); // active, cancelled, expired, trial
            $table->integer('max_clinics')->default(10); // Maximum clinics allowed
            $table->integer('max_users')->default(5); // Maximum users allowed
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('features')->nullable(); // Plan features as JSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

