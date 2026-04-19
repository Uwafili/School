<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;




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
        //
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

        $store=$request->validate([
             'stores'=>['required', 'max:255'],
             'owner'=>['required', 'max:255'],
             'email'=>['required','max:255', 'email'],
             'phone'=>['required', 'max:15', 'regex:/^\+?[0-9]{1,4}?[-. ]?([0-9]{1,3}[-. ]?){1,4}[0-9]{1,4}$/'],
             'address'=>['required', 'max:255'],
             'image'=>['file', 'nullable', 'mimes:jpg,png,jpeg,avif','max:3000'],
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
        $id=Auth::id();
        $stores=Store::where("user_id",'=',$id)->get();
        return view('enroll.storedashboard',['stores'=>$stores]);
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
            'items_description' => 'required|string',
        ]);

        $store = Store::where('user_id', Auth::id())->first();
        if (!$store) {
            return redirect()->back()->with('error', 'You need to have a store to create orders.');
        }

        \App\Models\Order::create([
            'store_id' => $store->id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'],
            'total_price' => $validated['total_price'],
            'items_description' => $validated['items_description'],
            'status' => 'pending',
        ]);

        return redirect()->route('storedashboard')->with('success', 'Order created successfully!');
    }

    /**
     * Assign an order to a rider
     */
    public function assignRider(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rider_id' => 'required|exists:riders,id',
        ]);

        $store = Store::where('user_id', Auth::id())->first();
        if (!$store) {
            return redirect()->back()->with('error', 'You need to have a store to assign orders.');
        }

        $order = \App\Models\Order::findOrFail($validated['order_id']);
        
        // Verify the order belongs to this store
        if ($order->store_id !== $store->id) {
            return redirect()->back()->with('error', 'You can only assign your own orders.');
        }

        // Update the order with rider and status
        $order->update([
            'rider_id' => $validated['rider_id'],
            'status' => 'assigned',
        ]);

        return redirect()->route('storedashboard')->with('success', 'Order assigned to rider successfully!');
    }
}
