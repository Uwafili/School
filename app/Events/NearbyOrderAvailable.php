<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NearbyOrderAvailable implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public int $riderId, public array $order)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('riders.' . $this->riderId)];
    }

    public function broadcastAs(): string
    {
        return 'nearby.order.created';
    }

    public function broadcastWith(): array
    {
        return ['order' => $this->order];
    }
}