<!-- filepath: c:\Users\Bishop\School\resources\views\enroll\Restaurant.blade.php -->
@extends('layouts.navbar')

@section('content')
<div class="min-h-screen flex items-center justify-center relative py-12 px-4">
    <!-- Blurred Background Image --> 
    <div class="absolute inset-0 z-0">
        <img src="{{('asset/one.jpg') }}" alt="Background" class="w-full h-full object-cover blur-sm brightness-75">
    </div>
    <div class="w-full max-w-lg bg-white bg-opacity-90 rounded-lg shadow-lg p-8 relative z-10">
        <h2 class="text-3xl font-bold text-center text-yellow-600 mb-6">Open Your Store</h2>
        <p class="text-center text-gray-600 mb-8">
            Register your restaurant or shop and start selling your delicious food to thousands of customers!
        </p>
        <form method="POST" action="{{ route('store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="store_name" class="block text-gray-700 mb-2">Store Name</label>
                <input id="stores" name="stores" type="text" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    value="{{ old('stores') }}">
                @error('store_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="owner_name" class="block text-gray-700 mb-2">Owner Name</label>
                <input id="owner" name="owner" type="text" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    value="{{ old('owner') }}">
                @error('owner_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 mb-2">Email Address</label>
                <input id="email" name="email" type="email" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    value="{{ old('email') }}">
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-gray-700 mb-2">Phone Number</label>
                <input id="phone" name="phone" type="text" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    value="{{ old('phone') }}">
                @error('phone')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="address" class="block text-gray-700 mb-2">Store Address</label>
                <input id="address" name="address" type="text" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    value="{{ old('address') }}">
                @error('address')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label for="logo" class="block text-gray-700 mb-2">Store Logo (optional)</label>
                <input id="image" name="image" type="file"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                @error('logo')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
               @error('failed')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            <button type="submit"
                class="w-full bg-yellow-500 text-white py-2 rounded-lg font-semibold hover:bg-yellow-600 transition">
                Open Store
            </button>
        </form>
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-yellow-600 hover:underline">Back to Home</a>
        </div>
    </div>
</div>
@endsection