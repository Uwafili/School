<?php

namespace App\Services;

use App\Models\Order;
use App\Models\RiderAssignment;
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
        'rider_assigned' => ['ready_for_pickup', 'picked_up', 'cancelled'],
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

            $previousRiderId = $lockedOrder->rider_id;
            $lockedOrder->update(array_merge($attributes, ['status' => $nextStatus]));
            $lockedOrder->statusHistories()->create(['status' => $nextStatus]);
            app(OrderNotificationService::class)->notifyCustomerOfStatusChange($lockedOrder, $nextStatus);

            if ($previousRiderId && (int) $lockedOrder->rider_id !== (int) $previousRiderId) {
                $cancelled = in_array($nextStatus, ['cancelled', 'rejected'], true);
                RiderAssignment::where('order_id', $lockedOrder->id)
                    ->where('rider_id', $previousRiderId)
                    ->whereNull('released_at')
                    ->update([
                        'status' => $cancelled ? 'cancelled' : 'released',
                        'released_at' => now(),
                    ]);
                app(OrderNotificationService::class)->sendToRiderId(
                    (int) $previousRiderId,
                    $lockedOrder,
                    $cancelled ? 'Delivery cancelled' : 'Delivery assignment changed',
                    $cancelled
                        ? "Delivery {$lockedOrder->order_reference} was cancelled."
                        : "Delivery {$lockedOrder->order_reference} is no longer assigned to you.",
                    $cancelled ? 'order_cancelled' : 'order_assignment_changed'
                );
            }

            if ($lockedOrder->rider_id && (int) $lockedOrder->rider_id !== (int) $previousRiderId) {
                RiderAssignment::create([
                    'order_id' => $lockedOrder->id,
                    'rider_id' => $lockedOrder->rider_id,
                    'status' => 'assigned',
                    'assigned_at' => now(),
                ]);
            }

            if ($lockedOrder->rider_id && $nextStatus === 'picked_up') {
                RiderAssignment::where('order_id', $lockedOrder->id)
                    ->where('rider_id', $lockedOrder->rider_id)
                    ->whereNull('released_at')
                    ->update(['status' => 'picked_up', 'picked_up_at' => now()]);
            } elseif ($lockedOrder->rider_id && $nextStatus === 'delivered') {
                RiderAssignment::where('order_id', $lockedOrder->id)
                    ->where('rider_id', $lockedOrder->rider_id)
                    ->whereNull('released_at')
                    ->update(['status' => 'completed', 'completed_at' => now()]);
            }

            return $lockedOrder->fresh();
        });
    }

    public function canTransition(Order $order, string $nextStatus): bool
    {
        return in_array($nextStatus, self::TRANSITIONS[$order->status] ?? [], true);
    }
}
