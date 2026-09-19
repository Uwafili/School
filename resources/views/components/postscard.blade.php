@props(['post', 'full' => false])

@php
    $fallbackImages = ['pizza' => 'generated.jpg', 'burger' => 'front.avif', 'salad' => 'brown.jpg', 'drinks' => 'drink.webp'];
    $image = $post->image ? 'storage/' . $post->image : 'asset/' . ($fallbackImages[$post->category] ?? 'front.avif');
@endphp

<article class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 transition duration-300 hover:-translate-y-1 hover:shadow-lg">
    <a href="{{ route('food.view', $post) }}" class="relative block h-48 overflow-hidden bg-yellow-100">
        <img src="{{ asset($image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-black uppercase text-orange-600">{{ ucfirst($post->category ?? 'Meal') }}</span>
    </a>
    <div class="p-4">
        <div class="mb-2 flex items-start justify-between gap-3">
            <h2 class="line-clamp-2 text-base font-black text-gray-900">{{ $post->title }}</h2>
            <span class="shrink-0 text-sm font-black text-yellow-600">₦{{ $post->price }}</span>
        </div>
        <p class="mb-4 line-clamp-2 text-xs leading-5 text-gray-500">{{ $post->description }}</p>
        <div class="mb-4 flex items-center gap-2 border-t border-gray-100 pt-3">
            <span class="grid h-7 w-7 place-items-center rounded-full bg-purple-100 text-xs font-black text-purple-700">{{ strtoupper(substr($post->user->name ?? 'S', 0, 1)) }}</span>
            <span class="min-w-0 truncate text-xs font-bold text-gray-600">{{ $post->user->name ?? 'FoodStore seller' }}</span>
            <span class="ml-auto shrink-0 text-[10px] text-gray-400">{{ $post->created_at->diffForHumans(null, true) }}</span>
        </div>
        <div class="grid grid-cols-[1fr_auto] gap-2">
            <form action="{{ route('add.cart', $post->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-full rounded-xl bg-gray-900 py-2.5 text-xs font-black text-white transition hover:bg-yellow-500 hover:text-gray-900">Add to cart</button>
            </form>
            <a href="{{ route('food.view', $post) }}" class="grid min-w-10 place-items-center rounded-xl bg-yellow-100 px-3 text-xs font-black text-yellow-800 transition hover:bg-yellow-200" aria-label="View {{ $post->title }} details">View</a>
        </div>
    </div>
</article>