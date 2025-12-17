<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Generic Service Updated Event
 * 
 * Broadcasts updates for any service type (replaces OPDLabUpdated).
 * Used for real-time updates on service queue displays.
 */
class ServiceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The service token data to broadcast.
     *
     * @var array
     */
    public $tokenData;

    /**
     * The tenant ID for channel isolation.
     *
     * @var int
     */
    public $tenantId;

    /**
     * The service ID.
     *
     * @var int
     */
    public $serviceId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $tenantId, int $serviceId, array $tokenData)
    {
        $this->tenantId = $tenantId;
        $this->serviceId = $serviceId;
        $this->tokenData = $tokenData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Private channel with tenant isolation: private-tenant.{tenantId}.service.{serviceId}
        return [
            new PrivateChannel("tenant.{$this->tenantId}.service.{$this->serviceId}"),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'service.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return $this->tokenData;
    }
}
