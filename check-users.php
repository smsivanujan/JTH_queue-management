<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "EXISTING USERS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$users = User::all(['id', 'name', 'email', 'current_tenant_id']);

if ($users->count() > 0) {
    foreach ($users as $user) {
        echo "Email: {$user->email}\n";
        echo "Name: {$user->name}\n";
        echo "Has Tenant: " . ($user->current_tenant_id ? 'Yes' : 'No') . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
    echo "⚠️  Note: Passwords are hashed and cannot be retrieved.\n";
    echo "If you don't know the password, you can:\n";
    echo "1. Register a new account at /register\n";
    echo "2. Reset password using the script below\n\n";
} else {
    echo "No users found. Create one using:\n";
    echo "php create-admin-user.php\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "DEFAULT LOGIN CREDENTIALS (if created):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Email: admin@hospital.com\n";
echo "Password: admin123\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

