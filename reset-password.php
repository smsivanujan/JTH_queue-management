<?php

/**
 * Reset password for a user
 * Usage: php reset-password.php admin@hospital.com newpassword123
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = $argv[1] ?? 'admin@hospital.com';
$newPassword = $argv[2] ?? 'admin123';

$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found: {$email}\n";
    echo "Create user first or use correct email.\n";
    exit(1);
}

$user->password = Hash::make($newPassword);
$user->save();

echo "✅ Password reset successful!\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "LOGIN CREDENTIALS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Email: {$email}\n";
echo "Password: {$newPassword}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\nVisit: http://localhost:8000/login\n";

