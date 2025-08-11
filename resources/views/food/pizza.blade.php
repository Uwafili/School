@extends('layouts.navbar')
@section('content')

<div class="container mt-10">
    <h1 class="text-center mb-4" style="color: gray;">Latest Posts</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($posts as $post)
            <div class="bg-white rounded-lg shadow p-5 flex flex-col items-center relative">
                @if($posts->image)
                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-40 object-cover rounded mb-3">
                @else
                    <div class="w-full h-40 bg-gray-200 flex items-center justify-center rounded mb-3 text-gray-400">
                        No Image
                    </div>
                @endif
                <h2 class="text-xl font-bold mb-2">{{ $post->title }}</h2>
                <p class="text-gray-700 mb-2">{{ $post->description }}</p>
                <span class="text-yellow-600 font-semibold mb-1">₦{{ $post->price }}</span>
                <span class="text-sm text-gray-500 mb-2">{{ ucfirst($post->category) }}</span>
                <span class="text-xs text-gray-400">Posted: {{ $post->created_at->diffForHumans() }}</span>
                <div class="flex gap-3 mt-3">
                    <!-- Comment Icon -->
                    <a href="#" title="Comment">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 hover:text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8L3 21l1.8-4A8.96 8.96 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </a>
                    <!-- View Details Icon -->
                    <a href="#" title="View Details">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 hover:text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 5-4.03 9-9 9s-9-4-9-9 4.03-9 9-9 9 4 9 9z" />
                        </svg>
                    </a>
                </div>
                <a href="{{ route('food.view') }}">View Details</a>
            </div>
        @empty
            <p class="col-span-3 text-center text-gray-500">No posts found.</p>
        @endforelse
    </div>
     <div>
        {{$post->links()}}
    </div>

</div>

@endsection