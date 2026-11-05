@extends('layouts.navbar')
@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<div class="min-h-screen bg-gray-100 py-10 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-yellow-600">Rider dashboard</h1>
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
        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-800 border-l-4 border-red-500">
                <strong>✗ Error:</strong> {{ session('error') }}
            </div>
        @endif

        <!-- Order Status Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                <span class="text-sm font-semibold text-gray-600 uppercase mb-2">📋 Assigned</span>
                <span class="text-4xl font-bold text-yellow-600">{{ $orders->where('status', 'rider_assigned')->count() }}</span>
                <p class="text-gray-600 text-sm mt-2">Awaiting pickup</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                <span class="text-sm font-semibold text-gray-600 uppercase mb-2">✅ In progress</span>
                <span class="text-4xl font-bold text-green-600">{{ $orders->whereIn('status', ['picked_up', 'out_for_delivery'])->count() }}</span>
                <p class="text-gray-600 text-sm mt-2">Picked up or on the way</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                <span class="text-sm font-semibold text-gray-600 uppercase mb-2">✅ Completed</span>
                <span class="text-4xl font-bold text-green-600">{{ $orders->where('status', 'delivered')->count() }}</span>
                <p class="text-gray-600 text-sm mt-2">Delivery history</p>
            </div>
        </div>

        <section class="mb-10 flex flex-wrap items-center justify-between gap-4 rounded-xl bg-white p-6 shadow">
            <div>
                <p class="text-sm font-semibold uppercase text-gray-500">Availability</p>
                <p class="mt-1 text-xl font-bold {{ $rider->is_online ? 'text-green-700' : 'text-gray-700' }}">{{ $rider->is_online ? 'Online and available' : 'Offline' }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ $rider->is_online ? 'Go offline when you need a break.' : 'Go online to receive nearby delivery requests.' }}</p>
            </div>
            <form action="{{ route('rider.availability') }}" method="POST">
                @csrf
                <input type="hidden" name="is_online" value="{{ $rider->is_online ? 0 : 1 }}">
                <button type="submit" class="rounded-lg px-5 py-3 font-bold text-white {{ $rider->is_online ? 'bg-gray-800 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }}">{{ $rider->is_online ? 'Go offline' : 'Go online' }}</button>
            </form>
        </section>

        <section class="mb-10 rounded-xl bg-white p-6 shadow">
            <h2 class="mb-4 text-xl font-bold text-gray-800">Pickup locations</h2>
            <div id="riderMap" class="h-72 rounded-lg bg-yellow-50"></div>
        </section>

        @if ($nearbyOrders->isNotEmpty())
            <section class="mb-10 rounded-xl bg-white p-8 shadow-lg">
                <h2 class="mb-6 text-2xl font-bold text-gray-800">Nearby open orders</h2>
                <div class="space-y-4">
                    @foreach ($nearbyOrders as $order)
                        <article class="rounded-lg border-l-4 border-orange-400 bg-orange-50 p-5">
                            <p class="font-bold text-gray-800">Order #{{ $order->id }} · {{ $order->store->stores }} · {{ $order->distance_km }} km</p>
                            <p class="mt-1 text-sm text-gray-600">Pickup: {{ $order->store->address }} · Drop-off: {{ $order->customer_address }}</p>
                            <p class="mt-2 text-sm text-gray-600">{{ $order->items_description }}</p>
                            <p class="mt-2 font-bold text-green-700">Delivery pay: ₦{{ number_format($order->delivery_fee, 2) }}</p>
                            <form method="POST" action="{{ route('order.bid', $order->id) }}" class="mt-3 flex items-end gap-2">
                                @csrf
                                <label class="flex-1 text-sm font-bold text-gray-700">Your delivery offer (₦)<input type="number" name="amount" min="0" max="1000000" step="0.01" value="{{ $order->delivery_fee }}" required class="mt-1 w-full rounded-lg border-2 border-gray-200 px-3 py-2"></label>
                                <button type="submit" class="rounded-lg bg-gray-900 px-5 py-3 font-bold text-white hover:bg-orange-600">Request assignment</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Assigned Orders -->
        @if ($orders->where('status', 'rider_assigned')->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                    <span class="text-3xl">🎯</span> Orders Assigned to You
                </h2>

                <div class="space-y-6">
                    @foreach ($orders->where('status', 'rider_assigned') as $order)
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

                            <!-- Action Buttons -->
                            <div class="mt-6 space-y-3 border-t border-yellow-200 pt-6">
                                @if (!$order->rider_accepted_at)
                                    <form method="POST" action="{{ route('rider.accept', $order->id) }}">@csrf<button type="submit" class="w-full rounded-lg bg-blue-600 py-3 font-bold text-white hover:bg-blue-700">Accept / acknowledge delivery</button></form>
                                @elseif (!$order->rider_arrived_at)
                                    <form method="POST" action="{{ route('rider.arrived', $order->id) }}">@csrf<button type="submit" class="w-full rounded-lg bg-blue-600 py-3 font-bold text-white hover:bg-blue-700">Confirm arrival at store</button></form>
                                @else
                                    <form method="POST" action="{{ route('rider.pickup', $order->id) }}">@csrf<button type="submit" class="w-full rounded-lg bg-green-600 py-3 font-bold text-white hover:bg-green-700">Confirm order picked up</button></form>
                                @endif
                                <button type="button" onclick="openRejectModal({{ $order->id }})" class="w-full rounded-lg bg-red-100 py-3 font-bold text-red-700 hover:bg-red-200">Decline assignment</button>
                            </div>

                            <p class="text-xs text-gray-500 mt-3">
                                Assigned: {{ $order->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Active Orders -->
        @if ($orders->whereIn('status', ['picked_up', 'out_for_delivery'])->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                    <span class="text-3xl">✅</span> Active Deliveries
                </h2>

                <div class="space-y-4">
                    @foreach ($orders->whereIn('status', ['picked_up', 'out_for_delivery']) as $order)
                        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-gray-800">Order #{{ $order->id }} - {{ $order->customer_name }}</p>
                                    <p class="text-gray-600">{{ $order->customer_address }}</p>
                                    <p class="text-green-700 font-bold mt-2">₦{{ number_format($order->total_price, 2) }}</p>
                                </div>
                                <span class="bg-green-200 text-green-800 font-bold px-4 py-2 rounded-lg">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                            </div>
                            @if ($order->status === 'picked_up')
                                <form method="POST" action="{{ route('rider.out-for-delivery', $order->id) }}" class="mt-4">@csrf<button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-bold text-white hover:bg-blue-700">Start delivery</button></form>
                            @else
                                <form method="POST" action="{{ route('rider.deliver', $order->id) }}" class="mt-4 flex gap-2">@csrf<input name="recipient_code" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required placeholder="4-digit customer code" class="min-w-0 flex-1 rounded-lg border-gray-200 px-3 py-2"><button type="submit" class="rounded-lg bg-gray-900 px-4 py-2 font-bold text-white hover:bg-green-700">Confirm delivery</button></form>
                                <form method="POST" action="{{ route('rider.delivery-failed', $order->id) }}" class="mt-3 space-y-2">@csrf<textarea name="reason" required maxlength="1000" rows="2" placeholder="Reason delivery could not be completed" class="w-full rounded-lg border-gray-200 px-3 py-2"></textarea><button type="submit" class="rounded-lg bg-red-100 px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-200">Report failed delivery attempt</button></form>
                                @foreach($order->deliveryAttempts as $attempt)
                                    <p class="mt-2 rounded-lg bg-red-50 p-3 text-sm text-red-800">Attempt {{ $attempt->attempted_at->format('M j, g:i A') }}: {{ $attempt->reason }}</p>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($orders->where('status', 'delivered')->count() > 0)
            <section class="rounded-xl bg-white p-8 shadow-lg">
                <h2 class="mb-5 text-2xl font-bold text-gray-800">Completed deliveries and history</h2>
                <div class="space-y-3">
                    @foreach ($orders->where('status', 'delivered') as $order)
                        <article class="flex flex-wrap items-center justify-between gap-3 rounded-lg bg-green-50 p-4">
                            <div>
                                <p class="font-bold text-gray-800">{{ $order->order_reference }} · {{ $order->customer_name }}</p>
                                <p class="text-sm text-gray-600">{{ $order->customer_address }}</p>
                            </div>
                            <p class="text-sm font-semibold text-green-700">Delivered {{ $order->recipient_verified_at?->format('M j, Y · g:i A') ?? $order->updated_at->format('M j, Y · g:i A') }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
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
        @if ($orders->isEmpty() && $nearbyOrders->isEmpty())
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-8 text-center">
                <p class="text-2xl font-bold text-yellow-700 mb-2">📭 No Orders Assigned Yet</p>
                <p class="text-yellow-600">Nearby paid orders and jobs assigned by stores will appear here.</p>
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Decline Delivery Assignment</h3>
        <p class="text-gray-600 mb-4">Please provide a reason for returning this delivery to the store.</p>

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
                    Decline Assignment
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const riderLatitude = @json($rider->latitude);
    const riderLongitude = @json($rider->longitude);
    const riderMap = L.map('riderMap').setView([riderLatitude || 6.5244, riderLongitude || 3.3792], riderLatitude ? 12 : 6);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(riderMap);

    if (riderLatitude && riderLongitude) {
        L.marker([riderLatitude, riderLongitude]).addTo(riderMap).bindPopup('Your location');
    }

    @foreach($orders as $order)
        @if($order->store && $order->store->latitude && $order->store->longitude)
            L.marker([{{ $order->store->latitude }}, {{ $order->store->longitude }}])
                .addTo(riderMap)
                .bindPopup(@json($order->store->stores . ' pickup'));
        @endif
    @endforeach

    @foreach($nearbyOrders as $order)
        @if($order->store && $order->store->latitude && $order->store->longitude)
            L.marker([{{ $order->store->latitude }}, {{ $order->store->longitude }}])
                .addTo(riderMap)
                .bindPopup(@json($order->store->stores . ' nearby pickup'));
        @endif
    @endforeach

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
