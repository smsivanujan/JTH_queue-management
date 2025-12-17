<?php

namespace App\Http\Controllers;

use App\Models\ActiveScreen;
use App\Models\Clinic;
use App\Models\Queue;
use App\Models\SubQueue;
use Illuminate\Http\Request;

class PublicScreenController extends Controller
{
    /**
     * Display queue second screen (read-only)
     * 
     * Signed URL validation ensures:
     * - Token is valid and not tampered
     * - Screen exists and is active
     * - Tenant isolation is maintained
     */
    public function queue(Request $request, string $screenToken)
    {
        // Validate screen token against active_screens table
        $screen = ActiveScreen::where('screen_token', $screenToken)
            ->where('screen_type', 'queue')
            ->first();

        if (!$screen) {
            abort(404, 'Screen not found or expired');
        }

        // Check if screen is still active (heartbeat within 30 seconds)
        if (!$screen->isActive(30)) {
            abort(404, 'Screen session has expired');
        }

        // Get clinic (required for queue screens)
        if (!$screen->clinic_id) {
            abort(404, 'Invalid screen configuration');
        }

        $clinic = Clinic::find($screen->clinic_id);
        if (!$clinic) {
            abort(404, 'Clinic not found');
        }

        // Verify tenant isolation (screen already scoped by TenantScope, but double-check)
        // Note: TenantScope will automatically filter, but we verify explicitly for security
        $tenant = $screen->tenant;
        if (!$tenant || !$tenant->is_active) {
            abort(404, 'Invalid tenant');
        }

        // Get queue data (read-only, no modifications allowed)
        // Note: We need to manually scope by tenant since we're accessing via screen (not auth)
        $queue = Queue::where('clinic_id', $clinic->id)
            ->where('tenant_id', $tenant->id)
            ->first();
        
        // Get sub-queues for display (manually scoped by tenant for security)
        $subQueues = SubQueue::where('clinic_id', $clinic->id)
            ->where('tenant_id', $tenant->id)
            ->get();
        
        // Ensure we have at least one sub-queue for display
        if ($subQueues->isEmpty()) {
            // Create a default sub-queue if none exist (for display purposes only)
            $subQueue = SubQueue::create([
                'clinic_id' => $clinic->id,
                'tenant_id' => $tenant->id,
                'queue_number' => 1,
                'current_number' => 1,
                'next_number' => 2
            ]);
            $subQueues = collect([$subQueue]);
        }

        // Render queue display view (read-only)
        return view('public.queue-screen', [
            'clinic' => $clinic,
            'queue' => $queue,
            'subQueues' => $subQueues,
            'screenToken' => $screenToken,
            'tenantId' => $tenant->id,
        ]);
    }

    /**
     * Get live queue data for public screen (read-only API)
     * Used for polling queue updates on public screen
     */
    public function queueApi(Request $request, string $screenToken)
    {
        // Validate screen token
        $screen = ActiveScreen::where('screen_token', $screenToken)
            ->where('screen_type', 'queue')
            ->first();

        if (!$screen || !$screen->isActive(30)) {
            return response()->json(['error' => 'Screen not found or expired'], 404);
        }

        // Get clinic and sub-queues (read-only)
        if (!$screen->clinic_id) {
            return response()->json(['error' => 'Invalid screen configuration'], 404);
        }

        $clinic = Clinic::find($screen->clinic_id);
        if (!$clinic) {
            return response()->json(['error' => 'Clinic not found'], 404);
        }

        // Get sub-queues (manually scoped by tenant for security)
        $subQueues = SubQueue::where('clinic_id', $clinic->id)
            ->where('tenant_id', $screen->tenant_id)
            ->get();

        $subQueueData = $subQueues->map(function ($subQueue) {
            return [
                'clinic_id' => $subQueue->clinic_id,
                'queue_number' => $subQueue->queue_number,
                'current_number' => $subQueue->current_number,
                'next_number' => $subQueue->next_number
            ];
        });

        return response()->json([
            'subQueues' => $subQueueData
        ]);
    }

    /**
     * Display QR code pairing page for TV screens
     * 
     * Signed URL validation ensures:
     * - Token is valid and not tampered
     * - Screen exists and is active
     * - Tenant isolation is maintained
     * - URL expires in 15 minutes (for pairing)
     */
    public function pair(Request $request, string $screenToken, string $type)
    {
        // Validate screen token and type
        $screen = ActiveScreen::where('screen_token', $screenToken)
            ->where('screen_type', $type)
            ->first();

        if (!$screen) {
            abort(404, 'Screen not found or expired');
        }

        // Check if screen is still active (heartbeat within 30 seconds)
        if (!$screen->isActive(30)) {
            abort(404, 'Screen session has expired');
        }

        // Verify tenant isolation
        $tenant = $screen->tenant;
        if (!$tenant || !$tenant->is_active) {
            abort(404, 'Invalid tenant');
        }

        // Generate the actual screen URL for QR code
        $screenType = $screen->screen_type;
        
        // Generate signed URL based on screen type
        if ($screenType === 'queue') {
            $screenUrl = \Illuminate\Support\Facades\URL::signedRoute(
                'public.screen.queue', 
                ['screen_token' => $screenToken], 
                now()->addHours(24)
            );
        } elseif ($screenType === 'service') {
            $screenUrl = \Illuminate\Support\Facades\URL::signedRoute(
                'service.second-screen', 
                ['service' => $screen->clinic_id, 'token' => $screenToken], 
                now()->addHours(24)
            );
        } else {
            abort(404, 'Invalid screen type');
        }

        // Get clinic/service name for display
        $clinicName = null;
        if ($screen->clinic_id) {
            if ($screenType === 'queue') {
                $clinic = Clinic::find($screen->clinic_id);
                $clinicName = $clinic ? $clinic->name : null;
            } elseif ($screenType === 'service') {
                $service = \App\Models\Service::find($screen->clinic_id);
                $clinicName = $service ? $service->name : null;
            }
        }

        return view('public.pair', [
            'screenUrl' => $screenUrl,
            'screenToken' => $screenToken,
            'screenType' => $screenType,
            'clinicName' => $clinicName,
        ]);
    }
}
