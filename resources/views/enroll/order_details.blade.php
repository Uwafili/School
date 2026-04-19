@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-gray-100 py-10 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-yellow-600">📦 Order Details</h1>
            <a href="{{ route('storedashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg transition shadow-md">
                ← Back to Dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-800 border-l-4 border-green-500">
                <strong>✓ Success:</strong> {{ session('success') }}
            </div>
        @endif

        <!-- Order Status Header -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-sm font-bold text-gray-600 uppercase">Order #</p>
                    <h2 class="text-4xl font-bold text-yellow-600">#{{ $order->id }}</h2>
                </div>
                <div class="text-right">
                    @if ($order->status === 'pending')
                        <span class="inline-block bg-orange-100 text-orange-700 px-4 py-2 rounded-lg text-lg font-bold">⏳ Pending</span>
                    @elseif ($order->status === 'assigned')
                        <span class="inline-block bg-yellow-100 text-yellow-700 px-4 py-2 rounded-lg text-lg font-bold">📍 Assigned</span>
                    @elseif ($order->status === 'accepted')
                        <span class="inline-block bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-lg font-bold">🚚 In Transit</span>
                    @elseif ($order->status === 'completed')
                        <span class="inline-block bg-green-100 text-green-700 px-4 py-2 rounded-lg text-lg font-bold">✓ Delivered</span>
                    @elseif ($order->status === 'rejected')
                        <span class="inline-block bg-red-100 text-red-700 px-4 py-2 rounded-lg text-lg font-bold">❌ Rejected</span>
                    @elseif ($order->status === 'cancelled')
                        <span class="inline-block bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-lg font-bold">⊘ Cancelled</span>
                    @endif
                </div>
            </div>
            <p class="text-gray-600">Ordered: {{ $order->created_at->format('M d, Y H:i') }}</p>
        </div>

        <!-- Customer Information -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <span class="text-3xl">👤</span> Customer Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                    <p class="text-xs font-bold text-gray-600 uppercase mb-1">Customer Name</p>
                    <p class="text-xl font-bold text-gray-800">{{ $order->customer_name }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                    <p class="text-xs font-bold text-gray-600 uppercase mb-1">Phone Number</p>
                    <p class="text-xl font-bold text-gray-800">{{ $order->customer_phone }}</p>
                </div>
                <div class="md:col-span-2 bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                    <p class="text-xs font-bold text-gray-600 uppercase mb-1">Delivery Address</p>
                    <p class="text-lg font-bold text-gray-800">{{ $order->customer_address }}</p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <span class="text-3xl">🛒</span> Order Items
            </h3>
            <div class="bg-gray-50 p-6 rounded-lg border-l-4 border-yellow-500">
                <p class="text-gray-700 text-lg leading-relaxed whitespace-pre-wrap">{{ $order->items_description }}</p>
            </div>
        </div>

        <!-- Rider Information (if assigned) -->
        @if ($order->rider)
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                    <span class="text-3xl">🏍️</span> Assigned Rider
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <p class="text-xs font-bold text-gray-600 uppercase mb-1">Rider Name</p>
                        <p class="text-xl font-bold text-gray-800">{{ $order->rider->user->name }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <p class="text-xs font-bold text-gray-600 uppercase mb-1">Rider Phone</p>
                        <p class="text-xl font-bold text-gray-800">{{ $order->rider->phone }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <p class="text-xs font-bold text-gray-600 uppercase mb-1">Vehicle</p>
                        <p class="text-xl font-bold text-gray-800">{{ $order->rider->vehicle }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <p class="text-xs font-bold text-gray-600 uppercase mb-1">Status</p>
                        <p class="text-xl font-bold text-gray-800">
                            @if ($order->status === 'accepted')
                                🚚 In Transit
                            @elseif ($order->status === 'assigned')
                                ⏳ Awaiting Response
                            @elseif ($order->status === 'completed')
                                ✅ Delivered
                            @else
                                {{ ucfirst($order->status) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Payment Summary -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <span class="text-3xl">💰</span> Payment Summary
            </h3>
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-6 rounded-lg border-l-4 border-yellow-500">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-lg font-bold text-gray-700">Order Total:</p>
                    <p class="text-4xl font-bold text-yellow-600">₦{{ number_format($order->total_price, 2) }}</p>
                </div>
                <div class="border-t border-yellow-200 pt-4">
                    <p class="text-sm text-gray-600">Delivery Commission: <span class="font-bold text-gray-800">10%</span></p>
                    <p class="text-sm text-gray-600">Your Revenue: <span class="font-bold text-green-700">₦{{ number_format($order->total_price * 0.9, 2) }}</span></p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Actions</h3>
            <div class="flex gap-4 flex-wrap">
                @if ($order->status === 'pending')
                    <button class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-6 rounded-lg transition shadow-md">
                        🎯 Assign to Rider
                    </button>
                @elseif ($order->status === 'accepted' || $order->status === 'assigned')
                    <form method="POST" action="{{ route('order.complete', $order->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-3 px-6 rounded-lg transition shadow-md" onclick="return confirm('Mark this order as completed?')">
                            ✅ Mark as Completed
                        </button>
                    </form>
                @endif

                @if ($order->status !== 'completed' && $order->status !== 'cancelled')
                    <form method="POST" action="{{ route('order.cancel', $order->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-3 px-6 rounded-lg transition shadow-md" onclick="return confirm('Cancel this order?')">
                            ❌ Cancel Order
                        </button>
                    </form>
                @endif

                <a href="{{ route('storedashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition shadow-md">
                    ← Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
