@props(['post', 'full'=>false])

<div class="bg-white rounded-lg shadow p-5 flex flex-col items-center w-90">
    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-25 object-cover rounded mb-3">
    <h2 class="text-xl font-bold mb-2">{{ $post->title }}</h2>
    <p class="text-gray-700 mb-2">{{ $post->description }}</p>
    <span class="text-yellow-600 font-semibold mb-1">₦{{ $post->price }}</span>
    <span class="text-sm text-gray-500 mb-2">{{ ucfirst($post->category) }}</span>
    <span class="text-xs text-gray-400">Posted: {{ $post->created_at->diffForHumans() }}</span>
<button class="mt-3 w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg">View more</button>

  </div>
           
          
     {{-- <div class="mt-6">
    {{ $post->links() }}
  </div> --}}