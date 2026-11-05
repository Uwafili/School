<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Rider;
use App\Jobs\ProcessPaystackRefund;
use App\Services\NearbyOrderDispatcher;
use App\Services\OrderNotificationService;




class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $store = Auth::check()
            ? Store::where('user_id', Auth::id())->latest()->first()
            : null;

        return view('enroll.store', compact('store'));
    } 

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user is already a registered rider
        $rider = \App\Models\Rider::where('user_id', Auth::id())->latest()->first();
        if ($rider) {
            return redirect()->back()->with('error', 'You cannot create a store because you are registered as a rider. Please create a new account if you want to open a store.');
        }

        $validated=$request->validate([
             'stores'=>['required', 'max:255'],
             'owner'=>['required', 'max:255'],
             'email'=>['required','max:255', 'email'],
             'phone'=>['required', 'max:15', 'regex:/^\+?[0-9]{1,4}?[-. ]?([0-9]{1,3}[-. ]?){1,4}[0-9]{1,4}$/'],
             'address'=>['required', 'max:255'],
             'image'=>['file', 'nullable', 'mimes:jpg,png,jpeg,avif','max:3000'],
             'latitude'=>['nullable','numeric','between:-90,90'],
             'longitude'=>['nullable','numeric','between:-180,180'],
        ]);
        $path=null;
        if($request->hasFile('image')){
            $path=Storage::disk('public')->put('post_image', $request->file('image'));
        }
        $store=Store::create([
            'stores'=> $request->stores,
            'owner' => $request->owner,
            'email' => $request->email,
            'phone' => $request->phone,
            'address'=> $request->address,
            'image'=> $path,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            "user_id"=>Auth::id(),
            'status' => 'pending'

        ]);


        if ($store) {
            return redirect()->route('store.info')->with('success', 'Store submitted successfully. Awaiting admin approval.');
        } else {
            return redirect()->back()->with('failed', 'Failed to create store.');
        }
         
    }

    /**
     * Display the specified resource.
     */
    public function Storedashboard()
    {
        $id = Auth::id();
        $stores = Store::where("user_id", '=', $id)->get();
        
        // Get pending orders for this store
        $orders = Order::whereIn('store_id', $stores->pluck('id'))
            ->where('status', 'pending')
            ->where('store_approval_status', 'approved')
            ->where('payment_status', 'paid')
            ->get();

        $storeNotifications = Notification::where('user_id', $id)
            ->with('order')
            ->latest()
            ->limit(10)
            ->get();
        $pendingApprovalOrders = Order::whereIn('store_id', $stores->pluck('id'))
            ->where('status', 'pending')
            ->where('store_approval_status', 'pending')
            ->with('customer')
            ->latest()
            ->get();
        
        // Get approved riders
        $riders = Rider::with('user')->where('status', 'approved')->get();
        $onlineRiders = $riders->where('is_online', true);
        $store = $stores->first();
        $nearbyRiders = $onlineRiders->filter(function ($rider) use ($store) {
            if (!$store?->latitude || !$store?->longitude || !$rider->latitude || !$rider->longitude) {
                return false;
            }

            $distance = $this->distanceInKilometres($store->latitude, $store->longitude, $rider->latitude, $rider->longitude);
            $rider->distance_km = round($distance, 1);

            return $distance <= 25;
        })->sortBy('distance_km')->values();
        
        // Calculate statistics
        $storeIds = $stores->pluck('id');
        
        // Total orders
        $totalOrders = Order::whereIn('store_id', $storeIds)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->count();
        
        // Revenue is earned only after delivery is completed.
        $totalRevenue = Order::whereIn('store_id', $storeIds)
            ->where('status', 'completed')
            ->sum('total_price');
        
        // Week's orders and revenue
        $weekOrders = Order::whereIn('store_id', $storeIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        $weekRevenue = Order::whereIn('store_id', $storeIds)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->sum('total_price');
        
        // Active riders (approved and have accepted orders)
        $activeRiders = Rider::where('status', 'approved')
            ->whereIn('id', Order::whereIn('store_id', $storeIds)
                ->pluck('rider_id'))
            ->count();
        
        // Recent orders (last 10)
        $recentOrders = Order::whereIn('store_id', $storeIds)
            ->with(['rider.user', 'customer', 'deliveryBids.rider.user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Rider responses (orders with riders and their status)
        $riderResponses = Order::whereIn('store_id', $storeIds)
            ->whereNotNull('rider_id')
            ->with('rider')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $ratingStats = \App\Models\StoreRating::whereIn('store_id', $storeIds)
            ->selectRaw('COALESCE(AVG(rating), 0) as average, COUNT(*) as count')
            ->first();
        
        return view('enroll.storedashboard', [
            'stores' => $stores,
            'orders' => $orders,
            'pendingApprovalOrders' => $pendingApprovalOrders,
            'storeNotifications' => $storeNotifications,
            'riders' => $onlineRiders,
            'nearbyRiders' => $nearbyRiders,
            'allRiders' => $riders,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'weekOrders' => $weekOrders,
            'weekRevenue' => $weekRevenue,
            'activeRiders' => $activeRiders,
            'recentOrders' => $recentOrders,
            'riderResponses' => $riderResponses,
            'averageRating' => (float) ($ratingStats->average ?? 0),
            'ratingCount' => (int) ($ratingStats->count ?? 0),
        ]);
    }

    public function markStoreNotificationAsRead($notificationId)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($notificationId);
        $notification->markAsRead();

        return back()->with('success', 'Order notification marked as read.');
    }

    private function distanceInKilometres(float $latitudeOne, float $longitudeOne, float $latitudeTwo, float $longitudeTwo): float
    {
        $earthRadius = 6371;
        $latitudeDelta = deg2rad($latitudeTwo - $latitudeOne);
        $longitudeDelta = deg2rad($longitudeTwo - $longitudeOne);
        $a = sin($latitudeDelta / 2) ** 2
            + cos(deg2rad($latitudeOne)) * cos(deg2rad($latitudeTwo)) * sin($longitudeDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Display store approval status and information
     */
    public function displayStore()
    {
        $store = Store::where('user_id', Auth::id())->latest()->first();
        return view('enroll.store', ['store' => $store]);
    }

    /**
     * Show detailed store information  
     */
    public function show(Store $store)
    {
        // Check if the store belongs to the authenticated user
        if ($store->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('enroll.storedetails', ['store' => $store]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        //
    }

    /**
     * Create a new order
     */
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:15',
            'customer_address' => 'required|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'delivery_fee' => 'required|numeric|min:0',
            'items_description' => 'required|string',
        ]);

        $store = Store::where('user_id', Auth::id())->first();
        if (!$store) {
            return redirect()->back()->with('error', 'You need to have a store to create orders.');
        }

        $order = \App\Models\Order::create([
            'store_id' => $store->id,
            'customer_id' => null,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'],
            'total_price' => $validated['total_price'],
            'delivery_fee' => $validated['delivery_fee'],
            'items_description' => $validated['items_description'],
            'status' => 'pending',
            'store_approval_status' => 'approved',
        ]);

        Notification::create([
            'user_id' => $store->user_id,
            'order_id' => $order->id,
            'title' => 'New food order received',
            'message' => "Order #{$order->id} has been placed at {$store->stores}.",
            'type' => 'order_created',
        ]);

        return redirect()->route('storedashboard')->with('success', 'Order created successfully!');
    }

    public function approveOrder(Order $order, OrderNotificationService $notifications, NearbyOrderDispatcher $dispatcher)
    {
        $store = Store::where('user_id', Auth::id())->findOrFail($order->store_id);

        $approvedOrder = DB::transaction(function () use ($order, $store) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->store_id === $store->id, 403);

            if ($lockedOrder->status !== 'pending' || $lockedOrder->store_approval_status !== 'pending') {
                return null;
            }

            $lockedOrder->update([
                'store_approval_status' => 'approved',
                'store_approved_at' => now(),
            ]);

            return $lockedOrder->fresh(['store']);
        });

        if (! $approvedOrder) {
            return back()->with('error', 'This order is no longer waiting for approval.');
        }

        $notifications->sendToCustomer(
            $approvedOrder,
            'Store approved your order',
            "The store approved order #{$approvedOrder->id} and it is now available for delivery assignment.",
            'order_approved'
        );
        $dispatcher->dispatch($approvedOrder);

        return back()->with('success', "Order #{$approvedOrder->id} approved.");
    }

    public function rejectOrder(Request $request, Order $order, OrderNotificationService $notifications)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);
        $store = Store::where('user_id', Auth::id())->findOrFail($order->store_id);

        $rejectedOrder = DB::transaction(function () use ($order, $store, $validated) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->store_id === $store->id, 403);

            if ($lockedOrder->status !== 'pending' || $lockedOrder->store_approval_status !== 'pending') {
                return null;
            }

            $isPaid = $lockedOrder->payment_status === 'paid';
            $lockedOrder->update([
                'status' => 'rejected',
                'store_approval_status' => 'rejected',
                'store_rejected_at' => now(),
                'store_rejection_reason' => $validated['reason'] ?? null,
                'refund_status' => $isPaid ? 'pending' : 'none',
                'payment_status' => $isPaid ? 'refund_pending' : $lockedOrder->payment_status,
            ]);

            return $lockedOrder->fresh();
        });

        if (! $rejectedOrder) {
            return back()->with('error', 'This order is no longer waiting for approval.');
        }

        $reason = $validated['reason'] ?? null;
        $notifications->sendToCustomer(
            $rejectedOrder,
            'Store could not accept your order',
            "Order #{$rejectedOrder->id} was rejected by the store."
                . ($reason ? " Reason: {$reason}" : '')
                . ($rejectedOrder->payment_status === 'refund_pending' ? ' A refund is being arranged.' : ''),
            'order_rejected'
        );
        if ($rejectedOrder->payment_status === 'refund_pending') {
            ProcessPaystackRefund::dispatch($rejectedOrder->id);
        }

        return back()->with('success', "Order #{$rejectedOrder->id} rejected.");
    }

    public function retryRefund(Order $order)
    {
        Store::where('user_id', Auth::id())->findOrFail($order->store_id);
        abort_unless(
            in_array($order->status, ['rejected', 'cancelled'], true)
                && $order->refund_status === 'pending'
                && $order->payment_reference
                && ! $order->refund_reference,
            422,
            'This order does not have a retryable refund.'
        );

        ProcessPaystackRefund::dispatch($order->id);

        return back()->with('success', 'Refund retry queued. Check its status before trying again.');
    }

    /**
     * Assign an order to a rider
     */
    public function assignRider(Request $request, OrderNotificationService $notifications)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rider_id' => 'required|exists:riders,id',
            'delivery_fee' => 'nullable|numeric|min:0',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $store = Store::where('user_id', Auth::id())->findOrFail($order->store_id);

        // Update the order with rider and status
        $rider = \App\Models\Rider::where('id', $validated['rider_id'])
            ->where('status', 'approved')
            ->where('is_online', true)
            ->first();

        if (!$rider) {
            return redirect()->back()->with('error', 'Choose an approved rider who is currently online.');
        }

        if ($rider->latitude !== null && $rider->longitude !== null && $store->latitude !== null && $store->longitude !== null
            && $this->distanceInKilometres($store->latitude, $store->longitude, $rider->latitude, $rider->longitude) > 25) {
            return redirect()->back()->with('error', 'Choose a rider within 25 km of your store.');
        }

        $deliveryFee = $validated['delivery_fee'] ?? $order->delivery_fee;

        DB::transaction(function () use ($order, $store, $rider, $deliveryFee, $notifications) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $lockedRider = Rider::query()->lockForUpdate()->findOrFail($rider->id);
            abort_unless(
                $lockedOrder->store_id === $store->id
                    && $lockedOrder->status === 'pending'
                    && $lockedOrder->store_approval_status === 'approved'
                    && $lockedOrder->payment_status === 'paid'
                    && ! $lockedOrder->rider_id
                    && $lockedRider->status === 'approved'
                    && $lockedRider->is_online,
                422,
                'This order is not ready for rider assignment.'
            );

            $lockedOrder->update([
                'rider_id' => $rider->id,
                'status' => 'assigned',
                'recipient_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
                'delivery_fee' => $deliveryFee,
            ]);
            $notifications->sendToRider(
                $lockedOrder,
                'New order assigned',
                "You have been assigned order #{$lockedOrder->id} from {$store->stores}. Customer: {$lockedOrder->customer_name}, Address: {$lockedOrder->customer_address}, Delivery pay: ₦{$deliveryFee}",
                'order_assigned'
            );
            $notifications->sendToCustomer(
                $lockedOrder,
                'Rider assigned',
                "A rider has been assigned to your order #{$lockedOrder->id}.",
                'rider_assigned'
            );
        });

        return redirect()->route('storedashboard')->with('success', 'Order assigned to rider successfully! Rider notified.');
    }

    public function acceptBid(Request $request, $bidId)
    {
        $bid = \App\Models\DeliveryBid::with(['order', 'rider'])->findOrFail($bidId);
        $order = $bid->order;
        $store = Store::where('user_id', Auth::id())->findOrFail($order->store_id);

        if ($bid->status !== 'pending' || $order->status !== 'pending' || $order->rider_id
            || $order->store_approval_status !== 'approved' || $order->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'This bid is no longer available.');
        }

        DB::transaction(function () use ($bid, $order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $lockedBid = \App\Models\DeliveryBid::query()->lockForUpdate()->findOrFail($bid->id);
            $rider = Rider::query()->lockForUpdate()->findOrFail($lockedBid->rider_id);
            abort_unless(
                $lockedOrder->store_approval_status === 'approved'
                    && $lockedOrder->payment_status === 'paid'
                    && $lockedOrder->status === 'pending'
                    && ! $lockedOrder->rider_id
                    && $lockedBid->status === 'pending'
                    && $rider->status === 'approved'
                    && $rider->is_online,
                422,
                'This bid is no longer available.'
            );

            $lockedOrder->update([
                'rider_id' => $lockedBid->rider_id,
                'delivery_fee' => $lockedBid->amount,
                'status' => 'assigned',
                'recipient_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            ]);
            $lockedBid->update(['status' => 'accepted']);
            $lockedOrder->deliveryBids()->whereKeyNot($lockedBid->id)->where('status', 'pending')->update(['status' => 'rejected']);
        });

        $order->refresh();
        $notifications = app(OrderNotificationService::class);
        $notifications->sendToRider($order, 'Delivery bid accepted', "Your bid for order #{$order->id} was accepted. Delivery pay: ₦{$bid->amount}", 'order_assigned');
        $notifications->sendToCustomer($order, 'Rider assigned', "A rider has been assigned to your order #{$order->id}.", 'rider_assigned');

        return redirect()->route('storedashboard')->with('success', 'Rider bid accepted and order assigned.');
    }

    /**
     * View order details
     */
    public function viewOrder($orderId)
    {
        $order = Order::with('rider', 'store', 'customer')->findOrFail($orderId);
        
        // Verify the order belongs to the authenticated store
        $store = Store::where('user_id', Auth::id())->whereKey($order->store_id)->exists();
        if (!$store) {
            abort(403, 'Unauthorized');
        }

        return view('enroll.order_details', compact('order'));
    }

    /**
     * Complete an order
     */
    public function completeOrder($orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        
        // Verify the order belongs to the authenticated store
        $store = Store::where('user_id', Auth::id())->whereKey($order->store_id)->exists();
        if (!$store) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        return redirect()->route('storedashboard')->with('error', 'Only the assigned rider can complete a delivery.');
    }

    /**
     * Cancel an order
     */
    public function cancelOrder(Request $request, $orderId, OrderNotificationService $notifications)
    {
        $order = Order::findOrFail($orderId);
        $request->merge(['reason' => $request->input('reason', 'Store declined the order.')]);

        return $this->rejectOrder($request, $order, $notifications);
    }
}
