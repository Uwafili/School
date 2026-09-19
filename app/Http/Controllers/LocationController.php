<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $coordinates = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $user = Auth::user();
        \App\Models\User::where('id', $user->id)->update([
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'location_updated_at' => now(),
        ]);

        Rider::where('user_id', $user->id)->update([
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'location_updated_at' => now(),
        ]);

        Store::where('user_id', $user->id)->update([
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
        ]);

        return response()->json(['ok' => true]);
    }

    public function store(Request $request, Store $store)
    {
        abort_unless($store->user_id === Auth::id(), 403);

        $coordinates = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $store->update($coordinates);

        return response()->json(['ok' => true]);
    }
}
