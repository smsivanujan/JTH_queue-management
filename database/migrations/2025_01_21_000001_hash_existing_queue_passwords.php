<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration hashes all existing plain text queue passwords.
     * Note: This is a one-way operation. Existing plain text passwords will be hashed.
     */
    public function up(): void
    {
        // Get all queues with non-empty passwords
        $queues = DB::table('queues')
            ->whereNotNull('password')
            ->where('password', '!=', '')
            ->get();

        foreach ($queues as $queue) {
            // Check if password is already hashed (starts with $2y$ for bcrypt)
            if (!str_starts_with($queue->password, '$2y$')) {
                // Hash the plain text password
                $hashedPassword = Hash::make($queue->password);
                
                DB::table('queues')
                    ->where('id', $queue->id)
                    ->update(['password' => $hashedPassword]);
            }
        }
    }

    /**
     * Reverse the migrations.
     * 
     * WARNING: This cannot be reversed as we cannot un-hash passwords.
     */
    public function down(): void
    {
        // Cannot reverse password hashing
        // This migration is irreversible
    }
};

