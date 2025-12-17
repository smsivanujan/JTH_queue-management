<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ActiveScreen;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Tenant-scoped queue channel authorization
 * Format: private-tenant.{tenantId}.queue.{clinicId}
 * 
 * Authorization rules:
 * 1. Authenticated users: Must belong to the tenant
 * 2. Public screens: Must have valid screen_token passed via X-Screen-Token header
 */
Broadcast::channel('tenant.{tenantId}.queue.{clinicId}', function ($user, $tenantId, $clinicId) {
    // For authenticated users, check tenant membership
    if ($user) {
        return $user->tenant_id === (int) $tenantId;
    }
    
    // For public screens, check screen_token from custom header
    // We'll set this header via Echo's auth configuration
    $screenToken = request()->header('X-Screen-Token') ?: request()->input('screen_token');
    if ($screenToken) {
        $screen = ActiveScreen::where('screen_token', $screenToken)
            ->where('screen_type', 'queue')
            ->where('tenant_id', $tenantId)
            ->where('clinic_id', $clinicId)
            ->first();
            
        if ($screen && $screen->isActive(30)) {
            return ['screen_token' => $screenToken, 'tenant_id' => $tenantId];
        }
    }
    
    return false;
});

// Generic service channel
Broadcast::channel('tenant.{tenantId}.service.{serviceId}', function ($user, $tenantId, $serviceId) {
    // For authenticated users, check tenant membership
    if ($user) {
        return $user->tenant_id === (int) $tenantId;
    }
    
    // For public screens, check screen_token from custom header
    $screenToken = request()->header('X-Screen-Token') ?: request()->input('screen_token');
    if ($screenToken) {
        $screen = ActiveScreen::where('screen_token', $screenToken)
            ->where('screen_type', 'service')
            ->where('tenant_id', $tenantId)
            ->where('clinic_id', $serviceId) // Reusing clinic_id field to store service_id for screens
            ->first();
            
        if ($screen && $screen->isActive(30)) {
            return ['screen_token' => $screenToken, 'tenant_id' => $tenantId];
        }
    }
    
    return false;
});
