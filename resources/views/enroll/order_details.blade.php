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
        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-800 border-l-4 border-red-500">{{ session('error') }}</div>
        @endif

        <!-- Order Status Header -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-sm font-bold text-gray-600 uppercase">Order #</p>
                    <h2 class="text-4xl font-bold text-yellow-600">#{{ $order->id }}</h2>
                </div>
                <div class="text-right">
                    <span class="inline-block bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-lg font-bold">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                </div>
            </div>
            <p class="text-gray-600">Ordered: {{ $order->created_at->format('M d, Y H:i') }}</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <p class="rounded-lg bg-gray-50 p-3 text-sm text-gray-700">Store approval: <strong>{{ ucfirst($order->store_approval_status) }}</strong></p>
                <p class="rounded-lg bg-gray-50 p-3 text-sm text-gray-700">Payment: <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</strong></p>
                @if ($order->store_rejection_reason)
                    <p class="rounded-lg bg-red-50 p-3 text-sm text-red-800 sm:col-span-2">Rejection reason: {{ $order->store_rejection_reason }}</p>
                @endif
                @if ($order->refund_status !== 'none')
                    <p class="rounded-lg bg-orange-50 p-3 text-sm text-orange-900 sm:col-span-2">
                        Refund: <strong>{{ ucfirst($order->refund_status) }}</strong>
                        @if ($order->refund_message) · {{ $order->refund_message }} @endif
                    </p>
                @endif
            </div>
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
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </p>
                    </div>
                </div>
                @if ($order->rider->user_id)
                    <a href="{{ route('chat.show', [$order->id, $order->rider->user_id]) }}" class="mt-4 inline-flex rounded-lg bg-indigo-50 px-4 py-2 font-bold text-indigo-700 hover:bg-indigo-100">Message rider</a>
                @endif
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
                @if ($order->customer_id)
                    <a href="{{ route('chat.show', [$order->id, $order->customer_id]) }}" class="rounded-lg bg-indigo-50 px-5 py-3 font-bold text-indigo-700 hover:bg-indigo-100">Message customer</a>
                @endif
                @if ($order->status === 'pending' && $order->store_approval_status === 'pending')
                    <form method="POST" action="{{ route('store.orders.approve', $order) }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-green-600 px-5 py-3 font-bold text-white hover:bg-green-700">Approve order</button>
                    </form>
                    <form method="POST" action="{{ route('store.orders.reject', $order) }}" class="flex flex-wrap gap-2">
                        @csrf
                        <input name="reason" maxlength="500" placeholder="Reason (optional)" class="rounded-lg border border-gray-300 px-3 py-2">
                        <button type="submit" class="rounded-lg bg-red-600 px-5 py-3 font-bold text-white hover:bg-red-700" onclick="return confirm('Reject this order and start a Paystack refund?')">Reject order</button>
                    </form>
                @elseif ($order->status === 'approved')
                    <form method="POST" action="{{ route('store.orders.preparing', $order) }}">@csrf<button type="submit" class="rounded-lg bg-orange-600 px-5 py-3 font-bold text-white hover:bg-orange-700">Start preparation</button></form>
                @elseif ($order->status === 'preparing')
                    <form method="POST" action="{{ route('store.orders.ready', $order) }}">@csrf<button type="submit" class="rounded-lg bg-green-600 px-5 py-3 font-bold text-white hover:bg-green-700">Mark ready for pickup</button></form>
                @elseif (in_array($order->status, ['rejected', 'cancelled']) && $order->refund_status === 'pending' && $order->payment_reference && !$order->refund_reference)
                    <form method="POST" action="{{ route('store.orders.refund.retry', $order) }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-orange-600 px-5 py-3 font-bold text-white hover:bg-orange-700">Retry refund</button>
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
