<?php

namespace App\Http\Controllers;

use App\Models\ApprovalNotification;
use App\Models\Rider;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalNotificationController extends Controller
{
    public function status(Request $request)
    {
        $role = $request->validate([
            'role' => ['required', 'in:rider,store'],
        ])['role'];

        $application = $role === 'rider'
            ? Rider::where('user_id', Auth::id())->latest()->first()
            : Store::where('user_id', Auth::id())->latest()->first();

        if (!$application) {
            return response()->json(['status' => null, 'approved' => false]);
        }

        $notification = ApprovalNotification::where('user_id', Auth::id())
            ->where('role', $role)
            ->where('is_read', false)
            ->latest()
            ->first();

        if ($notification) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'status' => $application->status,
            'approved' => $application->status === 'approved',
            'title' => $notification?->title,
            'message' => $notification?->message,
        ]);
    }
}
