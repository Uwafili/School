@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('admin.dashboard') }}" class="mb-5 inline-flex items-center gap-2 text-sm font-bold text-gray-500 transition hover:text-yellow-600">← Back to products</a>
        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100 sm:p-8">
            <div class="mb-7">
                <p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Product management</p>
                <h1 class="mt-1 text-2xl font-black text-gray-900">Edit food item</h1>
                <p class="mt-2 text-sm text-gray-500">Update the product details shown to FoodStore customers.</p>
            </div>
            <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label for="title" class="mb-2 block text-sm font-bold text-gray-700">Product name</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $post->title) }}" required class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300">
                    @error('title')<p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="description" class="mb-2 block text-sm font-bold text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="4" required class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300">{{ old('description', $post->description) }}</textarea>
                    @error('description')<p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="price" class="mb-2 block text-sm font-bold text-gray-700">Price</label>
                        <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $post->price) }}" required class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300">
                        @error('price')<p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="category" class="mb-2 block text-sm font-bold text-gray-700">Category</label>
                        <select id="category" name="category" required class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300">
                            @foreach(['pizza' => 'Pizza', 'burger' => 'Burger', 'salad' => 'Salad', 'drinks' => 'Drinks'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('category', $post->category) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category')<p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="image" class="mb-2 block text-sm font-bold text-gray-700">Replace image <span class="font-normal text-gray-400">(optional)</span></label>
                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.avif" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-yellow-100 file:px-3 file:py-2 file:font-bold file:text-yellow-700">
                    @error('image')<p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>@enderror
                </div>
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.dashboard') }}" class="rounded-xl px-5 py-3 text-center text-sm font-bold text-gray-500 transition hover:bg-gray-100">Cancel</a>
                    <button type="submit" class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-black text-white transition hover:bg-yellow-500 hover:text-gray-900">Save changes</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
