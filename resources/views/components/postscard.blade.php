@props(['post', 'full'=>false])
<div class="container mx-auto my-5">
  <!-- flex container for cards -->
  <div class="flex flex-wrap justify-center gap-8">

      <div class="w-72">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
            @if ($post->image)
             <img src="{{ asset('storage/' . $post->image) }}" class="w-full h-40 object-cover rounded mb-3">

            @else
              <div class="w-full h-40 bg-gray-200 flex items-center justify-center rounded mb-3 text-gray-400">
                        No Image
                    </div>
            @endif
          <div class="p-4">

            <h5 class="text-lg font-semibold">{{ $post->title }}</h5>

            @if($full)
            <p class="text-gray-600 text-sm mt-1">{{ ($post->body) }}</p>
            @else
            <p class="text-gray-600 text-sm mt-1">{{Str::words($post->body, 15) }}</p>

            @endif

            <p class="text-green-600 font-bold mt-2">{{ $post->price }}</p>
            <span class="text-sm text-gray-500 mb-2">{{ ucfirst($post->category) }}</span>
            <button class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">View more</button>
          </div>
        </div>
      </div>
  
  </div>
</div>