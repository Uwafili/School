<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{
   public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'message' => 'required'
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        return back();
    }

    // View chat between user and admin
    public function chat($id)
    {
        // $id = other person (admin or user)
        $messages = Message::where(function ($q) use ($id) {
                $q->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
            })
            ->where(function ($q) use ($id) {
                $q->where('sender_id', $id)
                  ->orWhere('receiver_id', $id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat', compact('messages', 'id'));
    }
}
