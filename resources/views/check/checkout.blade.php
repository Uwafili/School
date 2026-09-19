@extends('layouts.navbar')

@section('content')
@php
    $cart = session('cart', []);
    $grandTotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
@endphp

<div class="checkout-page min-h-screen bg-[#edf8f5] px-3 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex items-center justify-between">
            <div><p class="text-xs font-black uppercase tracking-[.16em] text-teal-600">FoodStore checkout</p><h1 class="mt-1 text-2xl font-black text-gray-900 sm:text-3xl">Complete your order</h1></div>
            <a href="{{ route('cart') }}" class="rounded-full bg-white px-4 py-2 text-sm font-bold text-gray-600 shadow-sm ring-1 ring-teal-100 transition hover:text-teal-700">Back to cart</a>
        </div>

        <form id="checkoutForm" action="{{ route('payment.pay') }}" method="POST" class="grid gap-6 lg:grid-cols-[1fr_20rem]">
            @csrf
            <input type="hidden" name="amount" value="{{ $grandTotal }}">
            <input type="hidden" name="delivery_address" id="deliveryAddress">
            <input type="hidden" name="pickup_location" id="pickupLocation">
            <div class="space-y-5">
                <section class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-teal-100 sm:p-7">
                    <div class="mb-5 flex items-center justify-between"><div><p class="text-xs font-black uppercase tracking-widest text-teal-600">01</p><h2 class="mt-1 text-lg font-black text-gray-900">Shipping address</h2></div><span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-bold text-teal-700">Required</span></div>
                    <div id="deliveryCard" class="rounded-2xl border border-teal-200 bg-teal-50/50 p-4">
                        <div class="flex items-start gap-3"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-teal-500 text-white">⌖</span><div class="min-w-0 flex-1"><p class="text-sm font-black text-gray-800">Your current location</p><p id="locationStatus" class="mt-1 text-xs leading-5 text-gray-500">Choose delivery below and we will detect your address.</p></div><button type="button" id="detectLocation" class="shrink-0 text-xs font-black text-teal-700 hover:text-teal-900">Detect</button></div>
                    </div>
                    <div class="mt-5"><h3 class="mb-3 text-sm font-black text-gray-800">Delivery method</h3><div class="grid gap-3 sm:grid-cols-2"><label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 p-4 transition has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50"><input id="deliveryRadio" type="radio" name="delivery_method" value="location" required class="text-teal-600"><span><strong class="block text-sm text-gray-800">Door delivery</strong><small class="text-xs text-gray-500">Use my current location</small></span></label><label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 p-4 transition has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50"><input id="pickupRadio" type="radio" name="delivery_method" value="pickup" class="text-teal-600"><span><strong class="block text-sm text-gray-800">Pick up order</strong><small class="text-xs text-gray-500">Find the nearest station</small></span></label></div></div>
                    <div id="pickupBox" class="mt-4 hidden rounded-2xl border border-teal-200 bg-teal-50 p-4"><div class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-full bg-teal-500 text-white">⌖</span><div><p class="text-sm font-black text-gray-800">Nearest pickup station</p><p id="pickupStatus" class="mt-1 text-xs text-gray-500">Finding a station near you...</p></div></div><select id="pickupStation" name="pickup_station" class="mt-4 w-full rounded-xl border-teal-200 bg-white px-3 py-3 text-sm focus:border-teal-500 focus:ring-teal-300"><option value="">Select station</option><option value="ikeja" data-lat="6.6018" data-lng="3.3515">Ikeja Hub</option><option value="lekki" data-lat="6.4698" data-lng="3.5852">Lekki Pickup</option><option value="abuja" data-lat="9.0765" data-lng="7.3986">Abuja Central</option></select></div>
                </section>

                <section class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-teal-100 sm:p-7"><div class="mb-5"><p class="text-xs font-black uppercase tracking-widest text-teal-600">02</p><h2 class="mt-1 text-lg font-black text-gray-900">Payment method</h2></div><div class="space-y-3"><label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 p-4 transition has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50"><input type="radio" name="payment_method" value="paystack" required class="text-teal-600"><span class="grid h-8 w-8 place-items-center rounded-lg bg-red-50 text-xs font-black text-red-500">●</span><span class="flex-1 text-sm font-bold text-gray-700">Paystack</span><span class="text-gray-300">○</span></label><label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 p-4 transition has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50"><input type="radio" name="payment_method" value="flutterwave" class="text-teal-600"><span class="grid h-8 w-8 place-items-center rounded-lg bg-yellow-50 text-xs font-black text-yellow-600">G</span><span class="flex-1 text-sm font-bold text-gray-700">Flutterwave</span><span class="text-gray-300">○</span></label><label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 p-4 transition has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50"><input type="radio" name="payment_method" value="cod" class="text-teal-600"><span class="grid h-8 w-8 place-items-center rounded-lg bg-gray-100 text-xs font-black text-gray-600">₦</span><span class="flex-1 text-sm font-bold text-gray-700">Cash on delivery</span><span class="text-gray-300">○</span></label></div></section>
            </div>

            <aside class="h-fit rounded-3xl bg-white p-5 shadow-sm ring-1 ring-teal-100 sm:p-6 lg:sticky lg:top-24"><h2 class="mb-4 text-lg font-black text-gray-900">Order summary</h2><div class="space-y-3 border-b border-gray-100 pb-4">@forelse($cart as $item)<div class="flex justify-between gap-3 text-sm"><span class="min-w-0 truncate text-gray-600">{{ $item['title'] }} × {{ $item['quantity'] }}</span><span class="shrink-0 font-bold text-gray-800">₦{{ number_format($item['price'] * $item['quantity']) }}</span></div>@empty<p class="text-sm text-gray-500">Your cart is empty.</p>@endforelse</div><div class="mt-4 flex items-center justify-between"><span class="text-sm font-bold text-gray-500">Total</span><strong class="text-xl font-black text-teal-700">₦{{ number_format($grandTotal) }}</strong></div><button type="submit" class="mt-6 w-full rounded-2xl bg-teal-600 py-3.5 text-sm font-black text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50" @disabled(empty($cart))>Confirm payment</button><p class="mt-3 text-center text-[11px] leading-5 text-gray-400">Your payment details are handled securely.</p></aside>
        </form>
    </div>
</div>

<script>
    const deliveryRadio = document.getElementById('deliveryRadio');
    const pickupRadio = document.getElementById('pickupRadio');
    const pickupBox = document.getElementById('pickupBox');
    const pickupStation = document.getElementById('pickupStation');
    const locationStatus = document.getElementById('locationStatus');
    const pickupStatus = document.getElementById('pickupStatus');
    const deliveryAddress = document.getElementById('deliveryAddress');
    const pickupLocation = document.getElementById('pickupLocation');

    const stations = Array.from(pickupStation.options).filter(option => option.dataset.lat);
    const distance = (lat1, lon1, lat2, lon2) => Math.hypot(lat1 - lat2, lon1 - lon2);

    function detectPosition(mode) {
        if (!navigator.geolocation) {
            (mode === 'pickup' ? pickupStatus : locationStatus).textContent = 'Location is not supported. Please choose manually.';
            return;
        }
        (mode === 'pickup' ? pickupStatus : locationStatus).textContent = 'Finding your location...';
        navigator.geolocation.getCurrentPosition(async position => {
            const { latitude, longitude } = position.coords;
            if (mode === 'pickup') {
                const nearest = stations.reduce((best, option) => !best || distance(latitude, longitude, option.dataset.lat, option.dataset.lng) < distance(latitude, longitude, best.dataset.lat, best.dataset.lng) ? option : best, null);
                if (nearest) {
                    pickupStation.value = nearest.value;
                    pickupLocation.value = `${latitude},${longitude}`;
                    pickupStatus.textContent = `${nearest.text} selected automatically.`;
                }
                return;
            }
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`);
                const data = await response.json();
                deliveryAddress.value = data.display_name || `${latitude}, ${longitude}`;
                locationStatus.textContent = deliveryAddress.value;
            } catch (error) {
                deliveryAddress.value = `${latitude}, ${longitude}`;
                locationStatus.textContent = 'Location detected. Address lookup is unavailable.';
            }
        }, () => {
            (mode === 'pickup' ? pickupStatus : locationStatus).textContent = 'Allow location access or choose a station manually.';
        });
    }

    deliveryRadio.addEventListener('change', () => { pickupBox.classList.add('hidden'); pickupLocation.value = ''; detectPosition('delivery'); });
    pickupRadio.addEventListener('change', () => { pickupBox.classList.remove('hidden'); deliveryAddress.value = ''; detectPosition('pickup'); });
    document.getElementById('detectLocation').addEventListener('click', () => detectPosition(deliveryRadio.checked ? 'delivery' : 'pickup'));
</script>
@endsection
