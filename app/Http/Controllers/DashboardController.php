<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Order;
use App\Models\Store;
use App\Models\Rider;
use App\Models\StoreRating;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
   public function index(){

      $posts=Post::with('user')->latest()->get();
      $orders = Order::with(['store.user', 'rider.user'])
         ->where(function ($query) {
            $query->where('customer_id', Auth::id())
               ->orWhere(function ($legacy) {
                  $legacy->whereNull('customer_id')
                     ->where('customer_name', Auth::user()->name);
               });
         })
         ->whereNotIn('status', ['cancelled', 'rejected'])
         ->latest()
         ->get();

      $stores = Store::whereNotNull('latitude')
         ->whereNotNull('longitude')
         ->where('status', 'approved')
         ->get(['id', 'stores', 'latitude', 'longitude']);
      $riders = Rider::with('user')
         ->where('status', 'approved')
         ->where('is_online', true)
         ->whereNotNull('latitude')
         ->whereNotNull('longitude')
         ->get();
      $ratings = StoreRating::where('customer_id', Auth::id())
         ->whereIn('order_id', $orders->pluck('id'))
         ->get()
         ->keyBy('order_id');

      return view('users.dashboard', compact('posts', 'orders', 'stores', 'riders', 'ratings'));
   }

   public function liveRiderLocations()
   {
      $orders = Order::where(function ($query) {
            $query->where('customer_id', Auth::id())
               ->orWhere(fn ($legacy) => $legacy->whereNull('customer_id')->where('customer_name', Auth::user()->name));
         })
         ->where('status', 'accepted')
         ->whereNotNull('picked_up_at')
         ->whereNotNull('rider_id')
         ->with(['rider.user', 'store'])
         ->get();

      $customer = Auth::user();

      return response()->json($orders->map(fn ($order) => [
         'order_id' => $order->id,
         'status' => $order->status,
         'name' => $order->rider?->user?->name ?? $order->rider?->name ?? 'Your rider',
         'latitude' => $order->rider?->latitude,
         'longitude' => $order->rider?->longitude,
         'updated_at' => $order->rider?->location_updated_at?->toIso8601String(),
         'pickup' => [
            'latitude' => $order->store?->latitude,
            'longitude' => $order->store?->longitude,
            'name' => $order->store?->stores ?? 'Pickup store',
         ],
         'destination' => [
            'latitude' => $customer->latitude,
            'longitude' => $customer->longitude,
            'name' => 'Your delivery location',
         ],
      ])->values());
   }

}
