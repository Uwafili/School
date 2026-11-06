@extends('layouts.navbar')

@section('content')
@php
    $isDelivered = $order->status === 'delivered';
    $riderIsAssigned = in_array($order->status, ['rider_assigned', 'picked_up', 'out_for_delivery', 'delivered'], true) && $order->rider;
    $mapLocations = [
        'pickup' => [
            'latitude' => $order->store?->latitude,
            'longitude' => $order->store?->longitude,
            'name' => $order->store?->stores ?? 'Store pickup',
        ],
        'destination' => [
            'latitude' => auth()->user()->latitude,
            'longitude' => auth()->user()->longitude,
            'name' => $order->customer_address,
        ],
        'rider' => [
            'latitude' => $order->rider?->latitude,
            'longitude' => $order->rider?->longitude,
            'name' => $order->rider?->user?->name ?? $order->rider?->name ?? 'Your rider',
        ],
    ];
    $hasRiderImage = $order->rider?->image;
@endphp
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #deliveryTrackingMap { z-index: 0; background: #e9edf0; }
    #deliveryTrackingMap .leaflet-tile-pane { filter: grayscale(.82) saturate(.55) brightness(1.04); }
    #deliveryTrackingMap .leaflet-control-attribution { font-size: 9px; }
    .tracking-pin { background: transparent; border: 0; }
    .rider-marker { display: grid; width: 42px; height: 42px; place-items: center; border: 3px solid white; border-radius: 50%; background: #14213d; color: white; box-shadow: 0 5px 16px rgb(15 23 42 / 28%); }
    .rider-marker svg { width: 23px; height: 23px; }
</style>

<main class="min-h-screen bg-[#f4f6f8] px-3 py-4 text-slate-900 sm:px-6 sm:py-7" data-order-tracking data-order-id="{{ $order->id }}">
    <div class="mx-auto max-w-7xl">
        <header class="mb-4 flex flex-wrap items-center justify-between gap-3 sm:mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50" aria-label="Back to orders">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6M9 12h12"/></svg>
                </a>
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[.16em] text-blue-700">FoodStore delivery</p>
                    <h1 class="text-lg font-black tracking-tight text-[#14213D] sm:text-xl">Track your order</h1>
                </div>
            </div>
            <div class="rounded-full border border-slate-200 bg-white px-3.5 py-2 text-right shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Order reference</p>
                <p class="text-xs font-black text-slate-800">{{ $order->order_reference }}</p>
            </div>
        </header>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,1.65fr)_minmax(340px,.85fr)] lg:items-start">
            <section class="relative isolate overflow-hidden rounded-[1.65rem] border border-slate-200 bg-[#e9edf0] shadow-sm" aria-label="Live delivery map">
                <div id="deliveryTrackingMap" class="h-[58vh] min-h-[390px] max-h-[690px] w-full sm:h-[620px]"></div>
                <div class="pointer-events-none absolute inset-x-0 top-0 z-[500] flex items-start justify-between gap-3 p-3 sm:p-5">
                    <div class="rounded-2xl border border-white/80 bg-white/95 px-3.5 py-2.5 shadow-lg shadow-slate-900/10 backdrop-blur sm:px-4">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="absolute inline-flex h-full w-full motion-safe:animate-ping rounded-full bg-blue-500 opacity-40"></span>
                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full {{ in_array($order->status, ['picked_up', 'out_for_delivery'], true) ? 'bg-blue-600' : 'bg-slate-400' }}"></span>
                            </span>
                            <span class="text-[10px] font-black uppercase tracking-[.14em] text-slate-600">{{ in_array($order->status, ['picked_up', 'out_for_delivery'], true) ? 'Live delivery' : 'Delivery route' }}</span>
                        </div>
                        <p class="mt-1 text-xs font-bold text-slate-900" data-map-status>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
                    </div>

                    <div class="rounded-2xl bg-[#14213D] px-4 py-3 text-white shadow-xl shadow-slate-900/20 sm:px-5 sm:py-4">
                        <p class="text-[9px] font-black uppercase tracking-[.18em] text-blue-200">{{ $isDelivered ? 'Delivery status' : 'Estimated arrival' }}</p>
                        <p class="mt-0.5 whitespace-nowrap text-xl font-black tracking-tight sm:text-2xl"><span data-eta-minutes>{{ $isDelivered ? '✓' : '--' }}</span> <span class="text-sm font-bold tracking-normal">{{ $isDelivered ? 'DELIVERED' : 'MIN ETA' }}</span></p>
                        <p class="mt-0.5 text-[10px] font-semibold text-slate-300" data-eta-arrival>{{ $isDelivered ? 'Order delivered successfully' : 'Updating route…' }}</p>
                    </div>
                </div>

                <div class="absolute right-3 top-24 z-[500] grid overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg sm:right-5 sm:top-28">
                    <button type="button" data-map-zoom-in class="grid h-10 w-10 place-items-center text-xl font-medium text-slate-700 transition hover:bg-slate-50" aria-label="Zoom in">+</button>
                    <span class="mx-2 border-t border-slate-100"></span>
                    <button type="button" data-map-zoom-out class="grid h-10 w-10 place-items-center text-xl font-medium text-slate-700 transition hover:bg-slate-50" aria-label="Zoom out">−</button>
                </div>

                @if($riderIsAssigned)
                    <article class="absolute inset-x-3 bottom-3 z-[500] max-w-md rounded-2xl border border-white/90 bg-white/95 p-3.5 shadow-xl shadow-slate-900/15 backdrop-blur sm:bottom-5 sm:left-5 sm:right-auto sm:w-[min(390px,calc(100%-2.5rem))] sm:p-4">
                        <div class="flex items-center gap-3">
                            @if($hasRiderImage)
                                <img src="{{ asset('storage/'.$order->rider->image) }}" alt="" class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-blue-100">
                            @else
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-blue-50 text-base font-black text-blue-800 ring-2 ring-blue-100">{{ strtoupper(substr($mapLocations['rider']['name'], 0, 1)) }}</span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-black text-[#14213D]">{{ $mapLocations['rider']['name'] }}</p>
                                <p class="mt-0.5 truncate text-xs font-medium text-slate-500">{{ $order->rider->vehicle ?: 'Delivery rider' }}@if($order->rider->vehicle_number) <span class="mx-1 text-slate-300">·</span>{{ $order->rider->vehicle_number }}@endif</p>
                            </div>
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-black text-blue-800"><svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1.7 12.5 4l3.4.5.5 3.4 2.3 2.1-2.3 2.5-.5 3.4-3.4.5L10 18.7l-2.5-2.3-3.4-.5-.5-3.4L1.3 10l2.3-2.1.5-3.4L7.5 4 10 1.7Zm3.2 6.7a.8.8 0 0 0-1.2-1l-2.8 3.4-1.3-1.2a.8.8 0 0 0-1.1 1.2l1.9 1.8a.8.8 0 0 0 1.2-.1l3.3-4.1Z" clip-rule="evenodd"/></svg> ASSIGNED</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                            <p class="min-w-0 truncate text-xs font-semibold text-slate-600" data-rider-trip-status>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
                            <div class="flex shrink-0 gap-2">
                                @if($order->rider->phone)
                                    <a href="tel:{{ $order->rider->phone }}" aria-label="Call rider" class="grid h-9 w-9 place-items-center rounded-full bg-blue-600 text-white transition hover:bg-blue-700"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a2 2 0 0 1 2-2h2l2 5-2 1.5a15 15 0 0 0 6.5 6.5L16 14l5 2v2a2 2 0 0 1-2 2C10.7 20 4 13.3 4 5Z"/></svg></a>
                                @endif
                                @if($order->rider->user_id)
                                    <a href="{{ route('chat.show', [$order->id, $order->rider->user_id]) }}" aria-label="Message rider" class="grid h-9 w-9 place-items-center rounded-full bg-slate-100 text-slate-700 transition hover:bg-slate-200"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 9h10M7 13h6m-2 8-4-4H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-6l-4 4Z"/></svg></a>
                                @endif
                            </div>
                        </div>
                    </article>
                @else
                    <div class="absolute bottom-3 left-3 right-3 z-[500] max-w-sm rounded-2xl border border-white/90 bg-white/95 p-3.5 shadow-xl shadow-slate-900/10 backdrop-blur sm:bottom-5 sm:left-5 sm:right-auto">
                        <p class="text-xs font-black text-[#14213D]">{{ $order->status === 'delivered' ? 'Delivery complete' : 'Your delivery route' }}</p>
                        <p class="mt-1 text-[11px] leading-4 text-slate-500">{{ $order->status === 'delivered' ? 'Your order has been delivered. Thanks for choosing FoodStore.' : 'We’ll show your rider here as soon as one is assigned.' }}</p>
                    </div>
                @endif

                <div class="pointer-events-none absolute bottom-3 right-3 z-[400] hidden rounded-lg bg-white/90 px-2 py-1 text-[9px] font-semibold text-slate-500 shadow-sm sm:block">Map data © OpenStreetMap</div>
            </section>

            <aside class="space-y-4">
                <livewire:order-tracking-progress :order="$order" :key="'order-tracking-'.$order->id" />

                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div><p class="text-[10px] font-black uppercase tracking-[.15em] text-slate-400">From the kitchen</p><h2 class="mt-1 text-sm font-black text-[#14213D]">{{ $order->store?->stores ?? 'FoodStore partner' }}</h2><p class="mt-1 text-xs text-slate-500">{{ $order->store?->address }}</p></div>
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-amber-50 text-amber-700"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6"/></svg></span>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @if($order->store?->phone)<a href="tel:{{ $order->store->phone }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">Call store</a>@endif
                        @if($order->store?->user_id)<a href="{{ route('chat.show', [$order->id, $order->store->user_id]) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">Message store</a>@endif
                        @if($order->rider?->user_id)<a href="{{ route('chat.show', [$order->id, $order->rider->user_id]) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">Message rider</a>@endif
                        @if($order->status === 'pending' && $order->store_approval_status === 'pending')
                            <form action="{{ route('customer.orders.cancel', $order) }}" method="POST" class="ml-auto">@csrf<button type="submit" onclick="return confirm('Cancel this order? Any paid amount will be refunded.')" class="rounded-lg px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-50">Cancel order</button></form>
                        @endif
                    </div>
                </section>

                <details class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <summary class="cursor-pointer list-none px-4 py-4 sm:px-5"><span class="flex items-center justify-between"><span class="text-sm font-black text-[#14213D]">Order items and payment</span><svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.2 7.5a.75.75 0 0 1 1.06.05L10 11.6l3.74-4.05a.75.75 0 1 1 1.1 1.02l-4.3 4.65a.75.75 0 0 1-1.1 0l-4.3-4.65a.75.75 0 0 1 .06-1.06Z" clip-rule="evenodd"/></svg></span></summary>
                    <div class="border-t border-slate-100 px-4 pb-4 pt-3 sm:px-5">
                        <p class="whitespace-pre-wrap text-xs leading-5 text-slate-600">{{ $order->items_description }}</p>
                        <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3"><span class="text-xs font-bold text-slate-500">Total</span><span class="text-sm font-black text-[#14213D]">₦{{ number_format($order->total_price, 2) }}</span></div>
                        <p class="mt-2 text-[10px] text-slate-500">Payment: {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }} · {{ ucfirst($order->payment_method ?? 'Not specified') }}</p>
                        @if($order->refund_status !== 'none')<p class="mt-2 rounded-lg bg-orange-50 p-2 text-xs text-orange-900">Refund: <strong>{{ ucfirst($order->refund_status) }}</strong>@if($order->refund_message) · {{ $order->refund_message }}@endif</p>@endif
                        @if($order->store_rejection_reason)<p class="mt-2 rounded-lg bg-red-50 p-2 text-xs text-red-800">Store note: {{ $order->store_rejection_reason }}</p>@endif
                    </div>
                </details>
            </aside>
        </div>
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (() => {
        const mapElement = document.getElementById('deliveryTrackingMap');
        if (!mapElement || !window.L) return;

        const orderId = @json($order->id);
        const liveLocationsUrl = @json(route('dashboard.live-riders'));
        const estimatedDeliveryAt = @json($order->estimated_delivery_at?->toIso8601String());
        const locations = @json($mapLocations);
        let status = @json($order->status);
        let isDelivered = status === 'delivered';
        const hasCoordinates = (location) => Number.isFinite(Number(location?.latitude))
            && Number.isFinite(Number(location?.longitude))
            && location?.latitude !== null && location?.latitude !== ''
            && location?.longitude !== null && location?.longitude !== '';
        const point = (location) => [Number(location.latitude), Number(location.longitude)];
        const pickup = hasCoordinates(locations.pickup) ? point(locations.pickup) : null;
        const destination = hasCoordinates(locations.destination) ? point(locations.destination) : null;
        let riderPosition = hasCoordinates(locations.rider) ? point(locations.rider) : null;
        let routeRequestVersion = 0;
        let locationPollTimer;
        let etaTimer;
        let etaDeadline = estimatedDeliveryAt ? new Date(estimatedDeliveryAt).getTime() : null;
        let routeLayer;
        let routeCasing;
        let riderMarker;

        const fallbackCenter = pickup || destination || [6.5244, 3.3792];
        const trackingMap = L.map(mapElement, { zoomControl: false, scrollWheelZoom: false }).setView(fallbackCenter, pickup && destination ? 13 : 14);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(trackingMap);
        document.querySelector('[data-map-zoom-in]')?.addEventListener('click', () => trackingMap.zoomIn());
        document.querySelector('[data-map-zoom-out]')?.addEventListener('click', () => trackingMap.zoomOut());

        const pin = (html, size, anchor) => L.divIcon({
            className: 'tracking-pin',
            html,
            iconSize: size,
            iconAnchor: anchor,
        });
        const pickupIcon = pin('<span style="display:grid;width:36px;height:36px;place-items:center;border:3px solid white;border-radius:12px;background:#14213d;color:white;box-shadow:0 4px 12px rgba(15,23,42,.22)"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6"/></svg></span>', [36, 36], [18, 30]);
        const riderIcon = pin('<span class="rider-marker"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 17h14l-1.2-5.2a2 2 0 0 0-2-1.5H8.2a2 2 0 0 0-2 1.5L5 17Zm0 0a2.5 2.5 0 1 0 5 0m4 0a2.5 2.5 0 1 0 5 0M9 10.3l1-3h4l1 3"/></svg></span>', [42, 42], [21, 21]);

        if (pickup) {
            L.marker(pickup, { icon: pickupIcon, zIndexOffset: 500 }).addTo(trackingMap)
                .bindTooltip(locations.pickup.name, { direction: 'top', offset: [0, -25] });
        }

        if (destination) {
            L.circle(destination, {
                radius: 190,
                color: '#3b82f6',
                weight: 1.5,
                opacity: .65,
                fillColor: '#60a5fa',
                fillOpacity: .16,
            }).addTo(trackingMap);
            L.circleMarker(destination, {
                radius: 8,
                color: '#fff',
                weight: 4,
                fillColor: '#2563eb',
                fillOpacity: 1,
            }).addTo(trackingMap).bindTooltip('Your delivery address', { direction: 'top', offset: [0, -8] });
        }

        const getRouteStart = () => riderPosition || pickup;
        const fitRoute = (coordinates) => {
            const bounds = L.latLngBounds(coordinates);
            trackingMap.fitBounds(bounds, { padding: [64, 64], maxZoom: 15 });
        };
        const setEta = (minutes) => {
            if (isDelivered) return;
            const etaElement = document.querySelector('[data-eta-minutes]');
            const arrivalElement = document.querySelector('[data-eta-arrival]');
            if (!etaElement || !arrivalElement) return;
            if (Number.isFinite(minutes)) {
                etaElement.textContent = String(Math.max(0, Math.ceil(minutes)));
                const arrival = new Date(Date.now() + Math.max(0, minutes) * 60000);
                etaElement.setAttribute('aria-label', `${Math.max(0, Math.ceil(minutes))} minutes estimated delivery time`);
                arrivalElement.textContent = `Arriving around ${arrival.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })}`;
                return;
            }
            if (etaDeadline && Number.isFinite(etaDeadline)) {
                const remaining = Math.max(0, Math.ceil((etaDeadline - Date.now()) / 60000));
                etaElement.textContent = String(remaining);
                arrivalElement.textContent = `Arriving around ${new Date(etaDeadline).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })}`;
                return;
            }
            etaElement.textContent = '--';
            arrivalElement.textContent = 'Route estimate unavailable';
        };
        const updateRiderMarker = (nextPosition) => {
            if (!riderMarker) {
                riderMarker = L.marker(nextPosition, { icon: riderIcon, zIndexOffset: 1000 }).addTo(trackingMap);
                riderMarker.bindTooltip(locations.rider.name, { direction: 'top', offset: [0, -23] });
                return;
            }
            const startPosition = riderMarker.getLatLng();
            const beganAt = performance.now();
            const duration = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 1200;
            const animate = (now) => {
                const progress = duration ? Math.min((now - beganAt) / duration, 1) : 1;
                const eased = 1 - Math.pow(1 - progress, 3);
                riderMarker.setLatLng([
                    startPosition.lat + (nextPosition[0] - startPosition.lat) * eased,
                    startPosition.lng + (nextPosition[1] - startPosition.lng) * eased,
                ]);
                if (progress < 1) window.requestAnimationFrame(animate);
            };
            window.requestAnimationFrame(animate);
        };
        const drawRoute = async () => {
            const start = getRouteStart();
            if (!start || !destination) {
                if (start) trackingMap.setView(start, 15);
                return;
            }
            updateRiderMarker(start);
            const version = ++routeRequestVersion;
            if (!routeLayer) {
                routeCasing = L.polyline([start, destination], { color: '#fff', weight: 11, opacity: .9, lineCap: 'round', lineJoin: 'round' }).addTo(trackingMap);
                routeLayer = L.polyline([start, destination], { color: '#14213d', weight: 6, opacity: .9, lineCap: 'round', lineJoin: 'round' }).addTo(trackingMap);
            } else {
                routeCasing.setLatLngs([start, destination]);
                routeLayer.setLatLngs([start, destination]);
            }
            fitRoute([start, destination]);

            try {
                const response = await fetch(`https://router.project-osrm.org/route/v1/driving/${start[1]},${start[0]};${destination[1]},${destination[0]}?overview=full&geometries=geojson`, {
                    headers: { Accept: 'application/json' },
                });
                if (!response.ok) throw new Error(`Route service returned ${response.status}`);
                const result = await response.json();
                if (version !== routeRequestVersion) return;
                const route = result.routes?.[0];
                if (!route) throw new Error('No driving route is available for these locations.');
                const coordinates = route.geometry.coordinates.map(([longitude, latitude]) => [latitude, longitude]);
                routeCasing.setLatLngs(coordinates);
                routeLayer.setLatLngs(coordinates);
                fitRoute(coordinates);
                etaDeadline = Date.now() + route.duration * 1000;
                setEta(route.duration / 60);
            } catch (error) {
                if (version !== routeRequestVersion) return;
                console.warn('Unable to calculate the delivery route.', error);
                setEta(null);
            }
        };

        const pollRiderLocation = async () => {
            if (!['picked_up', 'out_for_delivery'].includes(status)) return;
            try {
                const response = await fetch(liveLocationsUrl, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error(`Location service returned ${response.status}`);
                const orders = await response.json();
                const liveOrder = orders.find((item) => String(item.order_id) === String(orderId));
                if (!liveOrder || !Number.isFinite(Number(liveOrder.latitude)) || !Number.isFinite(Number(liveOrder.longitude))) return;
                riderPosition = [Number(liveOrder.latitude), Number(liveOrder.longitude)];
                await drawRoute();
            } catch (error) {
                console.warn('Unable to refresh rider location.', error);
            }
        };

        window.addEventListener('foodstore:order-tracking-updated', ({ detail }) => {
            if (String(detail.orderId) !== String(orderId)) return;

            status = detail.status;
            isDelivered = status === 'delivered';
            const statusLabel = status.replaceAll('_', ' ');
            document.querySelectorAll('[data-map-status], [data-rider-trip-status]').forEach((element) => {
                element.textContent = statusLabel.charAt(0).toUpperCase() + statusLabel.slice(1);
            });

            const nextRider = {
                latitude: detail.riderLatitude,
                longitude: detail.riderLongitude,
                name: detail.riderName || locations.rider.name,
            };
            if (hasCoordinates(nextRider)) {
                locations.rider = nextRider;
                riderPosition = point(nextRider);
                drawRoute();
            }

            if (['picked_up', 'out_for_delivery'].includes(status) && !locationPollTimer) {
                pollRiderLocation();
                locationPollTimer = window.setInterval(pollRiderLocation, 15000);
            } else if (!['picked_up', 'out_for_delivery'].includes(status) && locationPollTimer) {
                window.clearInterval(locationPollTimer);
                locationPollTimer = undefined;
            }

            if (isDelivered) {
                window.clearInterval(locationPollTimer);
                window.clearInterval(etaTimer);
                const etaElement = document.querySelector('[data-eta-minutes]');
                const arrivalElement = document.querySelector('[data-eta-arrival]');
                if (etaElement) etaElement.textContent = '✓';
                if (arrivalElement) arrivalElement.textContent = 'Order delivered successfully';
            } else {
                setEta(null);
            }
        });

        document.querySelector('[data-map-status]')?.setAttribute('title', `Order status: ${status.replaceAll('_', ' ')}`);
        if (getRouteStart() && destination) {
            drawRoute();
        } else {
            const availablePoints = [pickup, destination].filter(Boolean);
            if (availablePoints.length > 1) fitRoute(availablePoints);
            else if (availablePoints.length === 1) trackingMap.setView(availablePoints[0], 15);
            setEta(null);
        }
        if (estimatedDeliveryAt) {
            setEta(null);
            etaTimer = window.setInterval(() => setEta(null), 30000);
        }
        if (['picked_up', 'out_for_delivery'].includes(status)) {
            pollRiderLocation();
            locationPollTimer = window.setInterval(pollRiderLocation, 15000);
        }

        window.addEventListener('pagehide', () => {
            window.clearInterval(locationPollTimer);
            window.clearInterval(etaTimer);
            trackingMap.remove();
        }, { once: true });
    })();
</script>
@endsection
