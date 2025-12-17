<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
                'features' => ['basic_queue_management'],
                'trial_days' => 14,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Perfect for small clinics',
                'price' => 29.00,
                'billing_cycle' => 'monthly',
                'max_clinics' => 10,
                'max_users' => 5,
                'features' => ['basic_queue_management', 'real_time_updates'],
                'trial_days' => 0,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for medium hospitals',
                'price' => 99.00,
                'billing_cycle' => 'monthly',
                'max_clinics' => 50,
                'max_users' => 20,
                'features' => ['basic_queue_management', 'real_time_updates', 'analytics', 'custom_branding'],
                'trial_days' => 0,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large hospital networks',
                'price' => 0, // Custom pricing
                'billing_cycle' => 'monthly',
                'max_clinics' => -1, // Unlimited
                'max_users' => -1, // Unlimited
                'features' => ['basic_queue_management', 'real_time_updates', 'analytics', 'custom_branding', 'api_access', 'priority_support'],
                'trial_days' => 0,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }
}

