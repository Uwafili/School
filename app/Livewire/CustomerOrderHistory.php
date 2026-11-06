<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Order;
use App\Models\StoreRating;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CustomerOrderHistory extends Component
{
    #[On('order-status-updated')]
    public function refreshOrders(?int $orderId = null): void {}

    public function render()
    {
        $customerId = Auth::id();
        $orders = Order::query()
            ->where('customer_id', $customerId)
            ->with(['store', 'rider.user'])
            ->latest()
            ->get();
        $orderIds = $orders->pluck('id');
        $ratings = StoreRating::where('customer_id', $customerId)
            ->whereIn('order_id', $orderIds)
            ->get()
            ->keyBy('order_id');
        $unreadMessagesByOrder = Message::where('receiver_id', $customerId)
            ->whereNull('read_at')
            ->whereIn('order_id', $orderIds)
            ->selectRaw('order_id, COUNT(*) as unread_count')
            ->groupBy('order_id')
            ->pluck('unread_count', 'order_id');

        return view('livewire.customer-order-history', compact('orders', 'ratings', 'unreadMessagesByOrder'));
    }
}
