<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderTrackingProgress extends Component
{
    #[Locked]
    public int $orderId;

    public function mount(Order $order): void
    {
        $this->orderId = $order->id;
        $this->order();
    }

    #[On('order-status-updated')]
    public function refreshTracking(int $orderId): void
    {
        if ($orderId !== $this->orderId) {
            return;
        }

        $order = $this->order();
        $this->dispatch(
            'order-tracking-updated',
            orderId: $order->id,
            status: $order->status,
            riderLatitude: $order->rider?->latitude,
            riderLongitude: $order->rider?->longitude,
            riderName: $order->rider?->user?->name ?? $order->rider?->name ?? 'Your rider',
        );
    }

    private function order(): Order
    {
        $order = Order::with(['store', 'rider.user', 'statusHistories'])->findOrFail($this->orderId);
        Gate::authorize('view', $order);

        return $order;
    }

    public function render()
    {
        $order = $this->order();
        $workflowSteps = [
            'pending' => 'Order placed',
            'approved' => 'Approved by store',
            'preparing' => 'Being prepared',
            'ready_for_pickup' => 'Ready for pickup',
            'rider_assigned' => 'Rider assigned',
            'picked_up' => 'Picked up',
            'out_for_delivery' => 'Out for delivery',
            'delivered' => 'Delivered',
        ];
        $currentStep = array_search($order->status, array_keys($workflowSteps), true);
        $riderIsAssigned = in_array($order->status, ['rider_assigned', 'picked_up', 'out_for_delivery', 'delivered'], true)
            && $order->rider;

        return view('livewire.order-tracking-progress', compact('order', 'workflowSteps', 'currentStep', 'riderIsAssigned'));
    }
}
