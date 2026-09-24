<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Rider;
use App\Models\ApprovalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Display all stores for admin approval
     */
    public function storeApprove()
    {
        $stores = Store::orderBy('created_at', 'desc')->get();
        
        return view('admin.storeapprove', [
            'stores' => $stores,
            'pendingCount' => Store::where('status', 'pending')->count(),
            'approvedCount' => Store::where('status', 'approved')->count(),
            'rejectedCount' => Store::where('status', 'rejected')->count(),
        ]);
    }

    /**
     * Approve a store application
     */
    public function approveStore(Store $store)
    {
        $store->update(['status' => 'approved']);
        ApprovalNotification::create([
            'user_id' => $store->user_id,
            'role' => 'store',
            'title' => 'Store approved',
            'message' => "Your store, {$store->stores}, has been approved. You can now manage it and start receiving orders.",
            'status' => 'approved',
        ]);
        
        return back()->with('success', "✅ Store '{$store->stores}' has been approved! Owner will be notified.");
    }

    /**
     * Reject a store application
     */
    public function rejectStore(Store $store)
    {
        $store->update(['status' => 'rejected']);
        ApprovalNotification::create([
            'user_id' => $store->user_id,
            'role' => 'store',
            'title' => 'Store application update',
            'message' => "Your store, {$store->stores}, needs attention. Please review your application details and try again.",
            'status' => 'rejected',
        ]);
        
        return back()->with('warning', "❌ Store '{$store->stores}' has been rejected.");
    }

    public function editRider(Rider $rider)
    {
        return view('admin.rider-edit', compact('rider'));
    }

    public function updateRider(Request $request, Rider $rider)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:225'],
            'phone' => ['required', 'max:15'],
            'license' => ['required', 'max:255'],
            'vehicle_number' => ['required', 'max:15'],
            'vehicle' => ['required', 'max:255'],
            'image' => ['nullable', 'file', 'mimes:jpg,png,jpeg,avif', 'max:3000'],
        ]);

        if ($request->hasFile('image')) {
            if ($rider->image) {
                Storage::disk('public')->delete($rider->image);
            }
            $validated['image'] = $request->file('image')->store('rider_images', 'public');
        }

        $rider->update($validated);

        return redirect()->route('riders')->with('success', 'Rider updated successfully.');
    }

    public function editStore(Store $store)
    {
        return view('admin.store-edit', compact('store'));
    }

    public function updateStore(Request $request, Store $store)
    {
        $validated = $request->validate([
            'stores' => ['required', 'string', 'max:255'],
            'owner' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'max:15'],
            'address' => ['required', 'max:255'],
            'image' => ['nullable', 'file', 'mimes:jpg,png,jpeg,avif', 'max:3000'],
        ]);

        if ($request->hasFile('image')) {
            if ($store->image) {
                Storage::disk('public')->delete($store->image);
            }
            $validated['image'] = $request->file('image')->store('store_images', 'public');
        }

        $store->update($validated);

        return redirect()->route('storeapprove')->with('success', 'Store updated successfully.');
    }
}
