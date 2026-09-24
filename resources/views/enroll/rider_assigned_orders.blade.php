@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-gray-100 py-10 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-yellow-600">📦 Assigned Orders</h1>
            <div class="flex gap-3">
                <a href="{{ route('rider.notifications') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition shadow-md">
                    🔔 Notifications
                </a>
                <a href="{{ route('rider.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg transition shadow-md">
                    ← Dashboard
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-800 border-l-4 border-green-500">
                <strong>✓ Success:</strong> {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-800 border-l-4 border-red-500">
                <strong>✗ Warning:</strong> {{ session('warning') }}
            </div>
        @endif

        <!-- Order Status Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                <span class="text-sm font-semibold text-gray-600 uppercase mb-2">📋 Pending (Assigned)</span>
                <span class="text-4xl font-bold text-yellow-600">{{ $orders->where('status', 'assigned')->count() }}</span>
                <p class="text-gray-600 text-sm mt-2">Awaiting your decision</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                <span class="text-sm font-semibold text-gray-600 uppercase mb-2">✅ Accepted</span>
                <span class="text-4xl font-bold text-green-600">{{ $orders->where('status', 'accepted')->count() }}</span>
                <p class="text-gray-600 text-sm mt-2">Ready for delivery</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                <span class="text-sm font-semibold text-gray-600 uppercase mb-2">❌ Rejected</span>
                <span class="text-4xl font-bold text-red-600">{{ $orders->where('status', 'rejected')->count() }}</span>
                <p class="text-gray-600 text-sm mt-2">Declined orders</p>
            </div>
        </div>

        @if ($orders->where('status', 'pending')->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">📍 Nearby open orders</h2>
                <div class="space-y-4">
                    @foreach ($orders->where('status', 'pending') as $order)
                        <div class="rounded-lg border-l-4 border-blue-500 bg-blue-50 p-5">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <p class="font-bold text-gray-800">Order #{{ $order->id }} · {{ $order->customer_name }}</p>
                                    <p class="mt-1 text-sm text-gray-600">Pickup: {{ $order->store->stores ?? 'Store' }} · {{ $order->store->address ?? 'Location shared by store' }}</p>
                                    <p class="text-sm text-gray-600">Drop-off: {{ $order->customer_address }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Order total: <strong>₦{{ number_format($order->total_price, 2) }}</strong></p>
                                    <p class="text-lg font-bold text-green-700">Posted delivery pay: ₦{{ number_format($order->delivery_fee, 2) }}</p>
                                    <form method="POST" action="{{ route('order.bid', $order->id) }}" class="mt-3 flex items-end gap-2">
                                        @csrf
                                        <label class="flex-1 text-xs font-bold text-gray-700">Your bid (₦)<input type="number" name="amount" min="0" max="1000000" step="0.01" value="{{ $order->deliveryBids->first()->amount ?? $order->delivery_fee }}" required class="mt-1 w-full rounded-lg border-2 border-gray-200 px-3 py-2"></label>
                                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-bold text-white hover:bg-blue-700">Bid</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Assigned Orders (Pending Action) -->
        @if ($orders->where('status', 'assigned')->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                    <span class="text-3xl">🎯</span> Orders Awaiting Your Decision
                </h2>

                <div class="space-y-6">
                    @foreach ($orders->where('status', 'assigned') as $order)
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-6 hover:shadow-lg transition">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <!-- Order Details -->
                                <div>
                                    <p class="text-sm font-bold text-gray-600 uppercase mb-3">Order Information</p>
                                    <div class="space-y-2">
                                        <p class="text-gray-800">
                                            <span class="font-semibold">Order ID:</span> #{{ $order->id }}
                                        </p>
                                        <p class="text-gray-800">
                                            <span class="font-semibold">Customer:</span> {{ $order->customer_name }}
                                        </p>
                                        <p class="text-gray-800">
                                            <span class="font-semibold">Phone:</span> {{ $order->customer_phone }}
                                        </p>
                                        <p class="text-gray-800">
                                            <span class="font-semibold">Delivery Address:</span> {{ $order->customer_address }}
                                        </p>
                                        <p class="text-gray-800">
                                            <span class="font-semibold">Pickup:</span> {{ $order->store->stores ?? 'Store' }} · {{ $order->store->address ?? 'Pickup location' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Order Items & Price -->
                                <div>
                                    <p class="text-sm font-bold text-gray-600 uppercase mb-3">Delivery Details</p>
                                    <div class="space-y-2">
                                        <p class="text-gray-800">
                                            <span class="font-semibold">Items:</span>
                                        </p>
                                        <p class="text-gray-700 bg-white p-2 rounded">{{ $order->items_description }}</p>
                                        <div class="pt-2 border-t border-yellow-200 mt-3">
                                            <p class="text-2xl font-bold text-yellow-600">
                                                Total: ₦{{ number_format($order->total_price, 2) }}
                                            </p>
                                            <p class="text-lg font-bold text-green-700">Delivery pay: ₦{{ number_format($order->delivery_fee, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($order->status === 'pending' && !$order->rider_id)
                                <form method="POST" action="{{ route('order.bid', $order->id) }}" class="mt-5 flex flex-wrap items-end gap-3 border-t border-yellow-200 pt-5">
                                    @csrf
                                    <label class="flex-1 text-sm font-bold text-gray-700">Your delivery bid (₦)<input type="number" name="amount" min="0" max="1000000" step="0.01" value="{{ $order->deliveryBids->first()->amount ?? $order->delivery_fee }}" required class="mt-1 w-full rounded-lg border-2 border-gray-200 px-3 py-2"></label>
                                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-3 font-bold text-white hover:bg-blue-700">Send bid</button>
                                </form>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex gap-4 mt-6 border-t border-yellow-200 pt-6">
                                <form method="POST" action="{{ route('order.accept', $order->id) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                                        ✅ Accept Order
                                    </button>
                                </form>

                                <button type="button" onclick="openRejectModal({{ $order->id }})" class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                                    ❌ Reject Order
                                </button>
                            </div>

                            <p class="text-xs text-gray-500 mt-3">
                                Assigned: {{ $order->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Accepted Orders -->
        @if ($orders->where('status', 'accepted')->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                    <span class="text-3xl">✅</span> Accepted Orders (Ready for Delivery)
                </h2>

                <div class="space-y-4">
                    @foreach ($orders->where('status', 'accepted') as $order)
                        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-gray-800">Order #{{ $order->id }} - {{ $order->customer_name }}</p>
                                    <p class="text-gray-600">{{ $order->customer_address }}</p>
                                    <p class="text-green-700 font-bold mt-2">₦{{ number_format($order->total_price, 2) }}</p>
                                </div>
                                <span class="bg-green-200 text-green-800 font-bold px-4 py-2 rounded-lg">
                                    Ready to Go
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Rejected Orders -->
        @if ($orders->where('status', 'rejected')->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                    <span class="text-3xl">❌</span> Rejected Orders
                </h2>

                <div class="space-y-4">
                    @foreach ($orders->where('status', 'rejected') as $order)
                        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-gray-800">Order #{{ $order->id }} - {{ $order->customer_name }}</p>
                                    <p class="text-gray-600">{{ $order->customer_address }}</p>
                                    <p class="text-red-700 font-bold mt-2">₦{{ number_format($order->total_price, 2) }}</p>
                                </div>
                                <span class="bg-red-200 text-red-800 font-bold px-4 py-2 rounded-lg">
                                    Rejected
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Empty State -->
        @if ($orders->isEmpty())
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-8 text-center">
                <p class="text-2xl font-bold text-yellow-700 mb-2">📭 No Orders Assigned Yet</p>
                <p class="text-yellow-600">Check back soon! New orders will appear here when shop owners assign them to you.</p>
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">❌ Reject Order</h3>
        <p class="text-gray-600 mb-4">Are you sure you want to reject this order? Please provide a reason.</p>

        <form id="rejectForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="reason" class="block text-gray-700 font-bold mb-2">Reason for Rejection</label>
                <textarea id="reason" name="reason" rows="3" 
                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-300 outline-none"
                    placeholder="e.g., Too far, Vehicle issue, etc."></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-2 rounded-lg transition">
                    Reject Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentOrderId = null;

    function openRejectModal(orderId) {
        currentOrderId = orderId;
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectForm').action = `/rider/order/${orderId}/reject`;
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('reason').value = '';
    }

    // Close modal when clicking outside
    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
</script>
@endsection
