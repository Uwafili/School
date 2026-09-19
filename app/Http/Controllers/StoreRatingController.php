<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StoreRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreRatingController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_unless($order->customer_id === Auth::id(), 403);
        abort_unless($order->status === 'completed', 422, 'You can rate an order after delivery.');

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['nullable', 'string', 'max:500'],
        ]);

        StoreRating::updateOrCreate(
            ['order_id' => $order->id],
            [
                'store_id' => $order->store_id,
                'customer_id' => Auth::id(),
                'rating' => $validated['rating'],
                'review' => $validated['review'] ?? null,
            ],
        );

        return back()->with('success', 'Thanks for rating this store.');
    }
}
