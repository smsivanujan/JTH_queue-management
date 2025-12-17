<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $plans = [
            [
                'name' => 'Trial',
                'slug' => 'trial',
                'description' => '14-day free trial to explore all features',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'max_clinics' => 3,
                'max_users' => 2,
                'max_screens' => 1,
                'features' => json_encode(['basic_queue_management']),
                'trial_days' => 14,
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Perfect for small clinics',
                'price' => 29.00,
                'billing_cycle' => 'monthly',
                'max_clinics' => 10,
                'max_users' => 5,
                'max_screens' => 2,
                'features' => json_encode(['basic_queue_management', 'real_time_updates']),
                'trial_days' => 0,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for medium hospitals',
                'price' => 99.00,
                'billing_cycle' => 'monthly',
                'max_clinics' => 50,
                'max_users' => 20,
                'max_screens' => 5,
                'features' => json_encode(['basic_queue_management', 'real_time_updates', 'analytics', 'custom_branding']),
                'trial_days' => 0,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large hospital networks',
                'price' => 0, // Custom pricing
                'billing_cycle' => 'monthly',
                'max_clinics' => -1, // Unlimited
                'max_users' => -1, // Unlimited
                'max_screens' => -1, // Unlimited
                'features' => json_encode(['basic_queue_management', 'real_time_updates', 'analytics', 'custom_branding', 'api_access', 'priority_support']),
                'trial_days' => 0,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('plans')->insert($plans);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('plans')->whereIn('slug', ['trial', 'basic', 'professional', 'enterprise'])->delete();
    }
};

