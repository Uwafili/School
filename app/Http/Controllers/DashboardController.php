<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Order;
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

      return view('users.dashboard', compact('posts', 'orders'));
   }

}
