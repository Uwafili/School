<?php

namespace App\Livewire;

use App\Jobs\ProcessPaystackRefund;
use App\Models\Order;
use App\Models\Store;
use App\Services\NearbyOrderDispatcher;
use App\Services\OrderWorkflowService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class StoreOrderActions extends Component
{
    public int $orderId;

    public string $reason = '';

    #[On('order-status-updated')]
    public function refreshOrder(int $orderId): void
    {
        if ($orderId === $this->orderId) {
            $this->order();
        }
    }

    public function mount(Order $order): void
    {
        $this->orderId = $order->id;
        $this->order();
    }

    public function approve(OrderWorkflowService $workflow): void
    {
        $order = $this->order();
        abort_unless($order->store_approval_status === 'pending', 422, 'This order is no longer waiting for approval.');

        $workflow->transition($order, 'approved', [
            'store_approval_status' => 'approved',
            'store_approved_at' => now(),
        ]);
        $this->notifySuccess('Order approved.');
    }

    public function reject(OrderWorkflowService $workflow): void
    {
        $validated = $this->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);
        $order = $this->order();
        abort_unless($order->store_approval_status === 'pending', 422, 'This order is no longer waiting for approval.');
        $isPaid = $order->payment_status === 'paid';
        $rejectedOrder = $workflow->transition($order, 'rejected', [
            'store_approval_status' => 'rejected',
            'store_rejected_at' => now(),
            'store_rejection_reason' => $validated['reason'] ?: null,
            'refund_status' => $isPaid ? 'pending' : 'none',
            'payment_status' => $isPaid ? 'refund_pending' : $order->payment_status,
        ]);

        if ($rejectedOrder->payment_status === 'refund_pending') {
            ProcessPaystackRefund::dispatch($rejectedOrder->id);
        }

        $this->reset('reason');
        $this->notifySuccess('Order rejected.');
    }

    public function startPreparing(OrderWorkflowService $workflow): void
    {
        $workflow->transition($this->order(), 'preparing');
        $this->notifySuccess('Order marked as preparing.');
    }

    public function markReady(NearbyOrderDispatcher $dispatcher, OrderWorkflowService $workflow): void
    {
        $order = $workflow->transition($this->order(), 'ready_for_pickup');
        $dispatcher->dispatch($order);
        $this->notifySuccess('Order marked ready for pickup.');
    }

    public function cancelDelivery(OrderWorkflowService $workflow): void
    {
        $order = $this->order();
        abort_unless($order->status === 'rider_assigned', 422, 'This delivery cannot be cancelled in its current state.');
        $isPaid = $order->payment_status === 'paid';
        $cancelledOrder = $workflow->transition($order, 'cancelled', [
            'rider_id' => null,
            'recipient_code' => null,
            'refund_status' => $isPaid ? 'pending' : 'none',
            'payment_status' => $isPaid ? 'refund_pending' : $order->payment_status,
        ], fn (Order $lockedOrder): bool => (int) $lockedOrder->store_id === (int) $order->store_id
                && $lockedOrder->status === 'rider_assigned'
                && $lockedOrder->picked_up_at === null
        );

        if ($cancelledOrder->payment_status === 'refund_pending') {
            ProcessPaystackRefund::dispatch($cancelledOrder->id);
        }

        $this->notifySuccess('Delivery cancelled. The assigned rider has been notified.');
    }

    public function retryRefund(): void
    {
        $order = $this->order();
        abort_unless(
            in_array($order->status, ['rejected', 'cancelled'], true)
                && $order->refund_status === 'pending'
                && $order->payment_reference
                && ! $order->refund_reference,
            422,
            'This order does not have a retryable refund.'
        );

        ProcessPaystackRefund::dispatch($order->id);
        $this->notifySuccess('Refund retry queued.');
    }

    private function order(): Order
    {
        $order = Order::with('store')->findOrFail($this->orderId);
        $store = Store::where('user_id', Auth::id())->findOrFail($order->store_id);
        abort_unless((int) $store->id === (int) $order->store_id, 403);

        return $order;
    }

    private function notifySuccess(string $message): void
    {
        $this->dispatch('toast', type: 'success', message: $message);
    }

    public function render()
    {
        return view('livewire.store-order-actions', [
            'order' => $this->order(),
        ]);
    }
}
