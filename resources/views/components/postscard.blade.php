@props(['post', 'full'=>false])

<div class="bg-white rounded-lg shadow p-5 flex flex-col items-center w-90">

    <img src="{{ asset('storage/' . $post->image) }}" 
         alt="{{ $post->title }}" 
         class="w-full h-25 object-cover rounded mb-3">

    <h2 class="text-xl font-bold mb-2">{{ $post->title }}</h2>

    <p class="text-gray-700 mb-2">{{ $post->description }}</p>

    <span class="text-yellow-600 font-semibold mb-1">
        ₦{{ $post->price }}
    </span>

    <span class="text-sm text-gray-500 mb-2">
        {{ ucfirst($post->category) }}
    </span>

    <span class="text-xs text-gray-400 mb-3">
        Posted: {{ $post->created_at->diffForHumans() }}
    </span>

<form action="{{ route('add.cart', $post->id) }}" method="POST" class="w-full">
    @csrf
    <button class="mt-3 w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg">
        Add to Cart
    </button>
</form>

    <!-- FLEX BUTTONS START -->
    <div class="flex gap-2 w-full justify-between">

        <form action="" method="POST">
            @csrf
            <input type="hidden" name="id" value="">
            <button class="w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg">
                +
            </button>
        </form>

        <form action="" method="POST">
            @csrf
            <input type="hidden" name="id" value="">
            <button class="w-full px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg">
                -
            </button>
        </form>

 @if (session('success'))
    <div class="mb-6">
        <x-flashMsg msg="{{session('success')}}" />
    </div>
@endif
    </div>
    <!-- FLEX BUTTONS END -->

</div>

           
          
     {{-- <div class="mt-6">
    {{ $post->links() }}
  </div> --}}