<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Rider;
use App\Models\Store;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->usertype === 'admin'
            || $order->customer_id === $user->id
            || Store::where('id', $order->store_id)->where('user_id', $user->id)->exists()
            || Rider::where('id', $order->rider_id)->where('user_id', $user->id)->exists();
    }

    public function viewAsAssignedRider(User $user, Order $order): bool
    {
        return Rider::where('id', $order->rider_id)->where('user_id', $user->id)->exists();
    }
}
