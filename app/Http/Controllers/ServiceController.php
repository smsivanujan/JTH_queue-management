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
     * List all services for the current tenant
     */
    public function list()
    {
        $tenant = app('tenant');
        $services = Service::orderBy('name')->get();
        
        return view('service.list', compact('services', 'tenant'));
    }

    /**
     * Show the form for creating a new service
     */
    public function create()
    {
        $tenant = app('tenant');
        return view('service.create');
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $tenant = app('tenant');
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:sequential,range'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        try {
            $service = Service::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'tenant_id' => $tenant->id,
                'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
            ]);

            // Set password if provided
            if (!empty($validated['password'])) {
                $service->setPassword($validated['password']);
            }

            return redirect()->route('app.services.list')
                ->with('success', 'Service created successfully.');
        } catch (\Exception $e) {
            \Log::error('Service creation failed', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id ?? null,
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create service. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing a service
     */
    public function edit(Service $service)
    {
        $tenant = app('tenant');
        
        if ($service->tenant_id !== $tenant->id) {
            abort(403, 'Service not found.');
        }

        return view('service.edit', compact('service'));
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, Service $service)
    {
        $tenant = app('tenant');
        
        if ($service->tenant_id !== $tenant->id) {
            abort(403, 'Service not found.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:sequential,range'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        try {
            $service->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
            ]);

            // Update password if provided
            if (!empty($validated['password'])) {
                $service->setPassword($validated['password']);
            }

            return redirect()->route('app.services.list')
                ->with('success', 'Service updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Service update failed', [
                'error' => $e->getMessage(),
                'service_id' => $service->id,
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update service. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified service
     */
    public function destroy(Service $service)
    {
        $tenant = app('tenant');
        
        if ($service->tenant_id !== $tenant->id) {
            abort(403, 'Service not found.');
        }

        try {
            $service->delete();
            return redirect()->route('app.services.list')
                ->with('success', 'Service deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Service deletion failed', [
                'error' => $e->getMessage(),
                'service_id' => $service->id,
            ]);
            
            return back()
                ->withErrors(['error' => 'Failed to delete service. Please try again.']);
        }
    }

    /**
     * Show service queue management page
     */
    public function show(Service $service)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // Verify service belongs to tenant
        if ($service->tenant_id !== $tenant->id) {
            abort(403, 'Service not found.');
        }

        $labels = $service->labels;

        return view('service.show', compact('service', 'labels'));
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
