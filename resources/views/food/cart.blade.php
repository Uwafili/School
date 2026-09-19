@extends('layouts.navbar')

@section('content')
@php
    $cart = session('cart', []);
    $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    $fallbackImages = ['pizza' => 'generated.jpg', 'burger' => 'front.avif', 'salad' => 'brown.jpg', 'drinks' => 'drink.webp'];
@endphp

<div class="min-h-screen bg-slate-50 px-3 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex items-center justify-between gap-4"><div><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">FoodStore basket</p><h1 class="mt-1 text-2xl font-black text-gray-900 sm:text-3xl">My Cart</h1></div><a href="{{ route('home') }}" class="rounded-full bg-white px-4 py-2 text-sm font-bold text-gray-600 shadow-sm ring-1 ring-gray-200 transition hover:text-yellow-600">Continue shopping</a></div>
        @if(session('success'))<div class="mb-5 rounded-2xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{{ session('success') }}</div>@endif
        @if(empty($cart))
            <section class="rounded-3xl bg-white px-6 py-16 text-center shadow-sm ring-1 ring-gray-100"><div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-yellow-100 text-2xl">🛒</div><h2 class="mt-5 text-xl font-black text-gray-900">Your cart is empty</h2><p class="mt-2 text-sm text-gray-500">Add something delicious and it will appear here.</p><a href="{{ route('home') }}" class="mt-6 inline-flex rounded-xl bg-gray-900 px-5 py-3 text-sm font-black text-white transition hover:bg-yellow-500 hover:text-gray-900">Explore food</a></section>
        @else
            <div class="grid gap-6 lg:grid-cols-[1fr_20rem]">
                <section class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7"><div class="mb-5 flex items-center justify-between"><h2 class="text-lg font-black text-gray-900">My Cart List</h2><span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-black text-yellow-800">{{ count($cart) }} items</span></div><div class="divide-y divide-gray-100">
                    @foreach($cart as $id => $item)
                        @php $itemTotal = $item['price'] * $item['quantity']; $image = !empty($item['image']) ? 'storage/' . $item['image'] : 'asset/' . ($fallbackImages[$item['category'] ?? ''] ?? 'front.avif'); @endphp
                        <article class="flex gap-3 py-4 first:pt-0 sm:gap-4"><img src="{{ asset($image) }}" alt="{{ $item['title'] }}" class="h-20 w-20 shrink-0 rounded-2xl object-cover sm:h-24 sm:w-24"><div class="min-w-0 flex-1"><div class="flex items-start justify-between gap-3"><div><h3 class="truncate text-sm font-black text-gray-900 sm:text-base">{{ $item['title'] }}</h3><p class="mt-1 text-xs text-gray-500">₦{{ number_format($item['price']) }} each</p></div><strong class="shrink-0 text-sm font-black text-gray-900">₦{{ number_format($itemTotal) }}</strong></div><div class="mt-4 flex items-center justify-between"><div class="flex items-center gap-2"><form action="{{ route('cart.decrease', $id) }}" method="POST">@csrf<button type="submit" class="grid h-7 w-7 place-items-center rounded-lg bg-gray-100 text-sm font-black text-gray-700 transition hover:bg-yellow-100">−</button></form><span class="min-w-5 text-center text-xs font-black">{{ $item['quantity'] }}</span><form action="{{ route('cart.increase', $id) }}" method="POST">@csrf<button type="submit" class="grid h-7 w-7 place-items-center rounded-lg bg-gray-900 text-sm font-black text-white transition hover:bg-yellow-500 hover:text-gray-900">+</button></form></div><form action="{{ route('cart.remove', $id) }}" method="POST">@csrf<button type="submit" class="text-xs font-bold text-red-500 transition hover:text-red-700">Remove</button></form></div></div></article>
                    @endforeach
                </div></section>
                <aside class="h-fit rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-6 lg:sticky lg:top-24"><h2 class="mb-5 text-lg font-black text-gray-900">Order total</h2><div class="space-y-3 border-b border-gray-100 pb-4"><div class="flex justify-between text-sm text-gray-500"><span>Subtotal</span><span>₦{{ number_format($subtotal) }}</span></div><div class="flex justify-between text-sm text-gray-500"><span>Delivery</span><span class="font-bold text-teal-600">Calculated next</span></div></div><div class="mt-4 flex justify-between"><span class="text-sm font-bold text-gray-500">Total</span><strong class="text-xl font-black text-gray-900">₦{{ number_format($subtotal) }}</strong></div><a href="{{ route('payment.checkout') }}" class="mt-6 block w-full rounded-2xl bg-gray-900 py-3.5 text-center text-sm font-black text-white transition hover:bg-yellow-500 hover:text-gray-900">Checkout</a><p class="mt-3 text-center text-[11px] leading-5 text-gray-400">You can choose delivery or pickup on the next step.</p></aside>
            </div>
        @endif
    </div>
</div>
@endsection
