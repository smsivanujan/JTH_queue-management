<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantService;

$email = 'admin@hospital.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found: {$email}\n";
    exit(1);
}

// Check if user already has a tenant
if ($user->current_tenant_id) {
    $tenant = Tenant::find($user->current_tenant_id);
    echo "✅ User already has tenant: {$tenant->name}\n\n";
} else {
    // Check if tenant exists for this email
    $tenant = Tenant::where('email', $email)->first();
    
    if (!$tenant) {
        // Create tenant
        $tenantService = app(TenantService::class);
        $tenant = $tenantService->createTenant([
            'name' => 'Teaching Hospital Jaffna',
            'email' => $email,
        ], $user, 'professional');
        
        echo "✅ Tenant created!\n";
    } else {
        // Link user to existing tenant
        $user->update(['current_tenant_id' => $tenant->id]);
        
        // Ensure user is in tenant_users
        if (!$tenant->users()->where('user_id', $user->id)->exists()) {
            $tenant->users()->attach($user->id, [
                'role' => 'owner',
                'is_active' => true,
                'joined_at' => now(),
            ]);
        }
        
        echo "✅ Tenant linked to user!\n";
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "LOGIN CREDENTIALS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Email: {$email}\n";
echo "Password: admin123\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\nLogin URL: http://localhost:8000/login\n";

