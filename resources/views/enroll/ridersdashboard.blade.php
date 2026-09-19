@extends('layouts.navbar')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<div class="min-h-screen bg-slate-50 px-3 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-black uppercase tracking-[.18em] text-orange-500">Delivery command center</p><h1 class="mt-1 text-3xl font-black text-gray-900">Rider dashboard</h1><p class="mt-2 text-sm text-gray-500">Manage jobs, confirm pickups, and complete deliveries safely.</p></div><div class="flex gap-2"><a href="{{ route('rider.assigned-orders') }}" class="rounded-full bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-gray-200">All orders <span class="ml-1 rounded-full bg-yellow-100 px-2 py-0.5 text-yellow-800">{{ $orders->count() }}</span></a><a href="{{ route('rider.notifications') }}" class="rounded-full bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-gray-200">Notifications</a></div></div>
        @if(session('success'))<div class="mb-5 rounded-2xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{{ session('success') }}</div>@endif
        @if(session('error') || session('warning'))<div class="mb-5 rounded-2xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ session('error') ?? session('warning') }}</div>@endif

        <section class="mb-6 grid gap-5 lg:grid-cols-[1fr_20rem]">
            <div class="rounded-3xl bg-gradient-to-br from-yellow-400 to-orange-500 p-6 text-gray-900 shadow-lg shadow-yellow-100 sm:p-8"><div class="flex items-start justify-between gap-4"><div><p class="text-xs font-black uppercase tracking-widest text-orange-950/60">Your route today</p><h2 class="mt-2 text-2xl font-black">Ready when you are, {{ auth()->user()->name }}.</h2><p class="mt-2 max-w-lg text-sm font-semibold text-orange-950/70">Stay online to receive new delivery jobs from connected stores.</p></div><span class="text-4xl">🚴</span></div><div class="mt-7 flex flex-wrap gap-3 text-xs font-black"><span class="rounded-full bg-white/80 px-3 py-2">{{ $assignedCount }} waiting</span><span class="rounded-full bg-white/80 px-3 py-2">{{ $activeCount }} active</span><span class="rounded-full bg-white/80 px-3 py-2">{{ $completedCount }} delivered</span></div></div>
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100"><div class="flex items-center justify-between"><div><p class="text-xs font-black uppercase tracking-widest text-gray-400">Availability</p><h2 class="mt-1 text-xl font-black text-gray-900">{{ $Rider->is_online ? 'You are online' : 'You are offline' }}</h2></div><span class="h-3 w-3 rounded-full {{ $Rider->is_online ? 'bg-green-500' : 'bg-gray-300' }}"></span></div><p class="mt-3 text-sm text-gray-500">{{ $Rider->is_online ? 'Go offline when you need a break.' : 'Go online to receive jobs.' }}</p><form action="{{ route('rider.availability') }}" method="POST" class="mt-5">@csrf<input type="hidden" name="is_online" value="{{ $Rider->is_online ? 0 : 1 }}"><button type="submit" class="w-full rounded-xl {{ $Rider->is_online ? 'bg-gray-900 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }} py-3 text-sm font-black text-white">{{ $Rider->is_online ? 'Go offline' : 'Go online' }}</button></form></div>
        </section>

        <section class="mb-6 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
            <div class="mb-4"><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Live route view</p><h2 class="mt-1 text-xl font-black text-gray-900">Pickup points near your jobs</h2><p class="mt-1 text-sm text-gray-500">Store locations appear when sellers have shared their location.</p></div>
            <div id="riderMap" class="h-64 overflow-hidden rounded-2xl bg-yellow-50 sm:h-80"></div>
        </section>

        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4"><div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100"><p class="text-xs font-bold text-gray-400">Waiting</p><p class="mt-1 text-2xl font-black text-orange-500">{{ $assignedCount }}</p></div><div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100"><p class="text-xs font-bold text-gray-400">In transit</p><p class="mt-1 text-2xl font-black text-blue-500">{{ $activeCount }}</p></div><div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100"><p class="text-xs font-bold text-gray-400">Delivered</p><p class="mt-1 text-2xl font-black text-green-600">{{ $completedCount }}</p></div><div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100"><p class="text-xs font-bold text-gray-400">Vehicle</p><p class="mt-1 truncate text-lg font-black text-purple-600">{{ $Rider->vehicle }}</p></div></div>

        <section><div class="mb-4 flex items-end justify-between"><div><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Connected orders</p><h2 class="mt-1 text-2xl font-black text-gray-900">Delivery jobs</h2></div><span class="text-sm font-bold text-gray-400">{{ $orders->count() }} total</span></div>@if($orders->isEmpty())<div class="rounded-3xl bg-white p-10 text-center shadow-sm ring-1 ring-gray-100"><p class="text-lg font-black text-gray-800">No delivery jobs yet</p><p class="mt-1 text-sm text-gray-500">Stores will send work here when you are online.</p></div>@else<div class="grid gap-4 lg:grid-cols-2">@foreach($orders as $order)<article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100"><div class="flex items-start justify-between gap-3"><div><p class="text-xs font-black uppercase tracking-widest text-gray-400">Order #{{ $order->id }}</p><h3 class="mt-1 text-lg font-black text-gray-900">{{ $order->store->stores ?? 'FoodStore seller' }}</h3></div><span class="rounded-full px-3 py-1 text-xs font-black {{ $order->status === 'assigned' || $order->status === 'pending' ? 'bg-orange-100 text-orange-700' : ($order->status === 'accepted' ? 'bg-blue-100 text-blue-700' : ($order->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600')) }}">{{ $order->status === 'pending' ? 'Open job' : ucfirst($order->status) }}</span></div><div class="my-4 grid gap-3 text-sm sm:grid-cols-2"><div class="rounded-xl bg-gray-50 p-3"><p class="text-[10px] font-black uppercase text-gray-400">Pickup</p><p class="mt-1 font-bold text-gray-700">{{ $order->store->address ?? 'Store address' }}</p></div><div class="rounded-xl bg-gray-50 p-3"><p class="text-[10px] font-black uppercase text-gray-400">Drop-off</p><p class="mt-1 font-bold text-gray-700">{{ $order->customer_address }}</p></div></div><div class="mb-4 flex items-center justify-between border-t border-gray-100 pt-3 text-sm"><span class="text-gray-500">Customer: <strong class="text-gray-800">{{ $order->customer_name }}</strong></span><strong class="text-yellow-600">₦{{ number_format($order->total_price, 2) }}</strong></div>@if($order->status === 'pending' || $order->status === 'assigned')<div class="grid grid-cols-2 gap-2"><form action="{{ route('order.accept', $order->id) }}" method="POST">@csrf<button type="submit" class="w-full rounded-xl bg-green-600 py-2.5 text-xs font-black text-white hover:bg-green-700">Pick up job</button></form>@if($order->status === 'assigned')<form action="{{ route('order.reject', $order->id) }}" method="POST">@csrf<button type="submit" class="w-full rounded-xl bg-red-100 py-2.5 text-xs font-black text-red-700 hover:bg-red-200">Reject</button></form>@endif</div>@elseif($order->status === 'accepted')<div class="space-y-3"><form action="{{ route('rider.pickup', $order->id) }}" method="POST">@csrf<button type="submit" class="w-full rounded-xl bg-blue-600 py-2.5 text-xs font-black text-white hover:bg-blue-700">Confirm pickup</button></form><form action="{{ route('rider.deliver', $order->id) }}" method="POST" class="flex gap-2">@csrf<input name="recipient_code" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required placeholder="4-digit customer code" class="min-w-0 flex-1 rounded-xl border-gray-200 px-3 text-xs focus:border-yellow-500 focus:ring-yellow-300"><button type="submit" class="rounded-xl bg-gray-900 px-4 py-2.5 text-xs font-black text-white hover:bg-yellow-500 hover:text-gray-900">Deliver</button></form><p class="text-[11px] text-gray-400">Ask the recipient to show the 4-digit code from their order.</p></div>@elseif($order->status === 'completed')<p class="rounded-xl bg-green-50 px-3 py-2 text-center text-xs font-bold text-green-700">Recipient verified · Delivery complete</p>@endif</article>@endforeach</div>@endif</section>
    </div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const riderLatitude = @json($Rider->latitude);
    const riderLongitude = @json($Rider->longitude);
    const riderMap = L.map('riderMap').setView([riderLatitude || 6.5244, riderLongitude || 3.3792], riderLatitude ? 12 : 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(riderMap);
    if (riderLatitude && riderLongitude) L.marker([riderLatitude, riderLongitude]).addTo(riderMap).bindPopup('Your current location');
    @foreach($orders as $order)
        @if($order->store && $order->store->latitude && $order->store->longitude)
            L.marker([{{ $order->store->latitude }}, {{ $order->store->longitude }}]).addTo(riderMap).bindPopup('Pickup: {{ addslashes($order->store->stores) }}');
        @endif
    @endforeach

    function refreshRiderLocation() {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(function (position) {
            fetch('{{ route('location.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ latitude: position.coords.latitude, longitude: position.coords.longitude }),
            });
        }, function () {});
    }

    @if($Rider->is_online)
        refreshRiderLocation();
        setInterval(refreshRiderLocation, 15000);
    @endif
</script>
@endsection
