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

            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <span class="text-2xl font-bold text-yellow-600">Riders page</span>
                <span class="text-gray-700 mt-2"><a href="{{ route('riders') }}">Riders page</a></span>
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
    
    @if (session('success'))
    <div class="mb-4">
        <x-flashMsg msg="{{session('success')}}" />
    </div>
@endif
    
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

<div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($posts as $post)
        <div class="bg-white rounded-lg shadow p-3">
            <x-postscard :post="$post"/>

            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');" class="mt-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-300 bg-red-600 hover:bg-red-700 text-white py-2 rounded">
                    Delete
                </button>
            </form>
        </div>
    @endforeach
</div>

@endsection



