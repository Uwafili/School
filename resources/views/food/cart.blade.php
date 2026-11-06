@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-slate-50 px-3 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">FoodStore basket</p><h1 class="mt-1 text-2xl font-black text-gray-900 sm:text-3xl">My Cart</h1></div>
            <a href="{{ route('home') }}" class="rounded-full bg-white px-4 py-2 text-sm font-bold text-gray-600 shadow-sm ring-1 ring-gray-200 transition hover:text-yellow-600">Continue shopping</a>
        </div>
        <livewire:cart-manager />
    </div>
</div>
@endsection
