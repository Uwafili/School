<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreOrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public int $storeUserId, public array $notification)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('stores.' . $this->storeUserId)];
    }

    public function broadcastAs(): string
    {
        return 'store.order.created';
    }

    public function broadcastWith(): array
    {
        return ['notification' => $this->notification];
    }
}