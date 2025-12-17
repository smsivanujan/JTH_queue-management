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
     * Fixes the foreign key constraint in sub_queues table to reference clinics.id
     * instead of queues.id, as clinic_id should reference the clinic, not the queue.
     */
    public function up(): void
    {
        // Drop the existing incorrect foreign key
        Schema::table('sub_queues', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
        });

        // Update the foreign key to reference clinics.id
        Schema::table('sub_queues', function (Blueprint $table) {
            $table->foreign('clinic_id')
                ->references('id')
                ->on('clinics')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the correct foreign key
        Schema::table('sub_queues', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
        });

        // Restore the old (incorrect) foreign key for backward compatibility
        Schema::table('sub_queues', function (Blueprint $table) {
            $table->foreign('clinic_id')
                ->references('id')
                ->on('queues')
                ->onDelete('cascade');
        });
    }
};

