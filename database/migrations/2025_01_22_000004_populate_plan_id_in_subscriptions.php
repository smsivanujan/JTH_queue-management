<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Populates plan_id in subscriptions based on plan_name
     */
    public function up(): void
    {
        // Map plan names to plan IDs
        $planMapping = DB::table('plans')
            ->pluck('id', 'slug')
            ->toArray();

        // Update subscriptions with plan_id based on plan_name
        $subscriptions = DB::table('subscriptions')->whereNull('plan_id')->get();
        
        foreach ($subscriptions as $subscription) {
            $planSlug = strtolower($subscription->plan_name);
            
            if (isset($planMapping[$planSlug])) {
                DB::table('subscriptions')
                    ->where('id', $subscription->id)
                    ->update(['plan_id' => $planMapping[$planSlug]]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('subscriptions')->update(['plan_id' => null]);
    }
};

