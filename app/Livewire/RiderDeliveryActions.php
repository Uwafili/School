<?php

namespace App\Livewire;

use App\Models\DeliveryAttempt;
use App\Models\Order;
use App\Models\Rider;
use App\Services\OrderNotificationService;
use App\Services\OrderWorkflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RiderDeliveryActions extends Component
{
    #[Locked]
    public int $orderId;

    public string $recipientCode = '';

    public string $failureReason = '';

    public string $declineReason = '';

    public function mount(Order $order): void
    {
        $this->orderId = $order->id;
        $this->order();
    }

    public function acceptAssignment(OrderNotificationService $notifications): void
    {
        $rider = $this->rider();
        $order = DB::transaction(function () use ($rider) {
            $order = Order::query()->lockForUpdate()->where('rider_id', $rider->id)->findOrFail($this->orderId);
            abort_unless($order->status === 'rider_assigned', 422, 'This order is no longer awaiting acknowledgment.');
            $order->update(['rider_accepted_at' => $order->rider_accepted_at ?? now()]);
            $order->riderAssignments()
                ->where('rider_id', $rider->id)
                ->whereNull('released_at')
                ->update(['status' => 'accepted', 'acknowledged_at' => $order->rider_accepted_at]);

            return $order->fresh(['store']);
        });
        $notifications->sendToStore($order, 'Rider accepted assignment', "The rider accepted delivery {$order->order_reference}.", 'order_assignment_accepted');
        $this->notifySuccess('Delivery assignment acknowledged.');
    }

    public function confirmArrival(): void
    {
        $rider = $this->rider();
        DB::transaction(function () use ($rider): void {
            $order = Order::query()->lockForUpdate()->where('rider_id', $rider->id)->findOrFail($this->orderId);
            abort_unless(
                $order->status === 'rider_assigned' && $order->rider_accepted_at !== null,
                422,
                'Acknowledge this delivery before confirming arrival.'
            );
            $order->update(['rider_arrived_at' => $order->rider_arrived_at ?? now()]);
            $order->riderAssignments()
                ->where('rider_id', $rider->id)
                ->whereNull('released_at')
                ->update(['arrived_at' => $order->rider_arrived_at]);
        });

        $this->notifySuccess('Arrival at the store confirmed.');
    }

    public function confirmPickup(OrderWorkflowService $workflow, OrderNotificationService $notifications): void
    {
        $rider = $this->rider();
        $order = $workflow->transition($this->order(), 'picked_up', [
            'picked_up_at' => now(),
        ], fn (Order $lockedOrder): bool => (int) $lockedOrder->rider_id === (int) $rider->id
                && $lockedOrder->picked_up_at === null
                && $lockedOrder->rider_accepted_at !== null
                && $lockedOrder->rider_arrived_at !== null
                && $lockedOrder->store_approval_status === 'approved'
                && $lockedOrder->payment_status === 'paid'
        );

        $notifications->sendToStore($order, 'Order picked up', "The rider picked up order #{$order->id}.", 'order_picked_up');
        $this->notifySuccess('Pickup confirmed. Take the order to the customer.');
    }

    public function markOutForDelivery(OrderWorkflowService $workflow, OrderNotificationService $notifications): void
    {
        $rider = $this->rider();
        $order = $workflow->transition($this->order(), 'out_for_delivery', [], fn (Order $lockedOrder): bool => (int) $lockedOrder->rider_id === (int) $rider->id && $lockedOrder->picked_up_at !== null
        );

        $notifications->sendToStore($order, 'Order is out for delivery', "Order #{$order->id} is on its way to the customer.", 'order_out_for_delivery');
        $this->notifySuccess('Order marked out for delivery.');
    }

    public function confirmDelivery(OrderWorkflowService $workflow, OrderNotificationService $notifications): void
    {
        $this->validate(['recipientCode' => ['required', 'digits:4']]);
        $rider = $this->rider();
        $order = $this->order();

        if ($order->status !== 'out_for_delivery') {
            $this->addError('recipientCode', 'This order is not ready for delivery confirmation.');

            return;
        }
        if (! $order->recipient_code || ! hash_equals((string) $order->recipient_code, $this->recipientCode)) {
            $this->addError('recipientCode', 'The recipient code is incorrect. Ask the customer to show the code from their order.');

            return;
        }

        $order = $workflow->transition($order, 'delivered', [
            'recipient_verified_at' => now(),
        ], fn (Order $lockedOrder): bool => (int) $lockedOrder->rider_id === (int) $rider->id
                && $lockedOrder->picked_up_at !== null
                && $lockedOrder->recipient_code !== null
                && hash_equals((string) $lockedOrder->recipient_code, $this->recipientCode)
        );
        $notifications->sendToStore($order, 'Order delivered', "Order #{$order->id} was delivered successfully.", 'order_delivered');
        $this->reset('recipientCode');
        $this->notifySuccess('Delivery confirmed. Recipient verified successfully.');
    }

    public function reportFailedDelivery(OrderNotificationService $notifications): void
    {
        $validated = $this->validate([
            'failureReason' => ['required', 'string', 'max:1000'],
        ]);
        $rider = $this->rider();
        $order = $this->order();

        DB::transaction(function () use ($order, $rider, $validated, $notifications): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless(
                (int) $lockedOrder->rider_id === (int) $rider->id && $lockedOrder->status === 'out_for_delivery',
                422,
                'Only your order that is out for delivery can have a failed attempt reported.'
            );

            DeliveryAttempt::create([
                'order_id' => $lockedOrder->id,
                'rider_id' => $rider->id,
                'reason' => $validated['failureReason'],
                'attempted_at' => now(),
            ]);
            $notifications->sendToCustomer(
                $lockedOrder,
                'Delivery attempt unsuccessful',
                "The rider could not complete delivery for {$lockedOrder->order_reference}. Reason: {$validated['failureReason']}",
                'delivery_attempt_failed'
            );
            $notifications->sendToStore(
                $lockedOrder,
                'Delivery attempt unsuccessful',
                "The rider could not complete delivery for {$lockedOrder->order_reference}. Reason: {$validated['failureReason']}",
                'delivery_attempt_failed'
            );
        });

        $this->reset('failureReason');
        $this->notifySuccess('Failed delivery attempt reported.');
    }

    public function declineAssignment(OrderWorkflowService $workflow, OrderNotificationService $notifications): void
    {
        $validated = $this->validate([
            'declineReason' => ['nullable', 'string', 'max:500'],
        ]);
        $rider = $this->rider();
        $order = $this->order();
        abort_unless($order->status === 'rider_assigned', 422, 'You can only decline a delivery before pickup.');
        $reason = $validated['declineReason'] ?: 'No reason provided.';

        $order = $workflow->transition($order, 'ready_for_pickup', [
            'rider_id' => null,
            'recipient_code' => null,
            'rider_accepted_at' => null,
            'rider_arrived_at' => null,
        ], function (Order $lockedOrder) use ($rider): bool {
            if ((int) $lockedOrder->rider_id !== (int) $rider->id) {
                return false;
            }

            $lockedOrder->deliveryBids()
                ->where('rider_id', $rider->id)
                ->where('status', 'accepted')
                ->update(['status' => 'rejected']);

            return true;
        });
        $notifications->sendToStore($order, 'Rider declined the delivery', "The rider declined order #{$order->id}. Reason: {$reason}", 'order_rider_declined');
        $this->reset('declineReason');
        $this->notifySuccess('The delivery was returned to the store for reassignment.');
    }

    private function rider(): Rider
    {
        return Rider::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->where('is_active', true)
            ->firstOrFail();
    }

    private function order(): Order
    {
        $rider = $this->rider();

        return Order::with(['store', 'deliveryAttempts'])
            ->where('rider_id', $rider->id)
            ->findOrFail($this->orderId);
    }

    private function notifySuccess(string $message): void
    {
        $this->dispatch('toast', type: 'success', message: $message);
        $this->dispatch('delivery-updated');
    }

    public function render()
    {
        return view('livewire.rider-delivery-actions', [
            'order' => $this->order(),
        ]);
    }
}
