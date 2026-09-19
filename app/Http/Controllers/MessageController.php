<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function chat(Order $order, int $user)
    {
        $this->authorizeParticipant($order, $user);

        $messages = Message::where('order_id', $order->id)
            ->where(function ($query) use ($user) {
                $query->where(function ($conversation) use ($user) {
                    $conversation->where('sender_id', Auth::id())->where('receiver_id', $user);
                })->orWhere(function ($conversation) use ($user) {
                    $conversation->where('sender_id', $user)->where('receiver_id', Auth::id());
                });
            })
            ->with('sender')
            ->oldest()
            ->get();

        $contact = \App\Models\User::findOrFail($user);
        return view('chat', compact('messages', 'order', 'contact'));
    }

    public function send(Request $request, Order $order)
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'integer'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $this->authorizeParticipant($order, (int) $validated['receiver_id']);

        Message::create([
            'order_id' => $order->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Message sent.');
    }

    private function authorizeParticipant(Order $order, int $contactId): void
    {
        $order->loadMissing(['store', 'rider']);
        $customerId = $order->customer_id ?: \App\Models\User::where('name', $order->customer_name)->value('id');
        $vendorId = $order->store?->user_id;
        $riderId = $order->rider?->user_id;
        $allowed = array_values(array_filter([$customerId, $vendorId, $riderId]));

        abort_unless(in_array(Auth::id(), $allowed, true) && in_array($contactId, $allowed, true) && $contactId !== Auth::id(), 403);
    }
}
