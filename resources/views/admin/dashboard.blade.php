<!-- filepath: c:\Users\Bishop\School\resources\views\admin\dashboard.blade.php -->
@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-gray-100 py-10 px-4">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-yellow-600 mb-8 text-center">Admin Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <span class="text-2xl font-bold text-yellow-600"> Total Users {{ $userCount }}</span>
                <span class="text-gray-700 mt-2"><a href="{{route('manage')}}">Manage users</a></span>
              
   
                
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <span class="text-2xl font-bold text-yellow-600">Stores</span>
                <span class="text-gray-700 mt-2">Manage stores</span>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <span class="text-2xl font-bold text-yellow-600">Orders</span>
                <span class="text-gray-700 mt-2">Manage orders</span>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="flex flex-wrap gap-4">
                <a href="#" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-6 py-3 rounded-lg font-semibold transition">Add User</a>
                <a href="#" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-6 py-3 rounded-lg font-semibold transition">Add Store</a>
                <a href="#" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-6 py-3 rounded-lg font-semibold transition">View Reports</a>
            </div>
        </div>
    </div>

    <!-- filepath: c:\Users\Bishop\School\resources\views\admin\dashboard.blade.php -->
<div class="bg-white rounded-lg shadow p-8 mt-10">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Create New Post Item</h2>
   <!-- filepath: c:\Users\Bishop\School\resources\views\admin\dashboard.blade.php -->
<form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-4">
        <label for="title" class="block text-gray-700 mb-2">Title</label>
        <input id="title" name="title" type="text"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
            value="{{ old('title') }}">
        @error('title')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-4">
        <label for="description" class="block text-gray-700 mb-2">Description</label>
        <textarea id="description" name="description" rows="3"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">{{ old('description') }}</textarea>
        @error('description')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-4">
        <label for="price" class="block text-gray-700 mb-2">Price</label>
        <input id="price" name="price" type="text"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
            value="{{ old('price') }}">
        @error('price')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-4">
        <label for="image" class="block text-gray-700 mb-2">Image</label>
        <input id="image" name="image" type="file"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
        @error('image')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-4">
        <label for="category" class="block text-gray-700 font-semibold mb-2">Select Food Category</label>
        <select id="category" name="category"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="" disabled selected>Select a category</option>
            <option value="pizza">Pizza</option>
            <option value="burger">Burger</option>
            <option value="salad">Salad</option>
            <option value="drinks">Drinks</option>
           
        </select>
        @error('category')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
    <button type="submit"
        class="bg-yellow-500 text-white py-2 px-6 rounded-lg font-semibold hover:bg-yellow-600 transition">
        Create Post
    </button>
</form>
</div>
   
</div>
<!-- filepath: c:\Users\Bishop\School\resources\views\admin\dashboard.blade.php -->

</div>
<!-- Add this after your post creation form in admin/dashboard.blade.php -->

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
           
           <form action="{{ route('posts.destroy', $posts->id) }}" method="POST" class="absolute top-2 right-2" onsubmit="return confirm('Are you sure you want to delete this post?');">
                    @csrf
                    @method('DELETE')
                  <button type="submit" class="p-2 rounded-full hover:bg-red-100 transition" title="Delete">
        <!-- Heroicons Trash SVG -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 3h4a1 1 0 011 1v2H9V4a1 1 0 011-1z" />
        </svg>
    </button>
                </form>
            </div>

            
        @empty
            <p class="col-span-3 text-center text-gray-500">No posts found.</p>
        @endforelse
    </div>
</div>
@endsection