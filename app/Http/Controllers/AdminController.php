<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

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
        
        return back()->with('success', "✅ Store '{$store->stores}' has been approved! Owner will be notified.");
    }

    /**
     * Reject a store application
     */
    public function rejectStore(Store $store)
    {
        $store->update(['status' => 'rejected']);
        
        return back()->with('warning', "❌ Store '{$store->stores}' has been rejected.");
    }
}
