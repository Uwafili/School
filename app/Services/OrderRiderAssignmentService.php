<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class OrderRiderAssignmentService
{
    public function __construct(
        private readonly OrderWorkflowService $workflow,
        private readonly OrderNotificationService $notifications,
    ) {}

    public function assign(Order $order, int $riderId, User $actor): Order
    {
        $store = $order->store()->firstOrFail();
        $isAdmin = $actor->usertype === 'admin';
        abort_unless($isAdmin || (int) $store->user_id === (int) $actor->id, 403);
        abort_unless(
            $order->status === 'ready_for_pickup'
                && $order->payment_status === 'paid'
                && $order->store_approval_status === 'approved'
                && $order->rider_id === null,
            422,
            'Only paid, approved orders ready for pickup can be assigned.'
        );

        $rider = Rider::where('id', $riderId)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->where('is_online', true)
            ->first();

        if (! $rider) {
            throw ValidationException::withMessages([
                'riderId' => 'Choose an approved rider who is currently online.',
            ]);
        }

        if ($rider->latitude !== null && $rider->longitude !== null
            && $store->latitude !== null && $store->longitude !== null
            && $this->distanceInKilometres(
                (float) $store->latitude,
                (float) $store->longitude,
                (float) $rider->latitude,
                (float) $rider->longitude
            ) > 25) {
            throw ValidationException::withMessages([
                'riderId' => 'Choose a rider within 25 km of your store.',
            ]);
        }

        $deliveryFee = $order->delivery_fee;
        $order = $this->workflow->transition(
            $order,
            'rider_assigned',
            [
                'rider_id' => $rider->id,
                'recipient_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
                'delivery_fee' => $deliveryFee,
            ],
            function (Order $lockedOrder) use ($store, $rider): bool {
                $lockedRider = Rider::query()->lockForUpdate()->find($rider->id);

                return (int) $lockedOrder->store_id === (int) $store->id
                    && $lockedOrder->status === 'ready_for_pickup'
                    && $lockedOrder->payment_status === 'paid'
                    && $lockedOrder->store_approval_status === 'approved'
                    && ! $lockedOrder->rider_id
                    && $lockedRider?->status === 'approved'
                    && $lockedRider->is_active
                    && $lockedRider->is_online;
            }
        );

        $this->notifications->sendToRider(
            $order,
            'New order assigned',
            "You have been assigned order {$order->order_reference} from {$store->stores}. Delivery pay: ₦{$deliveryFee}",
            'order_assigned'
        );

        return $order;
    }

    private function distanceInKilometres(float $latitudeOne, float $longitudeOne, float $latitudeTwo, float $longitudeTwo): float
    {
        $earthRadius = 6371;
        $latitudeDelta = deg2rad($latitudeTwo - $latitudeOne);
        $longitudeDelta = deg2rad($longitudeTwo - $longitudeOne);
        $a = sin($latitudeDelta / 2) ** 2
            + cos(deg2rad($latitudeOne)) * cos(deg2rad($latitudeTwo)) * sin($longitudeDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
