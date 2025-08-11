@extends('layouts.navbar')

@section('content')
<div class="container max-w-xl mx-auto mt-10">
    <div class="bg-white shadow rounded-lg p-6 flex flex-col items-center">
        
        {{-- Product Image --}}
        @if($post->image)
            <img src="{{ asset('storage/' . $post->image) }}" class="w-64 h-64 object-cover rounded mb-4">
        @else
            <div class="w-64 h-64 bg-gray-200 flex items-center justify-center rounded mb-4 text-gray-400">
                No Image Available
            </div>
        @endif

        {{-- Product Info --}}
        <h2 class="text-2xl font-bold mb-2">{{ $post->title }}</h2>
        <p class="text-gray-700 mb-2">{{ $post->description }}</p>
        <span class="text-yellow-600 font-semibold text-lg mb-1">₦{{ number_format($posts->price, 2) }}</span>
        <span class="text-sm text-gray-500 mb-2">{{ ucfirst($post->category) }}</span>
        {{-- <span class="text-xs text-gray-400">Posted: {{ $posts->created_at->diffForHumans() }}</span> --}}

        {{-- Add to Cart Form --}}
        <form action="{{}}" method="POST" class="w-full mt-4">
            @csrf
            <div class="mb-4">
                <label for="quantity" class="block mb-1 font-semibold">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" min="1" value="1" required>
            </div>
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded transition duration-200">
                Add to Cart
            </button>
        </form>
    </div>
</div>
@endsection