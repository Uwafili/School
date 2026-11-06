<section id="products">
    <div class="mb-4 flex items-end justify-between"><div><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Live catalog</p><h2 class="mt-1 text-2xl font-black text-gray-900">Your food products</h2></div><span class="text-sm font-bold text-gray-400">{{ $posts->total() }} items</span></div>
    <form wire:submit="save" class="mb-5 space-y-4 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-2"><div><h3 class="text-lg font-black text-gray-900">{{ $editingId ? 'Edit food item' : 'Add food item' }}</h3><p class="mt-1 text-sm text-gray-500">Changes update the catalog immediately.</p></div>@if($editingId)<button type="button" wire:click="cancelEdit" class="text-sm font-bold text-slate-500 hover:text-slate-800">Cancel edit</button>@endif</div>
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="text-sm font-bold text-gray-700">Product name<input wire:model="title" maxlength="255" class="mt-1 w-full rounded-xl border-gray-200 px-4 py-3 font-normal">@error('title')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-bold text-gray-700">Price<input wire:model="price" type="number" min="0" step="0.01" class="mt-1 w-full rounded-xl border-gray-200 px-4 py-3 font-normal">@error('price')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-bold text-gray-700 sm:col-span-2">Description<textarea wire:model="description" rows="2" maxlength="255" class="mt-1 w-full rounded-xl border-gray-200 px-4 py-3 font-normal"></textarea>@error('description')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-bold text-gray-700">Category<select wire:model="category" class="mt-1 w-full rounded-xl border-gray-200 px-4 py-3 font-normal"><option value="">Choose one</option><option value="pizza">Pizza</option><option value="burger">Burger</option><option value="salad">Salad</option><option value="drinks">Drinks</option></select>@error('category')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-bold text-gray-700">Food image<input wire:model="image" type="file" accept=".jpg,.jpeg,.png,.avif" class="mt-1 block w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal">@error('image')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror</label>
        </div>
        <button type="submit" wire:loading.attr="disabled" wire:target="save,image" class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-black text-white transition hover:bg-yellow-500 hover:text-gray-900 disabled:opacity-60"><span wire:loading.remove wire:target="save,image">{{ $editingId ? 'Save changes' : 'Publish product' }}</span><span wire:loading wire:target="save,image">Saving…</span></button>
    </form>
    @if($posts->isEmpty())
        <div class="rounded-3xl bg-white p-10 text-center shadow-sm ring-1 ring-gray-100"><p class="text-lg font-black text-gray-800">Your catalog is empty</p><p class="mt-1 text-sm text-gray-500">Publish your first food item using the form.</p></div>
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach($posts as $post)
                @php $fallbackImages = ['pizza' => 'generated.jpg', 'burger' => 'front.avif', 'salad' => 'brown.jpg', 'drinks' => 'drink.webp']; $imagePath = $post->image ? 'storage/' . $post->image : 'asset/' . ($fallbackImages[$post->category] ?? 'front.avif'); @endphp
                <article wire:key="product-{{ $post->id }}" class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="relative h-40 overflow-hidden bg-yellow-100"><img src="{{ asset($imagePath) }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105"><span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-black uppercase text-orange-600">{{ ucfirst($post->category ?? 'Meal') }}</span></div>
                    <div class="p-4"><div class="mb-2 flex items-start justify-between gap-2"><h3 class="line-clamp-2 text-base font-black text-gray-900">{{ $post->title }}</h3><span class="shrink-0 text-sm font-black text-yellow-600">₦{{ $post->price }}</span></div><p class="mb-3 line-clamp-2 text-xs leading-5 text-gray-500">{{ $post->description }}</p><div class="mb-4 flex items-center gap-2 border-t border-gray-100 pt-3"><span class="grid h-7 w-7 place-items-center rounded-full bg-purple-100 text-xs font-black text-purple-700">{{ strtoupper(substr($post->user->name ?? 'A', 0, 1)) }}</span><span class="min-w-0 truncate text-xs font-bold text-gray-600">{{ $post->user->name ?? 'Admin seller' }}</span><span class="ml-auto text-[10px] text-gray-400">{{ $post->created_at->diffForHumans(null, true) }}</span></div>
                        <div class="grid grid-cols-3 gap-2"><livewire:cart-add-button :post-id="$post->id" :key="'cart-add-'.$post->id" /><button type="button" wire:click="edit({{ $post->id }})" class="rounded-xl bg-blue-100 py-2 text-xs font-black text-blue-700 transition hover:bg-blue-200">Edit</button><button type="button" wire:click="delete({{ $post->id }})" wire:confirm="Delete {{ $post->title }}?" class="rounded-xl bg-red-100 py-2 text-xs font-black text-red-700 transition hover:bg-red-200">Delete</button></div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-5">{{ $posts->links() }}</div>
    @endif
</section>
