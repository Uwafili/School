@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-slate-50 px-3 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[.18em] text-orange-500">FoodStore control center</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-gray-900 sm:text-4xl">Admin dashboard</h1>
                <p class="mt-2 text-sm text-gray-500">Manage products, sellers, customers, and delivery operations.</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 self-start rounded-full bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-gray-200 transition hover:text-yellow-600 sm:self-auto">View storefront <span>-&gt;</span></a>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-2xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{{ session('success') }}</div>
        @endif
        @if(session('delete'))
            <div class="mb-5 rounded-2xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ session('delete') }}</div>
        @endif
        <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="#products" class="rounded-2xl bg-yellow-400 p-5 text-gray-900 shadow-sm transition hover:-translate-y-1"><p class="text-xs font-black uppercase tracking-widest text-yellow-950/60">Products</p><p class="mt-2 text-3xl font-black">{{ $posts }}</p><p class="mt-1 text-sm font-semibold text-yellow-950/70">Posted food items</p></a>
            <a href="{{ route('manage.index') }}" class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-1 hover:ring-blue-300"><p class="text-xs font-black uppercase tracking-widest text-gray-400">Customers</p><p class="mt-2 text-3xl font-black text-blue-600">{{ $userCount }}</p><p class="mt-1 text-sm font-semibold text-gray-500">Manage users</p></a>
            <a href="{{ route('storeapprove') }}" class="rounded-2xl bg-orange-500 p-5 text-white shadow-sm transition hover:-translate-y-1"><p class="text-xs font-black uppercase tracking-widest text-orange-100">Stores</p><p class="mt-2 text-3xl font-black">{{ $storeCount }}</p><p class="mt-1 text-sm font-semibold text-orange-100">Manage sellers</p></a>
            <a href="{{ route('riders') }}" class="rounded-2xl bg-purple-600 p-5 text-white shadow-sm transition hover:-translate-y-1"><p class="text-xs font-black uppercase tracking-widest text-purple-100">Delivery riders</p><p class="mt-2 text-3xl font-black">{{ $riderCount }}</p><p class="mt-1 text-sm font-semibold text-purple-100">Manage dispatch team</p></a>
        </div>

        <livewire:product-catalog />
    </div>
</div>
@endsection
