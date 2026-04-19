<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Rider;

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
            'image'=>['file','nullable','mimes:jpg,png,jpeg,avif','max:3000']
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
            'status' => 'pending' // Default status
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

        return view('enroll.ridersdashboard', compact('Rider'));
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
        $rider = Rider::where('user_id', Auth::id())->first();
        
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

    /**
     * Mark a notification as read
     */
    public function markNotificationAsRead($notificationId)
    {
        $notification = \App\Models\Notification::findOrFail($notificationId);
        
        // Verify the notification belongs to the authenticated rider
        $rider = Rider::where('user_id', Auth::id())->first();
        if ($notification->rider_id !== $rider->id) {
            abort(403, 'Unauthorized');
        }

        $notification->markAsRead();
        
        return redirect()->route('rider.notifications')->with('success', 'Notification marked as read.');
    }

    /**
     * Get unread notification count (for AJAX/Dashboard)
     */
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

        $orders = \App\Models\Order::where('rider_id', $rider->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('enroll.rider_assigned_orders', compact('orders', 'rider'));
    }

    /**
     * Accept an assigned order
     */
    public function acceptOrder($orderId)
    {
        $rider = Rider::where('user_id', Auth::id())->first();
        
        if (!$rider) {
            return redirect()->route('rider.create')->with('error', 'Please complete your rider registration first.');
        }

        $order = \App\Models\Order::findOrFail($orderId);

        // Verify the order is assigned to this rider
        if ($order->rider_id !== $rider->id) {
            return redirect()->back()->with('error', 'This order is not assigned to you.');
        }

        // Only accept orders that are in "assigned" status
        if ($order->status !== 'assigned') {
            return redirect()->back()->with('error', 'You can only accept orders that are in assigned status.');
        }

        $order->update(['status' => 'accepted']);

        // Update notification
        \App\Models\Notification::where('order_id', $order->id)
            ->where('rider_id', $rider->id)
            ->update(['type' => 'order_completed']);

        return redirect()->route('rider.assigned-orders')->with('success', '✅ Order accepted! You can now proceed with delivery.');
    }

    /**
     * Reject an assigned order
     */
    public function rejectOrder(Request $request, $orderId)
    {
        $rider = Rider::where('user_id', Auth::id())->first();
        
        if (!$rider) {
            return redirect()->route('rider.create')->with('error', 'Please complete your rider registration first.');
        }

        $order = \App\Models\Order::findOrFail($orderId);

        // Verify the order is assigned to this rider
        if ($order->rider_id !== $rider->id) {
            return redirect()->back()->with('error', 'This order is not assigned to you.');
        }

        // Only reject orders that are in "assigned" status
        if ($order->status !== 'assigned') {
            return redirect()->back()->with('error', 'You can only reject orders that are in assigned status.');
        }

        // Reject the order
        $order->update([
            'status' => 'rejected',
            'rider_id' => null,
        ]);

        // Create rejection notification for store
        \App\Models\Notification::create([
            'rider_id' => $rider->id,
            'order_id' => $order->id,
            'title' => '❌ Order Rejected',
            'message' => "Rider #{$rider->id} ({$rider->user->name}) has rejected order #{$order->id}. Reason: {$request->input('reason', 'No reason provided')}",
            'type' => 'order_cancelled',
        ]);

        return redirect()->route('rider.assigned-orders')->with('warning', '❌ Order rejected and returned to pending list.');
    }
}
