<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Order;
use App\Models\Notification as UserNotification;
use App\Models\Store;
use App\Models\Rider;
use App\Models\StoreRating;
use App\Models\Message;
use App\Jobs\ProcessPaystackRefund;
use App\Services\OrderNotificationService;
use App\Services\OrderWorkflowService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
   public function index(){

      $posts = Post::with('user')->latest()->paginate(8)->fragment('food-feed');
      $topStore = Store::query()
         ->where('status', 'approved')
         ->whereExists(fn ($query) => $query->selectRaw('1')->from('posts')->whereColumn('posts.user_id', 'stores.user_id'))
         ->orderByDesc(Post::query()->selectRaw('COUNT(*)')->whereColumn('posts.user_id', 'stores.user_id'))
         ->first();

      $featuredProducts = Post::with('user')
         ->when($topStore, fn ($query) => $query->where('user_id', $topStore->user_id))
         ->latest()
         ->limit(5)
         ->get();

      if ($featuredProducts->isEmpty()) {
         $featuredProducts = Post::with('user')->latest()->limit(5)->get();
      }

      $featuredSlides = $featuredProducts->map(function (Post $post) use ($topStore) {
         $fallbackImages = [
            'pizza' => 'generated.jpg',
            'burger' => 'front.avif',
            'salad' => 'brown.jpg',
            'drinks' => 'drink.webp',
         ];
         $image = $post->image
            ? asset('storage/' . $post->image)
            : asset('asset/' . ($fallbackImages[$post->category] ?? 'generated.jpg'));

         return [
            'id' => $post->id,
            'title' => $post->title,
            'description' => $post->description,
            'category' => ucfirst($post->category ?? 'Featured'),
            'price' => $post->price,
            'store' => $topStore?->stores ?? ($post->user?->name ?? 'FoodStore picks'),
            'image' => $image,
            'url' => route('food.view', $post),
         ];
      });

      $orders = Order::with(['store.user', 'rider.user'])
         ->where('customer_id', Auth::id())
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
      $orderNotifications = UserNotification::where('user_id', Auth::id())
         ->whereNotNull('order_id')
         ->latest()
         ->limit(10)
         ->get();
      $unreadMessagesByOrder = Message::where('receiver_id', Auth::id())
         ->whereNull('read_at')
         ->whereIn('order_id', $orders->pluck('id'))
         ->selectRaw('order_id, COUNT(*) as unread_count')
         ->groupBy('order_id')
         ->pluck('unread_count', 'order_id');

      return view('users.dashboard', compact('posts', 'orders', 'stores', 'riders', 'ratings', 'featuredSlides', 'topStore', 'orderNotifications', 'unreadMessagesByOrder'));
   }

   public function cancelOrder(Order $order, OrderNotificationService $notifications, OrderWorkflowService $workflow)
   {
      if ($order->status !== 'pending' || $order->store_approval_status !== 'pending') {
         return back()->with('error', 'Orders can only be cancelled while waiting for store approval.');
      }

      $isPaid = $order->payment_status === 'paid';
      $cancelledOrder = $workflow->transition($order, 'cancelled', [
            'refund_status' => $isPaid ? 'pending' : 'none',
            'payment_status' => $isPaid ? 'refund_pending' : $order->payment_status,
         ], fn (Order $lockedOrder): bool =>
            $lockedOrder->customer_id === Auth::id()
               && $lockedOrder->store_approval_status === 'pending'
               && $lockedOrder->payment_status === $order->payment_status
         );

      $notifications->sendToStore(
         $cancelledOrder,
         'Customer cancelled an order',
         "Order #{$cancelledOrder->id} was cancelled by the customer.",
         'order_cancelled'
      );
      if ($cancelledOrder->payment_status === 'refund_pending') {
         ProcessPaystackRefund::dispatch($cancelledOrder->id);
      }

      return back()->with('success', 'Order cancelled.');
   }

   public function showOrder(Order $order)
   {
      abort_unless($order->customer_id === Auth::id(), 403);

      $order->load(['store.user', 'rider.user']);

      return view('users.order-details', compact('order'));
   }

   public function markNotificationAsRead($notificationId)
   {
      $notification = UserNotification::where('user_id', Auth::id())->findOrFail($notificationId);
      $notification->markAsRead();

      return back();
   }

   public function liveRiderLocations()
   {
      $orders = Order::where('customer_id', Auth::id())
         ->whereIn('status', ['picked_up', 'out_for_delivery'])
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
