<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdated;
use App\Models\Clinic;
use App\Models\Queue;
use App\Models\SubQueue;
use Illuminate\Http\Request;

class QueueController extends Controller
{

    public function index(Clinic $clinic)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            \Log::error('QueueController@index: Tenant not set', [
                'clinic_id' => $clinic->id,
                'user_id' => auth()->id(),
                'current_tenant_id' => auth()->user()?->current_tenant_id,
            ]);
            abort(500, 'System error: Tenant context not available');
        }

        // Clinic is already scoped by tenant via route model binding
        // Access is controlled by AuthorizeQueueAccess middleware

        // Get queue for this clinic (explicitly scope by tenant_id for safety)
        $queue = Queue::where('clinic_id', $clinic->id)
            ->where('tenant_id', $tenant->id)
            ->first();

        // If no queue exists, create a default one
        if (!$queue) {
            try {
                $queue = Queue::create([
                    'clinic_id' => $clinic->id,
                    'tenant_id' => $tenant->id,
                    'display' => 1, // default display value
                    'type' => 'sequential' // default to sequential
                ]);
            } catch (\Exception $e) {
                \Log::error('QueueController@index: Failed to create queue', [
                    'clinic_id' => $clinic->id,
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                abort(500, 'Failed to create queue: ' . $e->getMessage());
            }
        }

        // Ensure queue was created successfully
        if (!$queue) {
            \Log::error('QueueController@index: Queue is null after creation attempt', [
                'clinic_id' => $clinic->id,
                'tenant_id' => $tenant->id,
            ]);
            abort(500, 'System error: Unable to create or retrieve queue');
        }

        // Fallback to default view if custom display view is not found
        return view('index', compact('queue', 'clinic', 'tenant'));
    }

    /**
     * Move to the next number in the queue
     */
    public function next(Clinic $clinic, $queueNumber)
    {
        // Validate queueNumber route parameter
        if (!is_numeric($queueNumber) || (int)$queueNumber < 1) {
            return response()->json(['success' => false, 'message' => 'Invalid queue number'], 400);
        }

        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            \Log::error('QueueController: Tenant not set', [
                'method' => __FUNCTION__,
                'clinic_id' => $clinic->id ?? null,
                'user_id' => auth()->id(),
                'current_tenant_id' => auth()->user()?->current_tenant_id,
            ]);
            abort(500, 'System error: Tenant context not available');
        }

        // Clinic is already scoped by tenant via route model binding
        $queueNumber = (int)$queueNumber; // Ensure it's an integer
        
        $subQueue = SubQueue::firstOrCreate(
            ['clinic_id' => $clinic->id, 'queue_number' => $queueNumber],
            [
                'tenant_id' => $tenant->id,
                'current_number' => 1,
                'next_number' => 2
            ]
        );

        // Ensure tenant_id is set
        if (!$subQueue->tenant_id) {
            $subQueue->tenant_id = $tenant->id;
            $subQueue->save();
        }

        $subQueue->current_number = $subQueue->next_number;
        $subQueue->next_number += 1;
        $subQueue->save();

        // Broadcast queue update via WebSocket
        $subQueues = SubQueue::where('clinic_id', $clinic->id)->get();
        $subQueueData = $subQueues->map(function ($sq) {
            return [
                'clinic_id' => $sq->clinic_id,
                'queue_number' => $sq->queue_number,
                'current_number' => $sq->current_number,
                'next_number' => $sq->next_number
            ];
        })->toArray();
        
        event(new QueueUpdated($tenant->id, $clinic->id, $subQueueData));

        return response()->json(['success' => true]);
    }

    /**
     * Move to the previous number in the queue
     */
    public function previous(Clinic $clinic, $queueNumber)
    {
        // Validate queueNumber route parameter
        if (!is_numeric($queueNumber) || (int)$queueNumber < 1) {
            return response()->json(['success' => false, 'message' => 'Invalid queue number'], 400);
        }

        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            \Log::error('QueueController: Tenant not set', [
                'method' => __FUNCTION__,
                'clinic_id' => $clinic->id ?? null,
                'user_id' => auth()->id(),
                'current_tenant_id' => auth()->user()?->current_tenant_id,
            ]);
            abort(500, 'System error: Tenant context not available');
        }

        // Clinic is already scoped by tenant via route model binding
        $queueNumber = (int)$queueNumber; // Ensure it's an integer
        
        $subQueue = SubQueue::firstOrCreate(
            ['clinic_id' => $clinic->id, 'queue_number' => $queueNumber],
            [
                'tenant_id' => $tenant->id,
                'current_number' => 1,
                'next_number' => 2
            ]
        );

        // Ensure tenant_id is set
        if (!$subQueue->tenant_id) {
            $subQueue->tenant_id = $tenant->id;
            $subQueue->save();
        }

        if ($subQueue->current_number > 1) {
            $subQueue->next_number = $subQueue->current_number;
            $subQueue->current_number -= 1;
            $subQueue->save();
        }

        // Broadcast queue update via WebSocket
        $subQueues = SubQueue::where('clinic_id', $clinic->id)->get();
        $subQueueData = $subQueues->map(function ($sq) {
            return [
                'clinic_id' => $sq->clinic_id,
                'queue_number' => $sq->queue_number,
                'current_number' => $sq->current_number,
                'next_number' => $sq->next_number
            ];
        })->toArray();
        
        event(new QueueUpdated($tenant->id, $clinic->id, $subQueueData));

        return response()->json(['success' => true]);
    }

    /**
     * Reset queue to initial state
     */
    public function reset(Clinic $clinic, $queueNumber)
    {
        // Validate queueNumber route parameter
        if (!is_numeric($queueNumber) || (int)$queueNumber < 1) {
            return response()->json(['success' => false, 'message' => 'Invalid queue number'], 400);
        }

        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            \Log::error('QueueController: Tenant not set', [
                'method' => __FUNCTION__,
                'clinic_id' => $clinic->id ?? null,
                'user_id' => auth()->id(),
                'current_tenant_id' => auth()->user()?->current_tenant_id,
            ]);
            abort(500, 'System error: Tenant context not available');
        }

        // Clinic is already scoped by tenant via route model binding
        $queueNumber = (int)$queueNumber; // Ensure it's an integer
        
        $subQueue = SubQueue::firstOrCreate(
            ['clinic_id' => $clinic->id, 'queue_number' => $queueNumber],
            [
                'tenant_id' => $tenant->id,
                'current_number' => 1,
                'next_number' => 2
            ]
        );

        // Ensure tenant_id is set
        if (!$subQueue->tenant_id) {
            $subQueue->tenant_id = $tenant->id;
            $subQueue->save();
        }

        $subQueue->current_number = 1;
        $subQueue->next_number = 2;
        $subQueue->save();

        // Broadcast queue update via WebSocket
        $subQueues = SubQueue::where('clinic_id', $clinic->id)->get();
        $subQueueData = $subQueues->map(function ($sq) {
            return [
                'clinic_id' => $sq->clinic_id,
                'queue_number' => $sq->queue_number,
                'current_number' => $sq->current_number,
                'next_number' => $sq->next_number
            ];
        })->toArray();
        
        event(new QueueUpdated($tenant->id, $clinic->id, $subQueueData));

        return response()->json(['success' => true]);
    }

    /**
     * Get live queue data for AJAX polling
     */
    public function getLiveQueue(Clinic $clinic)
    {
        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            \Log::error('QueueController: Tenant not set', [
                'method' => __FUNCTION__,
                'clinic_id' => $clinic->id ?? null,
                'user_id' => auth()->id(),
                'current_tenant_id' => auth()->user()?->current_tenant_id,
            ]);
            abort(500, 'System error: Tenant context not available');
        }

        // Clinic is already scoped by tenant via route model binding
        // Automatically scoped by TenantScope
        $subQueues = SubQueue::where('clinic_id', $clinic->id)->get();

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
     * Update queue type
     */
    public function updateType(Request $request, Clinic $clinic)
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Tenant not set'], 500);
        }

        $validated = $request->validate([
            'type' => ['required', 'in:sequential,range'],
        ]);

        $queue = Queue::where('clinic_id', $clinic->id)
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$queue) {
            return response()->json(['success' => false, 'message' => 'Queue not found'], 404);
        }

        $queue->type = $validated['type'];
        $queue->save();

        return response()->json(['success' => true, 'type' => $queue->type]);
    }

    /**
     * Handle range-based queue update
     */
    public function updateRange(Request $request, Clinic $clinic, $queueNumber)
    {
        // Validate queueNumber route parameter
        if (!is_numeric($queueNumber) || (int)$queueNumber < 1) {
            return response()->json(['success' => false, 'message' => 'Invalid queue number'], 400);
        }

        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Tenant not set'], 500);
        }

        $validated = $request->validate([
            'start' => ['required', 'integer', 'min:1'],
            'end' => ['required', 'integer', 'min:1'],
        ]);

        if ($validated['end'] < $validated['start']) {
            return response()->json(['success' => false, 'message' => 'End value must be greater than or equal to start value'], 400);
        }

        $queueNumber = (int)$queueNumber;
        
        $subQueue = SubQueue::firstOrCreate(
            ['clinic_id' => $clinic->id, 'queue_number' => $queueNumber],
            [
                'tenant_id' => $tenant->id,
                'current_number' => 1,
                'next_number' => 2
            ]
        );

        // Ensure tenant_id is set
        if (!$subQueue->tenant_id) {
            $subQueue->tenant_id = $tenant->id;
            $subQueue->save();
        }

        // For range-based, we store the range in current_number and next_number
        // current_number = start, next_number = end
        $subQueue->current_number = $validated['start'];
        $subQueue->next_number = $validated['end'];
        $subQueue->save();

        // Broadcast queue update via WebSocket
        $subQueues = SubQueue::where('clinic_id', $clinic->id)->get();
        $subQueueData = $subQueues->map(function ($sq) {
            return [
                'clinic_id' => $sq->clinic_id,
                'queue_number' => $sq->queue_number,
                'current_number' => $sq->current_number,
                'next_number' => $sq->next_number
            ];
        })->toArray();
        
        event(new QueueUpdated($tenant->id, $clinic->id, $subQueueData));

        return response()->json(['success' => true]);
    }
}
