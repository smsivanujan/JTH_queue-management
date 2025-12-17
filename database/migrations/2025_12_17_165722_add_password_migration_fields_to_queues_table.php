<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds password_hash and password_migrated_at fields to track password migration status.
     * The existing 'password' field is kept for backward compatibility.
     */
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->string('password_hash')->nullable()->after('password');
            $table->timestamp('password_migrated_at')->nullable()->after('password_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropColumn(['password_hash', 'password_migrated_at']);
        });
    }
};
