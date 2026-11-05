<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Rider;
use App\Models\DeliveryBid;
use App\Models\Order;
use App\Models\Notification;
use App\Services\OrderNotificationService;
use Illuminate\Support\Facades\DB;

class RiderController extends Controller
{
    /**
     * Show the rider form and their information if approved
     */
    public function create()
    {
        $rider = null;
        if (Auth::check()) {
            $rider = Rider::where('user_id', Auth::id())->latest()->first();
        }
        return view('enroll.rider', compact('rider'));
    }

    /**
     * Store a newly created/updated rider in storage
     */
    public function store(Request $request)
    {
        // Check if user already owns a store
        $store = \App\Models\Store::where('user_id', Auth::id())->first();
        if ($store) {
            return redirect()->back()->with('error', 'You cannot register as a rider because you already own a store. Please create a new account if you want to become a rider.');
        }

        $request->validate([
            'name' => ['required','max:255'],
            'email' => ['required','email','max:225'],
            'phone'=>['required','max:15','regex:/^\+?[0-9]{1,4}?[-. ]?([0-9]{1,3}[-. ]?){1,4}[0-9]{1,4}$/'],
            'license' => ['required','max:255',],
            'vehicle_number'=>['required','max:15','regex:/^\+?[0-9]{1,4}?[-. ]?([0-9]{1,3}[-. ]?){1,4}[0-9]{1,4}$/'],
            'vehicle'=>['required','max:255'],
            'image'=>['file','nullable','mimes:jpg,png,jpeg,avif','max:3000'],
            'latitude'=>['nullable','numeric','between:-90,90'],
            'longitude'=>['nullable','numeric','between:-180,180'],
        ]);

        $path = null;
        
        if($request->hasFile('image')){
            $path = Storage::disk('public')->put('post_image', $request->file('image'));
        }

        // Create new rider record
        Rider::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'license' => $request->license,
            'vehicle_number' => $request->vehicle_number,
            'vehicle' => $request->vehicle,
            'image' => $path, 
            'user_id' => Auth::id(),
            'status' => 'pending',
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);
    
        return redirect()->route('rider.create')->with('success', 'Your details have been submitted. Admin will review and approve soon.');
    }

    /**
     * Show the rider dashboard (approved riders only)
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $Rider = Rider::where('user_id', Auth::id())->latest()->first();

        if (!$Rider) {
            return redirect()->route('rider.create')->with('error', 'Please complete your rider registration first.');
        }

        if ($Rider->status !== 'approved') {
            return redirect()->route('rider.create')->with('warning', 'Your application is still under review. Please check back later.');
        }

        $orders = $this->assignedOrdersForRider($Rider);
        $nearbyOrders = $this->nearbyOrdersForRider($Rider);
        $assignedCount = $orders->where('status', 'assigned')->count() + $nearbyOrders->count();
        $activeCount = $orders->whereIn('status', ['accepted'])->count();
        $completedCount = $orders->where('status', 'completed')->count();

        return view('enroll.ridersdashboard', compact('Rider', 'orders', 'nearbyOrders', 'assignedCount', 'activeCount', 'completedCount'));
    }

    private function assignedOrdersForRider(Rider $rider)
    {
        return Order::where('rider_id', $rider->id)
            ->with(['store', 'customer', 'deliveryBids' => fn ($query) => $query->where('rider_id', $rider->id)])
            ->latest()
            ->get();
    }

    private function nearbyOrdersForRider(Rider $rider)
    {
        if (!$rider->is_online || $rider->latitude === null || $rider->longitude === null) {
            return collect();
        }

        $radius = (float) config('services.delivery.radius_km', 25);
        $latitude = (float) $rider->latitude;
        $longitude = (float) $rider->longitude;
        $latitudeRange = $radius / 111.045;
        $longitudeRange = $radius / max(111.045 * abs(cos(deg2rad($latitude))), 0.01);

        return Order::whereNull('rider_id')
            ->where('status', 'pending')
            ->where('payment_status', 'paid')
            ->where('store_approval_status', 'approved')
            ->whereHas('store', function ($query) use ($latitude, $longitude, $latitudeRange, $longitudeRange) {
                $query->where('status', 'approved')
                    ->whereBetween('latitude', [$latitude - $latitudeRange, $latitude + $latitudeRange])
                    ->whereBetween('longitude', [$longitude - $longitudeRange, $longitude + $longitudeRange]);
            })
            ->with(['store', 'deliveryBids' => fn ($query) => $query->where('rider_id', $rider->id)])
            ->latest()
            ->get()
            ->map(function ($order) use ($latitude, $longitude, $radius) {
                $distance = $this->distanceInKilometres(
                    $latitude,
                    $longitude,
                    (float) $order->store->latitude,
                    (float) $order->store->longitude
                );
                $order->distance_km = round($distance, 1);
                $order->within_radius = $distance <= $radius;

                return $order;
            })
            ->filter(fn ($order) => $order->within_radius)
            ->values();
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

    public function placeBid(Request $request, $orderId, OrderNotificationService $notifications)
    {
        $rider = Rider::where('user_id', Auth::id())->where('status', 'approved')->firstOrFail();
        $validated = $request->validate(['amount' => ['required', 'numeric', 'min:0', 'max:1000000']]);
        $order = Order::whereKey($orderId)->firstOrFail();

        if (!$this->nearbyOrdersForRider($rider)->contains('id', $order->id)) {
            return back()->with('error', 'This order is no longer available in your delivery area.');
        }

        DB::transaction(function () use ($order, $rider, $validated, $notifications) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $lockedRider = Rider::query()->lockForUpdate()->findOrFail($rider->id);
            abort_unless(
                ! $lockedOrder->rider_id
                    && $lockedOrder->status === 'pending'
                    && $lockedOrder->payment_status === 'paid'
                    && $lockedOrder->store_approval_status === 'approved'
                    && $lockedRider->status === 'approved'
                    && $lockedRider->is_online,
                422,
                'This order is no longer available.'
            );

            DeliveryBid::updateOrCreate(
                ['order_id' => $lockedOrder->id, 'rider_id' => $lockedRider->id],
                ['amount' => $validated['amount'], 'status' => 'pending']
            );
            $notifications->sendToStore(
                $lockedOrder,
                'New rider offer',
                "A rider offered ₦{$validated['amount']} for order #{$lockedOrder->id}.",
                'order_bid'
            );
        });

        return back()->with('success', 'Your delivery request was sent to the store.');
    }

    public function toggleAvailability(Request $request)
    {
        $rider = Rider::where('user_id', Auth::id())->where('status', 'approved')->firstOrFail();
        $rider->update(['is_online' => $request->boolean('is_online')]);

        return back()->with('success', $rider->is_online ? 'You are now online and can receive jobs.' : 'You are now offline.');
    }

    public function confirmPickup($orderId, OrderNotificationService $notifications)
    {
        $rider = Rider::where('user_id', Auth::id())->where('status', 'approved')->firstOrFail();
        $order = Order::where('id', $orderId)->where('rider_id', $rider->id)->firstOrFail();

        if ($order->status !== 'accepted' || $order->picked_up_at) {
            return back()->with('error', 'Accept the order before confirming pickup.');
        }
        DB::transaction(function () use ($order, $rider) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->rider_id === $rider->id && $lockedOrder->status === 'accepted' && ! $lockedOrder->picked_up_at, 422);
            $lockedOrder->update([
                'picked_up_at' => now(),
                'notes' => trim(($lockedOrder->notes ? $lockedOrder->notes . "\n" : '') . 'Picked up by rider at ' . now()->toDateTimeString()),
            ]);
        });

        $order->refresh();
        $notifications->sendToCustomer($order, 'Order picked up', "The rider picked up order #{$order->id}.", 'order_picked_up');
        $notifications->sendToStore($order, 'Order picked up', "The rider picked up order #{$order->id}.", 'order_picked_up');

        return back()->with('success', 'Pickup confirmed. Take the order to the customer.');
    }

    public function confirmDelivery(Request $request, $orderId, OrderNotificationService $notifications)
    {
        $rider = Rider::where('user_id', Auth::id())->where('status', 'approved')->firstOrFail();
        $order = Order::where('id', $orderId)->where('rider_id', $rider->id)->firstOrFail();
        $request->validate(['recipient_code' => ['required', 'digits:4']]);

        if ($order->status !== 'accepted') {
            return back()->with('error', 'This order is not ready for delivery confirmation.');
        }
        if (!$order->recipient_code || !hash_equals((string) $order->recipient_code, (string) $request->recipient_code)) {
            return back()->with('error', 'The recipient code is incorrect. Ask the customer to show the code from their order.');
        }

        DB::transaction(function () use ($order, $rider, $request) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless(
                $lockedOrder->rider_id === $rider->id
                    && $lockedOrder->status === 'accepted'
                    && $lockedOrder->picked_up_at
                    && $lockedOrder->recipient_code
                    && hash_equals((string) $lockedOrder->recipient_code, (string) $request->input('recipient_code')),
                422,
                'This order is not ready for delivery confirmation.'
            );
            $lockedOrder->update(['status' => 'completed', 'recipient_verified_at' => now()]);
        });
        $order->refresh();
        $notifications->sendToCustomer($order, 'Order delivered', "Order #{$order->id} was delivered successfully.", 'order_completed');
        $notifications->sendToStore($order, 'Order delivered', "Order #{$order->id} was delivered successfully.", 'order_completed');

        return back()->with('success', 'Delivery confirmed. Recipient verified successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rider = Rider::findOrFail($id);
        return view('enroll.rider', compact('rider'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rider $rider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rider $rider)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rider $rider)
    {
        //
    }

    /**
     * Display all notifications for the authenticated rider
     */
    public function notifications()
    {
        $rider = Rider::where('user_id', Auth::id())->where('status', 'approved')->first();
        
        if (!$rider) {
            return redirect()->route('rider.create')->with('error', 'Please complete your rider registration first.');
        }

        if ($rider->status !== 'approved') {
            return redirect()->route('rider.create')->with('warning', 'Your application is still under review.');
        }

        $notifications = \App\Models\Notification::where('rider_id', $rider->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unreadCount = \App\Models\Notification::where('rider_id', $rider->id)
            ->where('is_read', false)
            ->count();

        return view('enroll.rider_notifications', compact('notifications', 'unreadCount', 'rider'));
    }

    public function markNotificationAsRead($notificationId)
    {
        $rider = Rider::where('user_id', Auth::id())->where('status', 'approved')->firstOrFail();
        $notification = Notification::where('rider_id', $rider->id)->findOrFail($notificationId);
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }
        
    public function getUnreadCount()
    {
        $rider = Rider::where('user_id', Auth::id())->first();
        
        if (!$rider) {
            return response()->json(['count' => 0]);
        }

        $count = \App\Models\Notification::where('rider_id', $rider->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Display assigned orders for the rider
     */
    public function assignedOrders()
    {
        $rider = Rider::where('user_id', Auth::id())->first();
        
        if (!$rider) {
            return redirect()->route('rider.create')->with('error', 'Please complete your rider registration first.');
        }

        if ($rider->status !== 'approved') {
            return redirect()->route('rider.create')->with('warning', 'Your application is still under review.');
        }

        $orders = $this->assignedOrdersForRider($rider);
        $nearbyOrders = $this->nearbyOrdersForRider($rider);

        return view('enroll.rider_assigned_orders', compact('orders', 'nearbyOrders', 'rider'));
    }

    /**
     * Accept an assigned order
     */
    public function acceptOrder($orderId, OrderNotificationService $notifications)
    {
        $rider = Rider::where('user_id', Auth::id())->where('status', 'approved')->first();
        
        if (!$rider) {
            return redirect()->route('rider.create')->with('error', 'Please complete your rider registration first.');
        }

        $order = Order::where('rider_id', $rider->id)
            ->where('status', 'assigned')
            ->findOrFail($orderId);

        if (!$rider->is_online) {
            return redirect()->back()->with('error', 'Go online before accepting delivery jobs.');
        }

        DB::transaction(function () use ($order, $rider) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $lockedRider = Rider::query()->lockForUpdate()->findOrFail($rider->id);
            abort_unless(
                $lockedOrder->rider_id === $rider->id
                    && $lockedOrder->status === 'assigned'
                    && $lockedOrder->store_approval_status === 'approved'
                    && $lockedOrder->payment_status === 'paid'
                    && $lockedRider->status === 'approved'
                    && $lockedRider->is_online,
                422,
                'This delivery is no longer available.'
            );
            $lockedOrder->update(['status' => 'accepted']);
        });
        $order->refresh();
        $notifications->sendToCustomer($order, 'Rider accepted your order', "The rider accepted order #{$order->id}.", 'order_accepted');
        $notifications->sendToStore($order, 'Rider accepted the delivery', "The rider accepted order #{$order->id}.", 'order_accepted');

        return redirect()->route('rider.assigned-orders')->with('success', '✅ Order accepted! You can now proceed with delivery.');
    }

    /**
     * Reject an assigned order
     */
    public function rejectOrder(Request $request, $orderId, OrderNotificationService $notifications)
    {
        $rider = Rider::where('user_id', Auth::id())->where('status', 'approved')->first();
        
        if (!$rider) {
            return redirect()->route('rider.create')->with('error', 'Please complete your rider registration first.');
        }

        $validated = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);
        $order = Order::findOrFail($orderId);

        // Verify the order is assigned to this rider
        if ($order->rider_id !== $rider->id) {
            return redirect()->back()->with('error', 'This order is not assigned to you.');
        }

        // Only reject orders that are in "assigned" status
        if ($order->status !== 'assigned') {
            return redirect()->back()->with('error', 'You can only reject orders that are in assigned status.');
        }

        DB::transaction(function () use ($order, $rider) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->rider_id === $rider->id && $lockedOrder->status === 'assigned', 422, 'This delivery is no longer available.');
            $lockedOrder->update([
                'status' => 'pending',
                'rider_id' => null,
                'recipient_code' => null,
            ]);
            $lockedOrder->deliveryBids()->where('status', 'accepted')->update(['status' => 'rejected']);
        });
        $order->refresh();
        $reason = $validated['reason'] ?? 'No reason provided.';
        $notifications->sendToStore(
            $order,
            'Rider declined the delivery',
            "The rider declined order #{$order->id}. Reason: {$reason}",
            'order_rider_declined'
        );
        $notifications->sendToCustomer(
            $order,
            'A new rider is being found',
            "The assigned rider could not take order #{$order->id}. The order is available for reassignment.",
            'order_rider_declined'
        );

        return redirect()->route('rider.assigned-orders')->with('success', 'The delivery was returned to the store for reassignment.');
    }
}
