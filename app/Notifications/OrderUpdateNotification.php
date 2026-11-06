<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class OrderUpdateNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $title,
        private readonly string $message,
        private readonly string $eventType,
        private readonly int $orderId,
        private readonly string $orderReference,
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (config('broadcasting.enabled')) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->notificationData();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->notificationData());
    }

    public function broadcastType(): string
    {
        return 'order.update';
    }

    private function notificationData(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->eventType,
            'order_id' => $this->orderId,
            'order_reference' => $this->orderReference,
        ];
    }
}
