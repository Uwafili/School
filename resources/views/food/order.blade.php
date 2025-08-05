@extends('layouts.navbar')
@section('content')
<div class="container mt-10 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-6 text-center text-yellow-700">Order a Pizza</h1>
    <form action="{{ route('order.submit') }}" method="POST" class="bg-white shadow rounded p-6 mb-6">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Pizza Name</label>
            <input type="text" name="pizza_name" class="w-full border rounded px-3 py-2" placeholder="e.g. Margherita" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Quantity</label>
            <input type="number" name="quantity" class="w-full border rounded px-3 py-2" min="1" value="1" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Delivery Address</label>
            <textarea name="address" class="w-full border rounded px-3 py-2" rows="3" required></textarea>
        </div>
        <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 rounded transition duration-200">
            Place Order
        </button>
    </form>

    <!-- Add to Cart Button -->
    <form action="{{ route('cart.add') }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf
        <input type="hidden" name="pizza_name" value="Margherita"><!-- You can set this dynamically -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Quantity</label>
            <input type="number" name="quantity" class="w-full border rounded px-3 py-2" min="1" value="1" required>
        </div>
        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded transition duration-200">
            Add to Cart
        </button>
    </form>
</div>
@endsection