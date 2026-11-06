<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Rider;
use App\Models\User;
use App\Notifications\OrderUpdateNotification;

class OrderNotificationService
{
    public function notifyCustomerOfStatusChange(Order $order, string $status): ?Notification
    {
        $labels = [
            'pending' => 'Order placed',
            'approved' => 'Order approved by store',
            'preparing' => 'Order being prepared',
            'ready_for_pickup' => 'Order ready for pickup',
            'rider_assigned' => 'Rider assigned',
            'picked_up' => 'Order picked up',
            'out_for_delivery' => 'Order out for delivery',
            'delivered' => 'Order delivered',
            'rejected' => 'Order rejected by store',
            'cancelled' => 'Order cancelled',
        ];
        $label = $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));

        return $this->sendToCustomer(
            $order,
            $label,
            "{$order->order_reference}: {$label}.",
            'order_status_'.$status
        );
    }

    public function sendToUser(?int $userId, Order $order, string $title, string $message, string $type): ?Notification
    {
        if (! $userId) {
            return null;
        }

        $rider = Rider::where('user_id', $userId)->first();
        $legacyNotification = Notification::create([
            'user_id' => $userId,
            'rider_id' => $rider?->id,
            'order_id' => $order->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
        ]);

        $notifiable = $rider ?? User::findOrFail($userId);
        $notifiable->notify(new OrderUpdateNotification(
            $title,
            $message,
            $type,
            $order->id,
            $order->order_reference ?? 'FS-'.$order->id
        ));

        return $legacyNotification;
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

        return $this->sendToRiderId($rider->id, $order, $title, $message, $type);
    }

    public function sendToRiderId(int $riderId, Order $order, string $title, string $message, string $type): ?Notification
    {
        $rider = Rider::find($riderId);

        if (! $rider) {
            return null;
        }

        return $this->sendToUser($rider->user_id, $order, $title, $message, $type);
    }
}
