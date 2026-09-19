@extends('layouts.navbar')

@section('content')
@php
    $fallbackImages = ['pizza' => 'generated.jpg', 'burger' => 'front.avif', 'salad' => 'brown.jpg', 'drinks' => 'drink.webp'];
    $image = $post->image ? 'storage/' . $post->image : 'asset/' . ($fallbackImages[$post->category] ?? 'front.avif');
@endphp

<div class="min-h-screen bg-slate-50 px-3 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <a href="{{ url()->previous() }}" class="mb-5 inline-flex items-center gap-2 text-sm font-bold text-gray-500 transition hover:text-yellow-600">← Back to food</a>
        <article class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-gray-100">
            <div class="grid lg:grid-cols-2">
                <div class="relative min-h-[19rem] overflow-hidden bg-yellow-100 sm:min-h-[26rem]">
                    <img src="{{ asset($image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                    <span class="absolute left-5 top-5 rounded-full bg-white/90 px-3 py-1.5 text-xs font-black uppercase text-orange-600">{{ ucfirst($post->category ?? 'Meal') }}</span>
                </div>
                <div class="flex flex-col p-5 sm:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">FoodStore special</p><h1 class="mt-2 text-3xl font-black tracking-tight text-gray-900">{{ $post->title }}</h1></div>
                        <span class="shrink-0 text-xl font-black text-yellow-600">₦{{ $post->price }}</span>
                    </div>
                    <div class="mt-5 flex items-center gap-3 border-b border-gray-100 pb-5"><span class="grid h-10 w-10 place-items-center rounded-full bg-purple-100 font-black text-purple-700">{{ strtoupper(substr($post->user->name ?? 'S', 0, 1)) }}</span><div><p class="text-xs text-gray-400">Posted by</p><p class="text-sm font-black text-gray-800">{{ $post->user->name ?? 'FoodStore seller' }}</p></div><span class="ml-auto text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span></div>
                    <div class="my-5 grid grid-cols-3 gap-2 text-center"><div class="rounded-xl bg-yellow-50 p-3"><p class="text-sm font-black text-yellow-700">4.8</p><p class="text-[10px] font-bold text-gray-400">Rating</p></div><div class="rounded-xl bg-orange-50 p-3"><p class="text-sm font-black text-orange-700">Fresh</p><p class="text-[10px] font-bold text-gray-400">Quality</p></div><div class="rounded-xl bg-purple-50 p-3"><p class="text-sm font-black text-purple-700">Local</p><p class="text-[10px] font-bold text-gray-400">Seller</p></div></div>
                    <div class="flex-1"><h2 class="mb-2 text-sm font-black text-gray-900">Description</h2><p class="text-sm leading-7 text-gray-600">{{ $post->description }}</p></div>
                    <form action="{{ route('add.cart', $post->id) }}" method="POST" class="mt-7">@csrf<button type="submit" class="w-full rounded-2xl bg-gray-900 py-3.5 text-sm font-black text-white transition hover:bg-yellow-500 hover:text-gray-900">Add to cart · ₦{{ $post->price }}</button></form>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
