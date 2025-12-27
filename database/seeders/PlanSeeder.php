<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Pricing Plans:
     * - Trial: 14-day free trial
     * - Starter: $29/month or $290/year (small businesses)
     * - Pro: $99/month or $990/year (medium businesses)
     * - Enterprise: Custom pricing (large businesses, manual payment only)
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
                'max_screens' => 1,
                'features' => [
                    'basic_queue_management',
                    'real_time_updates',
                ],
                'trial_days' => 14,
                'is_active' => true,
                'sort_order' => 0,
                'stripe_price_id' => null, // No Stripe for trial
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for small businesses and single locations',
                'price' => 29.00,
                'billing_cycle' => 'monthly',
                'max_clinics' => 5,
                'max_users' => 5,
                'max_screens' => 2,
                'features' => [
                    'basic_queue_management',
                    'real_time_updates',
                    'multi_service_support',
                    'basic_analytics',
                ],
                'trial_days' => 0,
                'is_active' => true,
                'sort_order' => 1,
                'stripe_price_id' => null, // Set this in Stripe Dashboard, then update: 'price_xxxxx_monthly'
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Ideal for growing businesses with multiple locations',
                'price' => 99.00,
                'billing_cycle' => 'monthly',
                'max_clinics' => 25,
                'max_users' => 15,
                'max_screens' => 10,
                'features' => [
                    'basic_queue_management',
                    'real_time_updates',
                    'multi_service_support',
                    'advanced_analytics',
                    'custom_branding',
                    'priority_support',
                ],
                'trial_days' => 0,
                'is_active' => true,
                'sort_order' => 2,
                'stripe_price_id' => null, // Set this in Stripe Dashboard, then update: 'price_xxxxx_monthly'
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large organizations with unlimited needs',
                'price' => 0, // Custom pricing - manual payment only
                'billing_cycle' => 'monthly',
                'max_clinics' => -1, // Unlimited
                'max_users' => -1, // Unlimited
                'max_screens' => -1, // Unlimited
                'features' => [
                    'basic_queue_management',
                    'real_time_updates',
                    'multi_service_support',
                    'advanced_analytics',
                    'custom_branding',
                    'api_access',
                    'white_label_support',
                    'dedicated_account_manager',
                    'priority_support',
                    'custom_integrations',
                ],
                'trial_days' => 0,
                'is_active' => true,
                'sort_order' => 3,
                'stripe_price_id' => null, // Manual payment only - no Stripe
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }

        // Note: After seeding, you need to:
        // 1. Create Products and Prices in Stripe Dashboard
        // 2. Update stripe_price_id for Starter and Pro plans:
        //    - Starter monthly: UPDATE plans SET stripe_price_id = 'price_xxxxx' WHERE slug = 'starter';
        //    - Pro monthly: UPDATE plans SET stripe_price_id = 'price_xxxxx' WHERE slug = 'pro';
        // 3. For yearly billing, create separate plans or use Stripe's recurring intervals
    }
}
