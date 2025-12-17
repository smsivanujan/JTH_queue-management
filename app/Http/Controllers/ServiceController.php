<?php

namespace App\Http\Controllers;

use App\Events\ServiceUpdated;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Generic Service Controller
 * 
 * Handles queue management for any service type (OPD Lab, Customer Service, Order Pickup, etc.)
 * Replaces hardcoded OPD Lab logic with a data-driven approach.
 */
class ServiceController extends Controller
{
    /**
     * Show service queue management page
     */
    public function index(Service $service)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // Verify service belongs to tenant
        if ($service->tenant_id !== $tenant->id) {
            abort(403, 'Service not found.');
        }

        $labels = $service->labels;

        return view('service.index', compact('service', 'labels'));
    }

    /**
     * Show second screen for service
     */
    public function secondScreen(Request $request, Service $service)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // Verify service belongs to tenant
        if ($service->tenant_id !== $tenant->id) {
            abort(403, 'Service not found.');
        }

        // Get screen token from query parameter
        $screenToken = $request->query('token');
        
        return view('service.second-screen', [
            'service' => $service,
            'screenToken' => $screenToken
        ]);
    }

    /**
     * Verify service password
     */
    public function verifyPassword(Request $request, Service $service)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // Verify service belongs to tenant
        if ($service->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 403);
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (empty($service->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => "{$service->name} access is not configured. Please contact your administrator."
            ], 403);
        }

        $passwordValid = $service->verifyPassword($validated['password']);

        if ($passwordValid) {
            // Store in session for access
            session(["service_{$service->id}_verified" => true]);
            
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid password. Please try again.'
        ], 401);
    }

    /**
     * Broadcast service token update
     * Called from frontend when tokens/numbers are displayed
     */
    public function broadcastUpdate(Request $request, Service $service)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // Verify service belongs to tenant
        if ($service->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Service not found'], 403);
        }

        if ($service->isRangeType()) {
            $validated = $request->validate([
                'start' => ['required', 'integer', 'min:1'],
                'end' => ['required', 'integer', 'min:1'],
                'label' => ['required', 'string'], // Service label name
                'tokenData' => ['required', 'array'],
            ]);
        } else {
            // Sequential type
            $validated = $request->validate([
                'number' => ['required', 'integer', 'min:1'],
                'label' => ['required', 'string'], // Service label name
                'tokenData' => ['required', 'array'],
            ]);
        }

        // Broadcast service update via WebSocket
        event(new ServiceUpdated($tenant->id, $service->id, [
            'service_name' => $service->name,
            'service_type' => $service->type,
            'label' => $validated['label'],
            'tokens' => $validated['tokenData'],
            'start' => $validated['start'] ?? null,
            'end' => $validated['end'] ?? null,
            'number' => $validated['number'] ?? null,
            'timestamp' => now()->toISOString(),
        ]));

        return response()->json(['success' => true]);
    }
}
