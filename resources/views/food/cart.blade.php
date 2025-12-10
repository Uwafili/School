@extends('layouts.navbar')

@section('content')
<div class="max-w-3xl mx-auto mt-10 bg-white p-6 rounded shadow">

    <h1 class="text-2xl font-bold mb-4">Your Cart</h1>

    @if(session('cart') && count(session('cart')) > 0)

        @foreach(session('cart') as $id => $item)
            <div class="flex items-center justify-between border-b py-3">
                
                <div class="flex items-center gap-3">
                    <img src="{{ asset('storage/' . $item['image']) }}" class="w-16 h-16 rounded object-cover">
                    <div>
                        <h2 class="font-bold">{{ $item['title'] }}</h2>
                        <p class="text-gray-700">₦{{ $item['price'] }}</p>
                        <p class="text-sm text-gray-500">Qty: {{ $item['quantity'] }}</p>
                    </div>
                </div>

                <div class="flex gap-2">
 
                    <form action="{{ route('cart.increase', $id) }}" method="POST">
                        @csrf
                        <button class="px-3 py-1 bg-green-600 text-white rounded">+</button>
                    </form>

                    <form action="{{ route('cart.decrease', $id) }}" method="POST">
                        @csrf
                        <button class="px-3 py-1 bg-yellow-600 text-white rounded">-</button>
                    </form>

                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                        @csrf
                        <button class="px-3 py-1 bg-red-600 text-white rounded">Remove</button>
                    </form>

                </div>
            </div>
        @endforeach

    @else
        <p>Your cart is empty.</p>
    @endif

</div>
@endsection
