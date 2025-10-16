<?php

namespace App\Events;

use App\Models\Driver;
use App\Models\SubOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DriverActionOnOrder
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $trackingId;
    public Driver $driver;
    public string $action; // e.g., 'accepted', 'status_updated'

    public function __construct(string $trackingId, Driver $driver, string $action)
    {
        $this->trackingId = $trackingId;
        $this->driver = $driver;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
