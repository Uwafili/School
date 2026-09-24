@extends('layouts.navbar')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <div class="min-h-screen bg-slate-50 px-3 py-5 sm:px-6 sm:py-8 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-5 flex items-center justify-between gap-4">
                <div class="flex min-w-0 items-center gap-3">
                    <span
                        class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-yellow-400 text-lg font-black text-gray-900">{{ strtoupper(substr(Auth::user()->name ?? 'F', 0, 1)) }}</span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-gray-500">Welcome back</p>
                        <h1 class="truncate text-lg font-black text-gray-900 sm:text-2xl">
                            {{ Auth::user()->name ?? 'Food lover' }}
                        </h1>
                    </div>
                </div>
                <a href="#settings"
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-white text-gray-700 shadow-sm ring-1 ring-gray-200 transition hover:text-yellow-600"
                    aria-label="Open account settings">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round"
                            d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7ZM19.4 15l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a2 2 0 0 0-3.4 1.4v.3a2 2 0 1 1-4 0v-.3a2 2 0 0 0-3.4-1.4l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1A2 2 0 0 0 1.7 11H2a2 2 0 1 1 0-4h-.3a2 2 0 0 0 1.4-3.4l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1A2 2 0 0 0 9.3 2.3V2a2 2 0 1 1 4 0v.3a2 2 0 0 0 3.4 1.4l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1A2 2 0 0 0 20.9 10h.3a2 2 0 1 1 0 4h-.3a2 2 0 0 0-1.5 1Z" />
                    </svg>
                </a>
            </div>

            <section
                class="mb-6 grid gap-4 overflow-hidden rounded-3xl bg-gradient-to-r from-yellow-400 to-orange-500 p-5 text-gray-900 shadow-lg shadow-yellow-100 sm:grid-cols-[1fr_auto] sm:items-center sm:p-7">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.16em] text-orange-950/60">Hungry?</p>
                    <h2 class="mt-1 text-2xl font-black sm:text-3xl">Find something delicious today.</h2>
                    <p class="mt-2 text-sm font-semibold text-orange-950/70">Fresh meals from FoodStore sellers near you.
                    </p>
                </div>
                <a href="#food-feed"
                    class="inline-flex items-center justify-center gap-2 rounded-full bg-gray-900 px-5 py-3 text-sm font-black text-white transition hover:bg-gray-700">Explore
                    food <span>→</span></a>
            </section>

            <div class="mb-7 grid gap-4 lg:grid-cols-[.8fr_1.2fr]">
                <section
                    class="rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 p-5 text-gray-900 shadow-md sm:p-6">
                    <div class="mb-5 flex items-center justify-between"><span
                            class="rounded-full bg-black/10 px-3 py-1 text-[10px] font-black uppercase tracking-widest">FoodStore
                            wallet</span><strong class="text-xl">FS</strong></div>
                    <p class="text-xs font-bold text-orange-950/60">Available balance</p>
                    <p class="mt-1 text-3xl font-black">$0.00</p>
                    <div class="mt-5 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-orange-950/60">Member</p>
                            <p class="truncate text-sm font-extrabold">{{ Auth::user()->name ?? 'FoodStore user' }}</p>
                        </div><span
                            class="rounded-full bg-white/90 px-3 py-1 text-xs font-black text-orange-600">Active</span>
                    </div>
                </section>
                <section>
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-lg font-black text-gray-900">Categories</h2><span
                            class="text-xs font-bold text-gray-400">Browse all</span>
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ([['Pizza', 'generated.jpg', 'food.pizza'], ['Burgers', 'front.avif', 'food.burger'], ['Salads', 'brown.jpg', 'food.salad'], ['Drinks', 'drink.webp', 'food.drinks']] as $category)
                            <a href="{{ route($category[2]) }}"
                                class="group rounded-2xl bg-white p-2 text-center shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-1 hover:ring-yellow-300 sm:p-3"><img
                                    src="{{ asset('asset/' . $category[1]) }}" alt="{{ $category[0] }}"
                                    class="mx-auto h-12 w-12 rounded-xl object-cover sm:h-16 sm:w-16"><span
                                    class="mt-2 block truncate text-xs font-bold text-gray-700 group-hover:text-yellow-600">{{ $category[0] }}</span></a>
                        @endforeach
                    </div>
                </section>
            </div>

            <section class="mb-8 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
                <div class="mb-4"><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Live nearby</p><h2 class="mt-1 text-xl font-black text-gray-900">Food stores and riders around you</h2><p class="mt-1 text-sm text-gray-500">See registered stores and online delivery riders who have shared their current location.</p></div>
                <div id="customerMap" class="h-64 overflow-hidden rounded-2xl bg-yellow-50 sm:h-80"></div>
            </section>

            @if($orders->isNotEmpty())
                <section class="mb-8 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
                    <div class="mb-5 flex items-end justify-between gap-4">
                        <div><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Track your food</p><h2 class="mt-1 text-xl font-black text-gray-900">Active orders</h2></div>
                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-black text-yellow-800">{{ $orders->count() }} open</span>
                    </div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        @foreach($orders as $order)
                            <article class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div><p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Order #{{ $order->id }}</p><h3 class="mt-1 text-sm font-black text-gray-900">{{ $order->store->stores ?? 'FoodStore order' }}</h3></div>
                                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-black text-blue-700">{{ ucfirst($order->status) }}</span>
                                </div>
                                <div class="mt-4 flex items-center justify-between gap-3 border-t border-gray-200 pt-3"><div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Recipient code</p><p class="mt-1 text-2xl font-black tracking-[.3em] text-purple-700">{{ $order->recipient_code ?? '----' }}</p></div><p class="max-w-36 text-right text-[11px] leading-4 text-gray-500">Show this code to your rider to confirm delivery.</p></div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($orders->where('status', 'completed')->isNotEmpty())
                <section class="mb-8 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
                    <div class="mb-5"><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Share your experience</p><h2 class="mt-1 text-xl font-black text-gray-900">Rate your completed orders</h2></div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        @foreach($orders->where('status', 'completed') as $order)
                            @php $existingRating = $ratings->get($order->id); @endphp
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4"><div class="flex items-center justify-between gap-3"><div><p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Order #{{ $order->id }}</p><p class="mt-1 text-sm font-black text-gray-800">{{ $order->store->stores ?? 'FoodStore store' }}</p></div><span class="text-xs font-bold text-green-600">Delivered</span></div>@if($existingRating)<p class="mt-3 text-sm font-black text-yellow-600">{{ str_repeat('★', $existingRating->rating) }}{{ str_repeat('☆', 5 - $existingRating->rating) }} <span class="ml-1 text-xs text-gray-500">Your rating</span></p>@else<form action="{{ route('order.rating', $order) }}" method="POST" class="mt-4">@csrf<div class="flex items-center gap-2"><select name="rating" required class="rounded-xl border-gray-200 bg-white px-3 py-2 text-sm font-bold text-yellow-600 focus:border-yellow-500 focus:ring-yellow-300"><option value="">Stars</option><option value="5">★★★★★</option><option value="4">★★★★☆</option><option value="3">★★★☆☆</option><option value="2">★★☆☆☆</option><option value="1">★☆☆☆☆</option></select><input name="review" maxlength="500" placeholder="Optional review" class="min-w-0 flex-1 rounded-xl border-gray-200 px-3 py-2 text-xs focus:border-yellow-500 focus:ring-yellow-300"><button type="submit" class="rounded-xl bg-gray-900 px-3 py-2 text-xs font-black text-white hover:bg-yellow-500 hover:text-gray-900">Rate</button></div></form>@endif</div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section id="food-feed" class="mb-8">
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Fresh from sellers</p>
                        <h2 class="mt-1 text-2xl font-black text-gray-900">Popular food</h2>
                    </div><span class="text-sm font-bold text-gray-400">{{ $posts->count() }} available</span>
                </div>
                @if($posts->isEmpty())
                    <div class="rounded-3xl bg-white p-10 text-center shadow-sm ring-1 ring-gray-100">
                        <p class="text-lg font-black text-gray-800">No meals posted yet</p>
                        <p class="mt-1 text-sm text-gray-500">Fresh dishes from local sellers will appear here.</p>
                    </div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach($posts as $post)
                            @php
                                $fallbackImages = ['pizza' => 'generated.jpg', 'burger' => 'front.avif', 'salad' => 'brown.jpg', 'drinks' => 'drink.webp'];
                                $image = $post->image ? 'storage/' . $post->image : 'asset/' . ($fallbackImages[$post->category] ?? 'front.avif');
                            @endphp
                            <article
                                class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="relative h-40 overflow-hidden bg-yellow-100"><img src="{{ asset($image) }}"
                                        alt="{{ $post->title }}"
                                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"><span
                                        class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-black uppercase text-orange-600">{{ ucfirst($post->category ?? 'Meal') }}</span>
                                </div>
                                <div class="p-4">
                                    <div class="mb-3 flex items-start justify-between gap-2">
                                        <h3 class="line-clamp-2 text-base font-black text-gray-900">{{ $post->title }}</h3><span
                                            class="shrink-0 text-sm font-black text-yellow-600">₦{{ $post->price }}</span>
                                    </div>
                                    <p class="mb-4 line-clamp-2 text-xs leading-5 text-gray-500">{{ $post->description }}</p>
                                    <div class="mb-4 flex items-center gap-2 border-t border-gray-100 pt-3"><span
                                            class="grid h-7 w-7 place-items-center rounded-full bg-purple-100 text-xs font-black text-purple-700">{{ strtoupper(substr($post->user->name ?? 'S', 0, 1)) }}</span><span
                                            class="min-w-0 truncate text-xs font-bold text-gray-600">{{ $post->user->name ?? 'FoodStore seller' }}</span><span
                                            class="ml-auto shrink-0 text-[10px] text-gray-400">{{ $post->created_at->diffForHumans(null, true) }}</span>
                                    </div>
                                    <form action="{{ route('add.cart', $post->id) }}" method="POST">@csrf<button type="submit"
                                            class="w-full rounded-xl bg-gray-900 py-2.5 text-xs font-black text-white transition hover:bg-yellow-500 hover:text-gray-900">Add
                                            to cart</button></form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section id="settings" x-data="{ notifications: true }"
                class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Your account</p>
                        <h2 class="mt-1 text-xl font-black text-gray-900">Settings</h2>
                    </div><span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">Secure</span>
                </div>
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="mb-2 px-1 text-xs font-black text-gray-900">General</h3>
                        <div class="overflow-hidden rounded-2xl bg-gray-50"><a href="#"
                                class="flex items-center gap-3 border-b border-gray-100 px-4 py-3.5 transition hover:bg-yellow-50"><span
                                    class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-blue-100 text-blue-600">◎</span><span
                                    class="flex-1 text-sm font-semibold text-gray-700">Profile details</span><span
                                    class="text-gray-400">›</span></a><button type="button"
                                @click="notifications = !notifications"
                                class="flex w-full items-center gap-3 px-4 py-3.5 text-left transition hover:bg-yellow-50"><span
                                    class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-yellow-100 text-yellow-600">♧</span><span
                                    class="flex-1 text-sm font-semibold text-gray-700">Notifications</span><span
                                    class="relative h-6 w-11 rounded-full transition"
                                    :class="notifications ? 'bg-green-500' : 'bg-gray-300'"><span
                                        class="absolute top-1 h-4 w-4 rounded-full bg-white shadow transition"
                                        :class="notifications ? 'right-1' : 'left-1'"></span></span></button></div>
                    </div>
                    <div>
                        <h3 class="mb-2 px-1 text-xs font-black text-gray-900">Other settings</h3>
                        <div class="overflow-hidden rounded-2xl bg-gray-50"><a href="{{ route('about') }}"
                                class="flex items-center gap-3 border-b border-gray-100 px-4 py-3.5 transition hover:bg-purple-50"><span
                                    class="grid h-8 w-8 place-items-center rounded-lg bg-purple-100 font-black text-purple-600">i</span><span
                                    class="flex-1 text-sm font-semibold text-gray-700">About FoodStore</span><span
                                    class="text-gray-400">›</span></a><a href="{{ route('cart') }}"
                                class="flex items-center gap-3 px-4 py-3.5 transition hover:bg-orange-50"><span
                                    class="grid h-8 w-8 place-items-center rounded-lg bg-orange-100 text-orange-600">₦</span><span
                                    class="flex-1 text-sm font-semibold text-gray-700">Orders & payments</span><span
                                    class="text-gray-400">›</span></a></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const customerMap = L.map('customerMap').setView([6.5244, 3.3792], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(customerMap);

        function yellowMarker(color = '#facc15') {
            return L.divIcon({
                className: 'custom-pin',
                html: `<span style="display:block;width:18px;height:18px;border-radius:50%;background:${color};border:3px solid #f59e0b;box-shadow:0 0 0 2px rgba(255,255,255,0.8);"></span>`,
                iconSize: [18, 18],
                iconAnchor: [9, 9],
                popupAnchor: [0, -10]
            });
        }

        const customerLatitude = @json(auth()->user()->latitude);
        const customerLongitude = @json(auth()->user()->longitude);
        if (customerLatitude && customerLongitude) {
            L.marker([customerLatitude, customerLongitude], { icon: L.divIcon({ className: 'custom-pin', html: '<span style="display:block;width:18px;height:18px;border-radius:50%;background:#2563eb;border:3px solid #1d4ed8;box-shadow:0 0 0 2px rgba(255,255,255,0.8);"></span>', iconSize: [18,18], iconAnchor:[9,9], popupAnchor:[0,-10] }) }).addTo(customerMap).bindPopup('Your current location');
        }

        @foreach($stores as $store)
            L.marker([{{ $store->latitude }}, {{ $store->longitude }}], { icon: yellowMarker('#facc15') }).addTo(customerMap).bindPopup('Food store: {{ addslashes($store->stores) }}');
        @endforeach

        @foreach($riders as $rider)
            L.marker([{{ $rider->latitude }}, {{ $rider->longitude }}], { icon: yellowMarker('#fbbf24') }).addTo(customerMap).bindPopup('Online rider: {{ addslashes($rider->user->name ?? $rider->name) }}');
        @endforeach

        const assignedRiderMarkers = {};
        const orderRoutes = {};
        const orderDestinationMarkers = {};
        const orderPickupMarkers = {};
        let hasFittedTrackingBounds = false;

        function destinationMarker() {
            return L.divIcon({
                className: 'custom-pin',
                html: '<span style="display:block;width:18px;height:18px;border-radius:50%;background:#2563eb;border:3px solid #1d4ed8;box-shadow:0 0 0 2px rgba(255,255,255,0.8);"></span>',
                iconSize: [18, 18],
                iconAnchor: [9, 9],
                popupAnchor: [0, -10]
            });
        }

        function drawOrderRoute(order) {
            const pickup = order.pickup;
            const destination = order.destination;
            if (!destination?.latitude || !destination?.longitude) return;

            const end = [parseFloat(destination.latitude), parseFloat(destination.longitude)];

            if (pickup?.latitude && pickup?.longitude) {
                const pickupCoordinates = [parseFloat(pickup.latitude), parseFloat(pickup.longitude)];
                if (!orderPickupMarkers[order.order_id]) {
                    orderPickupMarkers[order.order_id] = L.marker(pickupCoordinates, { icon: yellowMarker('#facc15') }).addTo(customerMap);
                } else {
                    orderPickupMarkers[order.order_id].setLatLng(pickupCoordinates);
                }
                orderPickupMarkers[order.order_id].bindPopup(`Order #${order.order_id} pickup: ${pickup.name}`);
            }

            if (!orderDestinationMarkers[order.order_id]) {
                orderDestinationMarkers[order.order_id] = L.marker(end, { icon: destinationMarker() }).addTo(customerMap);
            } else {
                orderDestinationMarkers[order.order_id].setLatLng(end);
            }
            orderDestinationMarkers[order.order_id].bindPopup(`Order #${order.order_id} delivery location`);

            const hasRiderLocation = order.latitude && order.longitude;
            const start = hasRiderLocation
                ? [parseFloat(order.latitude), parseFloat(order.longitude)]
                : (pickup?.latitude && pickup?.longitude
                    ? [parseFloat(pickup.latitude), parseFloat(pickup.longitude)]
                    : null);
            if (!start) return;

            if (orderRoutes[order.order_id]) {
                orderRoutes[order.order_id].setLatLngs([start, end]);
            } else {
                orderRoutes[order.order_id] = L.polyline([start, end], {
                    color: '#f59e0b',
                    weight: 5,
                    opacity: 0.8,
                    dashArray: '10 8'
                }).addTo(customerMap);
            }

            fetch(`https://router.project-osrm.org/route/v1/driving/${start[1]},${start[0]};${end[1]},${end[0]}?overview=full&geometries=geojson`)
                .then(response => response.json())
                .then(route => {
                    const coordinates = route.routes?.[0]?.geometry?.coordinates;
                    if (coordinates?.length) {
                        orderRoutes[order.order_id].setLatLngs(coordinates.map(([longitude, latitude]) => [latitude, longitude]));
                    }
                })
                .catch(() => {});
        }

        function refreshAssignedRiders() {
            fetch('{{ route('dashboard.live-riders') }}', { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(orders => {
                    const trackingBounds = [];
                    orders.forEach(order => {
                        drawOrderRoute(order);
                        if (order.pickup?.latitude && order.pickup?.longitude) {
                            trackingBounds.push([parseFloat(order.pickup.latitude), parseFloat(order.pickup.longitude)]);
                        }
                        if (order.destination?.latitude && order.destination?.longitude) {
                            trackingBounds.push([parseFloat(order.destination.latitude), parseFloat(order.destination.longitude)]);
                        }
                        if (order.latitude && order.longitude) {
                            const coordinates = [parseFloat(order.latitude), parseFloat(order.longitude)];
                            trackingBounds.push(coordinates);
                            if (!assignedRiderMarkers[order.order_id]) {
                                assignedRiderMarkers[order.order_id] = L.marker(coordinates, { icon: yellowMarker('#f59e0b') }).addTo(customerMap);
                            } else {
                                assignedRiderMarkers[order.order_id].setLatLng(coordinates);
                            }
                            assignedRiderMarkers[order.order_id].bindPopup(`Your rider for order #${order.order_id}: ${order.name}<br>Status: ${order.status}<br>Last update: ${order.updated_at ?? 'just now'}`);
                        }
                    });
                    if (!hasFittedTrackingBounds && trackingBounds.length) {
                        customerMap.fitBounds(trackingBounds, { padding: [24, 24], maxZoom: 14 });
                        hasFittedTrackingBounds = true;
                    }
                })
                .catch(() => {});
        }
        refreshAssignedRiders();
        setInterval(refreshAssignedRiders, 15000);
    </script>
@endsection