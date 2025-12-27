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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Stripe subscription and customer IDs
            $table->string('stripe_subscription_id')->nullable()->after('plan_id');
            $table->string('stripe_customer_id')->nullable()->after('stripe_subscription_id');
            $table->string('payment_method')->nullable()->after('stripe_customer_id')->comment('manual, stripe');
            
            // Indexes for faster lookups
            $table->index('stripe_subscription_id');
            $table->index('stripe_customer_id');
        });

        // Add stripe_price_id to plans table
        Schema::table('plans', function (Blueprint $table) {
            $table->string('stripe_price_id')->nullable()->after('price')->comment('Stripe Price ID for this plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['stripe_subscription_id']);
            $table->dropIndex(['stripe_customer_id']);
            $table->dropColumn(['stripe_subscription_id', 'stripe_customer_id', 'payment_method']);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('stripe_price_id');
        });
    }
};
