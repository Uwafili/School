<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Rider;

class OrderNotificationService
{
    public function sendToUser(?int $userId, Order $order, string $title, string $message, string $type): ?Notification
    {
        if (! $userId) {
            return null;
        }

        return Notification::create([
            'user_id' => $userId,
            'rider_id' => Rider::where('user_id', $userId)->value('id'),
            'order_id' => $order->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
        ]);
    }

    public function sendToCustomer(Order $order, string $title, string $message, string $type): ?Notification
    {
        return $this->sendToUser($order->customer_id, $order, $title, $message, $type);
    }

    public function sendToStore(Order $order, string $title, string $message, string $type): ?Notification
    {
        $storeOwnerId = $order->store()->value('user_id');

        return $this->sendToUser($storeOwnerId, $order, $title, $message, $type);
    }

    public function sendToRider(Order $order, string $title, string $message, string $type): ?Notification
    {
        $rider = $order->rider()->first();
        if (! $rider) {
            return null;
        }

        return Notification::create([
            'user_id' => $rider->user_id,
            'rider_id' => $rider->id,
            'order_id' => $order->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
        ]);
    }
}
