<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPaystackRefund;
use App\Models\DeliveryBid;
use App\Models\Message;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Store;
use App\Models\StoreRating;
use App\Services\NearbyOrderDispatcher;
use App\Services\OrderNotificationService;
use App\Services\OrderRiderAssignmentService;
use App\Services\OrderWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $rider = Rider::where('user_id', Auth::id())->latest()->first();
        if ($rider) {
            return redirect()->back()->with('error', 'You cannot create a store because you are registered as a rider. Please create a new account if you want to open a store.');
        }

        $validated = $request->validate([
            'stores' => ['required', 'max:255'],
            'owner' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'email'],
            'phone' => ['required', 'max:15', 'regex:/^\+?[0-9]{1,4}?[-. ]?([0-9]{1,3}[-. ]?){1,4}[0-9]{1,4}$/'],
            'address' => ['required', 'max:255'],
            'image' => ['file', 'nullable', 'mimes:jpg,png,jpeg,avif', 'max:3000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);
        $path = null;
        if ($request->hasFile('image')) {
            $path = Storage::disk('public')->put('post_image', $request->file('image'));
        }
        $store = Store::create([
            'stores' => $request->stores,
            'owner' => $request->owner,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'image' => $path,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'user_id' => Auth::id(),
            'status' => 'pending',

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
        $stores = Store::where('user_id', '=', $id)->get();

        // Get pending orders for this store
        $orders = Order::whereIn('store_id', $stores->pluck('id'))
            ->where('status', 'ready_for_pickup')
            ->where('payment_status', 'paid')
            ->get();

        $storeNotifications = Auth::user()->notifications()->latest()->limit(10)->get();
        $pendingApprovalOrders = Order::whereIn('store_id', $stores->pluck('id'))
            ->where('status', 'pending')
            ->where('store_approval_status', 'pending')
            ->with(['customer', 'messages' => fn ($query) => $query->where('receiver_id', $id)->whereNull('read_at')])
            ->latest()
            ->get();

        // Get approved riders
        $riders = Rider::with('user')->where('status', 'approved')->where('is_active', true)->get();
        $onlineRiders = $riders->where('is_online', true);
        $store = $stores->first();
        $nearbyRiders = $onlineRiders->filter(function ($rider) use ($store) {
            if (! $store?->latitude || ! $store?->longitude || ! $rider->latitude || ! $rider->longitude) {
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
            ->where('status', 'delivered')
            ->sum('total_price');

        // Week's orders and revenue
        $weekOrders = Order::whereIn('store_id', $storeIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $weekRevenue = Order::whereIn('store_id', $storeIds)
            ->where('status', 'delivered')
            ->where('created_at', '>=', now()->subDays(7))
            ->sum('total_price');

        // Active riders (approved and have accepted orders)
        $activeRiders = Rider::where('status', 'approved')
            ->where('is_active', true)
            ->whereIn('id', Order::whereIn('store_id', $storeIds)
                ->pluck('rider_id'))
            ->count();

        // Recent orders (last 10)
        $recentOrders = Order::whereIn('store_id', $storeIds)
            ->with(['rider.user', 'customer', 'deliveryBids.rider.user'])
            ->orderBy('created_at', 'desc')
            ->get();
        $unreadMessagesByOrder = Message::where('receiver_id', $id)
            ->whereNull('read_at')
            ->whereIn('order_id', $recentOrders->pluck('id')->merge($pendingApprovalOrders->pluck('id'))->unique())
            ->selectRaw('order_id, COUNT(*) as unread_count')
            ->groupBy('order_id')
            ->pluck('unread_count', 'order_id');
        $pendingApprovalOrders->each(fn (Order $order) => $order->setAttribute(
            'unread_message_count',
            (int) ($unreadMessagesByOrder[$order->id] ?? 0)
        ));
        $recentOrders->each(fn (Order $order) => $order->setAttribute(
            'unread_message_count',
            (int) ($unreadMessagesByOrder[$order->id] ?? 0)
        ));
        $unreadMessageCount = $unreadMessagesByOrder->sum();

        // Rider responses (orders with riders and their status)
        $riderResponses = Order::whereIn('store_id', $storeIds)
            ->whereNotNull('rider_id')
            ->with('rider')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $ratingStats = StoreRating::whereIn('store_id', $storeIds)
            ->selectRaw('COALESCE(AVG(rating), 0) as average, COUNT(*) as count')
            ->first();

        return view('enroll.storedashboard', [
            'stores' => $stores,
            'orders' => $orders,
            'pendingApprovalOrders' => $pendingApprovalOrders,
            'unreadMessageCount' => $unreadMessageCount,
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
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
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
    public function createOrder(Request $request, OrderNotificationService $notifications)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:15',
            'customer_address' => 'required|string|max:255',
            'items_description' => 'required|string',
        ]);

        $store = Store::where('user_id', Auth::id())->first();
        if (! $store) {
            return redirect()->back()->with('error', 'You need to have a store to create orders.');
        }

        $order = Order::create([
            'store_id' => $store->id,
            'customer_id' => null,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'],
            'total_price' => 0,
            'delivery_fee' => 0,
            'items_description' => $validated['items_description'],
            'status' => 'pending',
            'store_approval_status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $notifications->sendToUser(
            $store->user_id,
            $order,
            'New order draft created',
            "Order #{$order->id} has been created as an unpaid draft at {$store->stores}.",
            'order_created'
        );

        return redirect()->route('storedashboard')->with('success', 'Order created successfully!');
    }

    public function approveOrder(Order $order, OrderNotificationService $notifications, OrderWorkflowService $workflow)
    {
        $store = Store::where('user_id', Auth::id())->findOrFail($order->store_id);
        abort_unless($order->store_approval_status === 'pending', 422, 'This order is no longer waiting for approval.');
        $approvedOrder = $workflow->transition($order, 'approved', [
            'store_approval_status' => 'approved',
            'store_approved_at' => now(),
        ]);

        return back()->with('success', "Order #{$approvedOrder->id} approved.");
    }

    public function startPreparing(Order $order, OrderNotificationService $notifications, OrderWorkflowService $workflow)
    {
        Store::where('user_id', Auth::id())->findOrFail($order->store_id);
        $order = $workflow->transition($order, 'preparing');

        return back()->with('success', 'Order marked as preparing.');
    }

    public function markReadyForPickup(Order $order, OrderNotificationService $notifications, OrderWorkflowService $workflow, NearbyOrderDispatcher $dispatcher)
    {
        Store::where('user_id', Auth::id())->findOrFail($order->store_id);
        $order = $workflow->transition($order, 'ready_for_pickup');
        $dispatcher->dispatch($order);

        return back()->with('success', 'Order marked ready for pickup.');
    }

    public function rejectOrder(Request $request, Order $order, OrderNotificationService $notifications, OrderWorkflowService $workflow)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);
        $store = Store::where('user_id', Auth::id())->findOrFail($order->store_id);

        abort_unless($order->store_approval_status === 'pending', 422, 'This order is no longer waiting for approval.');
        $isPaid = $order->payment_status === 'paid';
        $rejectedOrder = $workflow->transition($order, 'rejected', [
            'store_approval_status' => 'rejected',
            'store_rejected_at' => now(),
            'store_rejection_reason' => $validated['reason'] ?? null,
            'refund_status' => $isPaid ? 'pending' : 'none',
            'payment_status' => $isPaid ? 'refund_pending' : $order->payment_status,
        ]);

        $reason = $validated['reason'] ?? null;
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
    public function assignRider(Request $request, OrderRiderAssignmentService $assignments)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rider_id' => 'required|exists:riders,id',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $isAdmin = Auth::user()->usertype === 'admin';
        $assignments->assign($order, (int) $validated['rider_id'], Auth::user());

        return redirect()->route($isAdmin ? 'riders' : 'storedashboard')->with('success', 'Order assigned to rider successfully! Rider notified.');
    }

    public function acceptBid($bidId)
    {
        $bid = DeliveryBid::with(['order', 'rider'])->findOrFail($bidId);
        $order = $bid->order;
        $store = Store::where('user_id', Auth::id())->findOrFail($order->store_id);

        if ($bid->status !== 'pending' || $order->status !== 'ready_for_pickup' || $order->rider_id
            || $order->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'This bid is no longer available.');
        }

        $order = app(OrderWorkflowService::class)->transition(
            $order,
            'rider_assigned',
            [
                'rider_id' => $bid->rider_id,
                'delivery_fee' => $bid->amount,
                'recipient_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            ],
            function (Order $lockedOrder) use ($bid, $store): bool {
                $lockedBid = DeliveryBid::query()->lockForUpdate()->find($bid->id);
                $rider = $lockedBid ? Rider::query()->lockForUpdate()->find($lockedBid->rider_id) : null;
                if ($lockedOrder->store_id !== $store->id
                    || ! $lockedBid || $lockedBid->status !== 'pending' || ! $rider
                    || $rider->status !== 'approved' || ! $rider->is_active || ! $rider->is_online
                    || $lockedOrder->payment_status !== 'paid' || $lockedOrder->rider_id) {
                    return false;
                }

                $lockedBid->update(['status' => 'accepted']);
                $lockedOrder->deliveryBids()->whereKeyNot($lockedBid->id)->where('status', 'pending')->update(['status' => 'rejected']);

                return true;
            }
        );
        $notifications = app(OrderNotificationService::class);
        $notifications->sendToRider($order, 'Delivery bid accepted', "Your bid for order #{$order->id} was accepted. Delivery pay: ₦{$bid->amount}", 'order_assigned');

        return redirect()->route('storedashboard')->with('success', 'Rider bid accepted and order assigned.');
    }

    /**
     * View order details
     */
    public function viewOrder($orderId)
    {
        $order = Order::with('rider', 'store', 'customer')->findOrFail($orderId);
        $this->authorize('view', $order);

        return view('enroll.order_details', compact('order'));
    }

    /**
     * Cancel an order
     */
    public function cancelOrder(Request $request, $orderId, OrderNotificationService $notifications, OrderWorkflowService $workflow)
    {
        $order = Order::findOrFail($orderId);

        if ($order->status === 'rider_assigned') {
            $store = Store::where('user_id', Auth::id())->findOrFail($order->store_id);
            $isPaid = $order->payment_status === 'paid';
            $cancelledOrder = $workflow->transition($order, 'cancelled', [
                'rider_id' => null,
                'recipient_code' => null,
                'refund_status' => $isPaid ? 'pending' : 'none',
                'payment_status' => $isPaid ? 'refund_pending' : $order->payment_status,
            ], fn (Order $lockedOrder): bool => $lockedOrder->store_id === $store->id
                    && $lockedOrder->status === 'rider_assigned'
                    && $lockedOrder->picked_up_at === null
            );

            if ($cancelledOrder->payment_status === 'refund_pending') {
                ProcessPaystackRefund::dispatch($cancelledOrder->id);
            }

            return back()->with('success', 'Delivery cancelled. The assigned rider has been notified.');
        }

        $request->merge(['reason' => $request->input('reason', 'Store declined the order.')]);

        return $this->rejectOrder($request, $order, $notifications, $workflow);
    }
}
