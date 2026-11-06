@extends('layouts.navbar')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<main class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <header class="mb-7 flex flex-wrap items-center justify-between gap-4">
            <div><p class="text-xs font-black uppercase tracking-[.18em] text-orange-500">FoodStore delivery partner</p><h1 class="mt-1 text-3xl font-black tracking-tight text-gray-900">Rider dashboard</h1></div>
            <div class="flex gap-2"><a href="{{ route('rider.notifications') }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700">Notifications</a><a href="{{ route('rider.dashboard') }}" class="rounded-xl bg-gray-800 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-gray-700">Refresh</a></div>
        </header>

        @if(session('success'))<div class="mb-5 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('success') }}</div>@endif
        @if(session('warning') || session('error'))<div class="mb-5 rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ session('warning') ?? session('error') }}</div>@endif

        <div class="mb-8"><livewire:rider-availability /></div>

        <section class="mb-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
            <div class="mb-4"><p class="text-xs font-black uppercase tracking-wide text-blue-600">Route overview</p><h2 class="mt-1 text-xl font-black text-gray-900">Pickup locations</h2></div>
            <div id="riderMap" class="h-72 rounded-xl bg-slate-100"></div>
        </section>

        @if($nearbyOrders->isNotEmpty())
            <section class="mb-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
                <div class="mb-5"><p class="text-xs font-black uppercase tracking-wide text-orange-600">Nearby opportunities</p><h2 class="mt-1 text-xl font-black text-gray-900">Open orders</h2></div>
                <div class="space-y-4">
                    @foreach($nearbyOrders as $order)
                        <article class="rounded-xl border border-orange-100 bg-orange-50 p-5">
                            <p class="font-bold text-gray-900">Order #{{ $order->id }} · {{ $order->store->stores }} · {{ $order->distance_km }} km</p>
                            <p class="mt-1 text-sm text-gray-600">Pickup: {{ $order->store->address }} · Drop-off: {{ $order->customer_address }}</p>
                            <p class="mt-2 text-sm text-gray-700">{{ $order->items_description }}</p>
                            <p class="mt-2 font-bold text-green-700">Delivery pay: ₦{{ number_format($order->delivery_fee, 2) }}</p>
                            <form method="POST" action="{{ route('order.bid', $order->id) }}" class="mt-4 flex flex-wrap items-end gap-3" data-loading-text="Sending request...">
                                @csrf
                                <label class="min-w-52 flex-1 text-sm font-bold text-gray-700">Your delivery offer (₦)<input type="number" name="amount" min="0" max="1000000" step="0.01" value="{{ $order->delivery_fee }}" required class="mt-1 w-full rounded-lg border-gray-200 px-3 py-2"></label>
                                <button type="submit" class="rounded-lg bg-gray-900 px-5 py-3 font-bold text-white transition hover:bg-orange-600">Request assignment</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <livewire:rider-order-sections />
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const riderLatitude = @json($rider->latitude);
    const riderLongitude = @json($rider->longitude);
    const riderMap = L.map('riderMap').setView([riderLatitude || 6.5244, riderLongitude || 3.3792], riderLatitude ? 12 : 6);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors', maxZoom: 19 }).addTo(riderMap);
    if (riderLatitude && riderLongitude) L.marker([riderLatitude, riderLongitude]).addTo(riderMap).bindPopup('Your location');
    @foreach($orders as $order)
        @if($order->store && $order->store->latitude && $order->store->longitude)
            L.marker([{{ $order->store->latitude }}, {{ $order->store->longitude }}]).addTo(riderMap).bindPopup(@json($order->store->stores . ' pickup'));
        @endif
    @endforeach
    @foreach($nearbyOrders as $order)
        @if($order->store && $order->store->latitude && $order->store->longitude)
            L.marker([{{ $order->store->latitude }}, {{ $order->store->longitude }}]).addTo(riderMap).bindPopup(@json($order->store->stores . ' nearby pickup'));
        @endif
    @endforeach
</script>
@endsection
