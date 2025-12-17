<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The queue data to broadcast.
     *
     * @var array
     */
    public $subQueues;

    /**
     * The tenant ID for channel isolation.
     *
     * @var int
     */
    public $tenantId;

    /**
     * The clinic ID.
     *
     * @var int
     */
    public $clinicId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $tenantId, int $clinicId, array $subQueues)
    {
        $this->tenantId = $tenantId;
        $this->clinicId = $clinicId;
        $this->subQueues = $subQueues;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Private channel with tenant isolation: private-tenant.{tenantId}.queue.{clinicId}
        return [
            new PrivateChannel("tenant.{$this->tenantId}.queue.{$this->clinicId}"),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'queue.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'subQueues' => $this->subQueues,
            'clinic_id' => $this->clinicId,
        ];
    }
}
