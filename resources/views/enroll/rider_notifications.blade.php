@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-gray-100 py-10 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-yellow-600">🔔 My Notifications</h1>
            <a href="{{ route('rider.dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition shadow-md">
                ← Back to Dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-800 border-l-4 border-green-500">
                <strong>✓ Success:</strong> {{ session('success') }}
            </div>
        @endif

        <!-- Unread Count Badge -->
        <div class="bg-white rounded-lg shadow p-4 mb-6 flex justify-between items-center">
            <div>
                <p class="text-gray-600 text-sm">Total Notifications</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $notifications->total() }}</p>
            </div>
            @if ($unreadCount > 0)
                <div class="bg-red-100 rounded-lg p-4 text-center">
                    <p class="text-gray-600 text-sm">Unread</p>
                    <p class="text-3xl font-bold text-red-600">{{ $unreadCount }}</p>
                </div>
            @else
                <div class="bg-green-100 rounded-lg p-4 text-center">
                    <p class="text-gray-600 text-sm">Status</p>
                    <p class="text-lg font-bold text-green-600">✅ All Read</p>
                </div>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @forelse ($notifications as $notification)
                <div class="bg-white rounded-lg shadow-md p-6 @if(!$notification->is_read) border-l-4 border-yellow-500 hover:shadow-lg @else border-l-4 border-gray-300 @endif transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="text-3xl">📦</div>
                            <div class="flex-1">
                                <div class="flex gap-2 items-center mb-2">
                                    <h3 class="text-lg font-bold text-gray-800">{{ $notification->title }}</h3>
                                    @if (!$notification->is_read)
                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full">
                                            NEW
                                        </span>
                                    @endif
                                </div>
                                <p class="text-gray-700 mb-3">{{ $notification->message }}</p>
                                
                                <!-- Order Details -->
                                @if ($notification->order)
                                    <div class="bg-gray-50 rounded p-3 mb-3 text-sm">
                                        <p class="text-gray-600">
                                            <strong>Order #:</strong> {{ $notification->order->id }}
                                        </p>
                                        <p class="text-gray-600">
                                            <strong>Customer:</strong> {{ $notification->order->customer_name }}
                                        </p>
                                        <p class="text-gray-600">
                                            <strong>Phone:</strong> {{ $notification->order->customer_phone }}
                                        </p>
                                        <p class="text-gray-600">
                                            <strong>Delivery Address:</strong> {{ $notification->order->customer_address }}
                                        </p>
                                        <p class="text-gray-600">
                                            <strong>Items:</strong> {{ $notification->order->items_description }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col gap-2">
                            @if (!$notification->is_read)
                                <form method="POST" action="{{ route('notification.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition whitespace-nowrap text-sm">
                                        ✓ Mark as Read
                                    </button>
                                </form>
                            @else
                                <div class="bg-green-100 text-green-800 font-bold py-2 px-4 rounded-lg text-center text-sm">
                                    ✅ Read
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timestamp -->
                    <div class="text-xs text-gray-500">
                        <p>{{ $notification->created_at->diffForHumans() }}</p>
                        @if ($notification->is_read)
                            <p>Read: {{ $notification->read_at->format('M d, Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-8 text-center">
                    <p class="text-2xl font-bold text-yellow-700 mb-2">🔔 No Notifications Yet</p>
                    <p class="text-yellow-600">When orders are assigned to you, you'll see them here.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
