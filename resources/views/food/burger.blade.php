@extends('layouts.navbar')
@section('content')

<div class="container mt-10">
    <h1 class="text-center mb-4" style="color: gray;">Latest Posts</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($post as $posts)
            <div class="bg-white rounded-lg shadow p-5 flex flex-col items-center relative">
                
                @if($posts->image)
                    <img src="{{ asset('storage/' . $posts->image) }}" alt="{{ $posts->title }}" class="w-full h-40 object-cover rounded mb-3">
                @else
                    <div class="w-full h-40 bg-gray-200 flex items-center justify-center rounded mb-3 text-gray-400">
                        No Image
                    </div>
                @endif
                <h2 class="text-xl font-bold mb-2">{{ $posts->title }}</h2>
                <p class="text-gray-700 mb-2">{{ $posts->description }}</p>
                <span class="text-yellow-600 font-semibold mb-1">₦{{ $posts->price }}</span>
                <span class="text-sm text-gray-500 mb-2">{{ ucfirst($posts->category) }}</span>
                <span class="text-xs text-gray-400">Posted: {{ $posts->created_at->diffForHumans() }}</span>
            </div>

            
        @empty
            <p class="col-span-3 text-center text-gray-500">No posts found.</p>
        @endforelse
    </div>
</div>

@endsection