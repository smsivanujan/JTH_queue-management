<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds max_screens field to subscriptions table
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('max_screens')->default(1)->after('max_users'); // -1 for unlimited
        });
        
        // Populate existing subscriptions with default screen limit from their plans
        // Only if plans table has the max_screens column
        if (Schema::hasColumn('plans', 'max_screens')) {
            DB::statement('
                UPDATE subscriptions s
                INNER JOIN plans p ON s.plan_id = p.id
                SET s.max_screens = COALESCE(p.max_screens, 1)
                WHERE s.plan_id IS NOT NULL
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('max_screens');
        });
    }
};

