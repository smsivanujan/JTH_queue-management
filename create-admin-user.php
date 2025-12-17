<?php

/**
 * Quick script to create an admin user
 * Run: php create-admin-user.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Support\Facades\Hash;

echo "Creating admin user...\n\n";

// Create admin user
$user = User::firstOrCreate(
    ['email' => 'admin@hospital.com'],
    [
        'name' => 'Hospital Admin',
        'password' => Hash::make('admin123')
    ]
);

echo "✅ User created/updated!\n";
echo "   Email: admin@hospital.com\n";
echo "   Password: admin123\n\n";

// Create tenant if doesn't exist
if (!$user->current_tenant_id) {
    $tenantService = app(TenantService::class);
    
    $tenant = $tenantService->createTenant([
        'name' => 'Teaching Hospital Jaffna',
        'email' => 'admin@hospital.com',
    ], $user, 'professional');
    
    echo "✅ Tenant created!\n";
    echo "   Name: {$tenant->name}\n";
    echo "   Slug: {$tenant->slug}\n\n";
} else {
    $tenant = Tenant::find($user->current_tenant_id);
    echo "✅ Tenant already exists: {$tenant->name}\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "LOGIN CREDENTIALS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Email: admin@hospital.com\n";
echo "Password: admin123\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\nVisit: http://localhost:8000/login\n";

