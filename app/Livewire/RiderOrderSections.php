<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Rider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class RiderOrderSections extends Component
{
    #[On('delivery-updated')]
    #[On('order-status-updated')]
    public function refreshOrderGroups(?int $orderId = null): void {}

    private function rider(): Rider
    {
        return Rider::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function render()
    {
        $orders = Order::where('rider_id', $this->rider()->id)
            ->with(['store', 'deliveryAttempts'])
            ->latest()
            ->get();

        return view('livewire.rider-order-sections', [
            'assignedOrders' => $orders->where('status', 'rider_assigned')->values(),
            'activeOrders' => $orders->whereIn('status', ['picked_up', 'out_for_delivery'])->values(),
            'completedOrders' => $orders->where('status', 'delivered')->values(),
            'rejectedOrders' => $orders->where('status', 'rejected')->values(),
            'allOrders' => $orders,
        ]);
    }
}
