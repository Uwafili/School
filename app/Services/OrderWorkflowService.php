<?php

namespace App\Services;

use App\Models\Order;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderWorkflowService
{
    private const TRANSITIONS = [
        'pending' => ['approved', 'rejected', 'cancelled'],
        'approved' => ['preparing'],
        'preparing' => ['ready_for_pickup'],
        'ready_for_pickup' => ['rider_assigned'],
        'rider_assigned' => ['ready_for_pickup', 'picked_up'],
        'picked_up' => ['out_for_delivery'],
        'out_for_delivery' => ['delivered'],
        'delivered' => [],
        'rejected' => [],
        'cancelled' => [],
    ];

    public function transition(Order $order, string $nextStatus, array $attributes = [], ?Closure $guard = null): Order
    {
        return DB::transaction(function () use ($order, $nextStatus, $attributes, $guard) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $allowed = self::TRANSITIONS[$lockedOrder->status] ?? [];

            if (! in_array($nextStatus, $allowed, true)) {
                throw ValidationException::withMessages([
                    'status' => "Order cannot move from {$lockedOrder->status} to {$nextStatus}.",
                ]);
            }

            if ($guard && ! $guard($lockedOrder)) {
                throw ValidationException::withMessages([
                    'status' => 'The order or rider is no longer available for this transition.',
                ]);
            }

            $lockedOrder->update(array_merge($attributes, ['status' => $nextStatus]));

            return $lockedOrder->fresh();
        });
    }

    public function canTransition(Order $order, string $nextStatus): bool
    {
        return in_array($nextStatus, self::TRANSITIONS[$order->status] ?? [], true);
    }
}
