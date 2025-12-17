<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates a default tenant and assigns all existing data to it.
     */
    public function up(): void
    {
        // Only run if there's existing data and no tenants yet
        if (DB::table('tenants')->count() > 0) {
            return; // Tenants already exist, skip
        }

        // Check if we have existing data to migrate
        $hasClinics = Schema::hasTable('clinics') && DB::table('clinics')->count() > 0;
        $hasQueues = Schema::hasTable('queues') && DB::table('queues')->count() > 0;
        
        if (!$hasClinics && !$hasQueues) {
            return; // No existing data to migrate
        }

        // Create default tenant
        $tenantId = DB::table('tenants')->insertGetId([
            'name' => 'Default Hospital',
            'slug' => 'default-hospital',
            'email' => 'admin@example.com',
            'is_active' => true,
            'trial_ends_at' => now()->addDays(14),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create default subscription
        DB::table('subscriptions')->insert([
            'tenant_id' => $tenantId,
            'plan_name' => 'professional',
            'status' => 'active',
            'max_clinics' => 50,
            'max_users' => 20,
            'starts_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign existing clinics to tenant
        if (Schema::hasTable('clinics')) {
            DB::table('clinics')
                ->whereNull('tenant_id')
                ->update(['tenant_id' => $tenantId]);
        }

        // Assign existing queues to tenant
        if (Schema::hasTable('queues')) {
            DB::table('queues')
                ->whereNull('tenant_id')
                ->update(['tenant_id' => $tenantId]);
        }

        // Assign existing sub_queues to tenant
        if (Schema::hasTable('sub_queues')) {
            DB::table('sub_queues')
                ->whereNull('tenant_id')
                ->update(['tenant_id' => $tenantId]);
        }

        // Link existing users to tenant
        if (Schema::hasTable('users')) {
            $users = DB::table('users')->get();
            
            foreach ($users as $user) {
                // Set current_tenant_id
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['current_tenant_id' => $tenantId]);

                // Create tenant_user relationship
                DB::table('tenant_users')->insertOrIgnore([
                    'tenant_id' => $tenantId,
                    'user_id' => $user->id,
                    'role' => 'owner',
                    'is_active' => true,
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Find default tenant
        $tenant = DB::table('tenants')->where('slug', 'default-hospital')->first();
        
        if ($tenant) {
            // Remove tenant_id from all records
            if (Schema::hasTable('clinics')) {
                DB::table('clinics')
                    ->where('tenant_id', $tenant->id)
                    ->update(['tenant_id' => null]);
            }

            if (Schema::hasTable('queues')) {
                DB::table('queues')
                    ->where('tenant_id', $tenant->id)
                    ->update(['tenant_id' => null]);
            }

            if (Schema::hasTable('sub_queues')) {
                DB::table('sub_queues')
                    ->where('tenant_id', $tenant->id)
                    ->update(['tenant_id' => null]);
            }

            if (Schema::hasTable('users')) {
                DB::table('users')
                    ->where('current_tenant_id', $tenant->id)
                    ->update(['current_tenant_id' => null]);
            }

            // Delete tenant relationships
            DB::table('tenant_users')->where('tenant_id', $tenant->id)->delete();
            
            // Delete subscription
            DB::table('subscriptions')->where('tenant_id', $tenant->id)->delete();
            
            // Delete tenant
            DB::table('tenants')->where('id', $tenant->id)->delete();
        }
    }
};

