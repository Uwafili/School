@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-slate-50 px-3 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[.18em] text-orange-500">FoodStore control center</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-gray-900 sm:text-4xl">Admin dashboard</h1>
                <p class="mt-2 text-sm text-gray-500">Manage products, sellers, customers, and delivery operations.</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 self-start rounded-full bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-gray-200 transition hover:text-yellow-600 sm:self-auto">View storefront <span>-&gt;</span></a>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-2xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{{ session('success') }}</div>
        @endif
        @if(session('delete'))
            <div class="mb-5 rounded-2xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ session('delete') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-5 rounded-2xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">Please check the product form below.</div>
        @endif

        <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="#products" class="rounded-2xl bg-yellow-400 p-5 text-gray-900 shadow-sm transition hover:-translate-y-1"><p class="text-xs font-black uppercase tracking-widest text-yellow-950/60">Products</p><p class="mt-2 text-3xl font-black">{{ $posts->count() }}</p><p class="mt-1 text-sm font-semibold text-yellow-950/70">Posted food items</p></a>
            <a href="{{ route('manage.index') }}" class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-1 hover:ring-blue-300"><p class="text-xs font-black uppercase tracking-widest text-gray-400">Customers</p><p class="mt-2 text-3xl font-black text-blue-600">{{ $userCount }}</p><p class="mt-1 text-sm font-semibold text-gray-500">Manage users</p></a>
            <a href="{{ route('storeapprove') }}" class="rounded-2xl bg-orange-500 p-5 text-white shadow-sm transition hover:-translate-y-1"><p class="text-xs font-black uppercase tracking-widest text-orange-100">Stores</p><p class="mt-2 text-3xl font-black">{{ $storeCount }}</p><p class="mt-1 text-sm font-semibold text-orange-100">Manage sellers</p></a>
            <a href="{{ route('riders') }}" class="rounded-2xl bg-purple-600 p-5 text-white shadow-sm transition hover:-translate-y-1"><p class="text-xs font-black uppercase tracking-widest text-purple-100">Delivery riders</p><p class="mt-2 text-3xl font-black">{{ $riderCount }}</p><p class="mt-1 text-sm font-semibold text-purple-100">Manage dispatch team</p></a>
        </div>

        <div class="grid gap-7 xl:grid-cols-[.75fr_1.25fr]">
            <section class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
                <div class="mb-6"><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Catalog</p><h2 class="mt-1 text-xl font-black text-gray-900">Add food item</h2><p class="mt-2 text-sm text-gray-500">Create a product customers can discover and add to cart.</p></div>
                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div><label for="title" class="mb-1.5 block text-sm font-bold text-gray-700">Product name</label><input id="title" name="title" type="text" value="{{ old('title') }}" required class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300">@error('title')<p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>@enderror</div>
                    <div><label for="description" class="mb-1.5 block text-sm font-bold text-gray-700">Description</label><textarea id="description" name="description" rows="3" required class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300">{{ old('description') }}</textarea>@error('description')<p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>@enderror</div>
                    <div class="grid gap-4 sm:grid-cols-2"><div><label for="price" class="mb-1.5 block text-sm font-bold text-gray-700">Price</label><input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price') }}" required class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300">@error('price')<p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>@enderror</div><div><label for="category" class="mb-1.5 block text-sm font-bold text-gray-700">Category</label><select id="category" name="category" required class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300"><option value="" disabled @selected(!old('category'))>Choose one</option><option value="pizza" @selected(old('category') === 'pizza')>Pizza</option><option value="burger" @selected(old('category') === 'burger')>Burger</option><option value="salad" @selected(old('category') === 'salad')>Salad</option><option value="drinks" @selected(old('category') === 'drinks')>Drinks</option></select>@error('category')<p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>@enderror</div></div>
                    <div><label for="image" class="mb-1.5 block text-sm font-bold text-gray-700">Food image</label><input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.avif" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-yellow-100 file:px-3 file:py-2 file:font-bold file:text-yellow-700">@error('image')<p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>@enderror</div>
                    <button type="submit" class="w-full rounded-xl bg-gray-900 py-3 text-sm font-black text-white transition hover:bg-yellow-500 hover:text-gray-900">Publish product</button>
                </form>
            </section>

            <section id="products">
                <div class="mb-4 flex items-end justify-between"><div><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Live catalog</p><h2 class="mt-1 text-2xl font-black text-gray-900">Your food products</h2></div><span class="text-sm font-bold text-gray-400">{{ $posts->count() }} items</span></div>
                @if($posts->isEmpty())
                    <div class="rounded-3xl bg-white p-10 text-center shadow-sm ring-1 ring-gray-100"><p class="text-lg font-black text-gray-800">Your catalog is empty</p><p class="mt-1 text-sm text-gray-500">Publish your first food item using the form.</p></div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($posts as $post)
                            @php $fallbackImages = ['pizza' => 'generated.jpg', 'burger' => 'front.avif', 'salad' => 'brown.jpg', 'drinks' => 'drink.webp']; $image = $post->image ? 'storage/' . $post->image : 'asset/' . ($fallbackImages[$post->category] ?? 'front.avif'); @endphp
                            <article class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-1 hover:shadow-lg"><div class="relative h-40 overflow-hidden bg-yellow-100"><img src="{{ asset($image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105"><span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-black uppercase text-orange-600">{{ ucfirst($post->category ?? 'Meal') }}</span></div><div class="p-4"><div class="mb-2 flex items-start justify-between gap-2"><h3 class="line-clamp-2 text-base font-black text-gray-900">{{ $post->title }}</h3><span class="shrink-0 text-sm font-black text-yellow-600">₦{{ $post->price }}</span></div><p class="mb-3 line-clamp-2 text-xs leading-5 text-gray-500">{{ $post->description }}</p><div class="mb-4 flex items-center gap-2 border-t border-gray-100 pt-3"><span class="grid h-7 w-7 place-items-center rounded-full bg-purple-100 text-xs font-black text-purple-700">{{ strtoupper(substr($post->user->name ?? 'A', 0, 1)) }}</span><span class="min-w-0 truncate text-xs font-bold text-gray-600">{{ $post->user->name ?? 'Admin seller' }}</span><span class="ml-auto text-[10px] text-gray-400">{{ $post->created_at->diffForHumans(null, true) }}</span></div><div class="grid grid-cols-3 gap-2"><form action="{{ route('add.cart', $post->id) }}" method="POST">@csrf<button type="submit" class="w-full rounded-xl bg-gray-900 py-2 text-xs font-black text-white transition hover:bg-yellow-500 hover:text-gray-900">Add cart</button></form><a href="{{ route('posts.edit', $post) }}" class="rounded-xl bg-blue-100 py-2 text-center text-xs font-black text-blue-700 transition hover:bg-blue-200">Edit</a><form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this product?');">@csrf @method('DELETE')<button type="submit" class="w-full rounded-xl bg-red-100 py-2 text-xs font-black text-red-700 transition hover:bg-red-200">Delete</button></form></div></div></article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
