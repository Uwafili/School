<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Order;
use App\Models\Store;
use App\Models\Rider;
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

      return view('users.dashboard', compact('posts', 'orders', 'stores', 'riders'));
   }

}
