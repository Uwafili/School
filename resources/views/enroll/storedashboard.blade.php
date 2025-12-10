<!-- filepath: c:\Users\Bishop\School\resources\views\store\dashboard.blade.php -->
@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-yellow-50 py-10 px-4">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-4xl font-bold text-yellow-600 mb-8 text-center">Store Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            <!-- Example Stat Cards -->
            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <span class="text-2xl font-bold text-yellow-600">120</span>
                <span class="text-gray-700 mt-2">Total Orders</span>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <span class="text-2xl font-bold text-yellow-600">₦250,000</span>
                <span class="text-gray-700 mt-2">Total Revenue</span>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <span class="text-2xl font-bold text-yellow-600">4.8/5</span>
                <span class="text-gray-700 mt-2">Store Rating</span>
            </div>
        </div>

        <!-- Store Info -->
        <div class="bg-white rounded-lg shadow p-8 mb-10">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Store Information</h2>
        
               @foreach ($stores as $store) 
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
                    <div>
                    <p class="mb-2"><span class="font-semibold text-gray-700">Store Name:</span> {{ $store->stores }}</p>
                    <p class="mb-2"><span class="font-semibold text-gray-700">Owner:</span> {{ $store->owner}}</p>
                    <p class="mb-2"><span class="font-semibold text-gray-700">Email:</span> {{ $store->email }}</p>
                    <p class="mb-2"><span class="font-semibold text-gray-700">Phone:</span> {{ $store->phone }}</p>
                    <p class="mb-2"><span class="font-semibold text-gray-700">Address:</span> {{ $store->address }}</p>
                </div>
                
              
                <div class="flex items-center justify-center">
                    @if(!empty($store->image))
                        <img src="{{asset('post_image/' . $store->image) }}" alt="Store Logo" class="w-32 h-32 object-cover rounded-full shadow">
                    @else
                        <div class="w-32 h-32 bg-yellow-100 flex items-center justify-center rounded-full text-yellow-600 text-2xl font-bold">No Logo</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
            
        <!-- Orders Table Example -->
        <div class="bg-white rounded-lg shadow p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Recent Orders</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Order ID</th>
                            <th class="py-2 px-4 border-b">Customer</th>
                            <th class="py-2 px-4 border-b">Amount</th>
                            <th class="py-2 px-4 border-b">Status</th>
                            <th class="py-2 px-4 border-b">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Example rows, replace with dynamic data -->
                        <tr>
                            <td class="py-2 px-4 border-b">#1001</td>
                            <td class="py-2 px-4 border-b">John Doe</td>
                            <td class="py-2 px-4 border-b">₦5,000</td>
                            <td class="py-2 px-4 border-b"><span class="bg-green-100 text-green-700 px-2 py-1 rounded">Delivered</span></td>
                            <td class="py-2 px-4 border-b">2025-06-18</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b">#1002</td>
                            <td class="py-2 px-4 border-b">Jane Smith</td>
                            <td class="py-2 px-4 border-b">₦3,500</td>
                            <td class="py-2 px-4 border-b"><span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded">Pending</span></td>
                            <td class="py-2 px-4 border-b">2025-06-17</td>
                        </tr>
                        <!-- ... -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
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
@endsection