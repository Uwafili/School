@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-yellow-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-4xl font-bold text-yellow-600 mb-2 text-center">🏪 Open Your Store</h1>
        <p class="text-center text-gray-600 mb-8">Register your restaurant or manage your store status</p>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-800 border-l-4 border-green-500">
                <strong>✓ Success:</strong> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-800 border-l-4 border-red-500">
                <strong>✗ Error:</strong> {{ session('error') }}
            </div>
        @endif

        @if($store && $store->status === 'approved')
            <!-- Approved Store Info -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-800 border-l-4 border-green-500 text-center">
                    <p class="text-lg font-bold">✅ Your Store Has Been Approved!</p>
                    <p class="text-sm mt-2">You can now manage your store and start taking orders.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Store Logo/Image -->
                    <div class="flex flex-col items-center">
                        @if(!empty($store->image))
                            <img src="{{ asset('storage/' . $store->image) }}" alt="Store Logo" class="w-48 h-48 object-cover rounded-xl shadow-md border-4 border-green-500">
                        @else
                            <div class="w-48 h-48 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center rounded-xl text-green-600 text-4xl font-bold shadow-md">🏪</div>
                        @endif
                        <p class="text-sm text-gray-600 mt-4 font-semibold">Store Verified</p>
                    </div>

                    <!-- Store Information -->
                    <div class="space-y-4">
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg border-l-4 border-yellow-500">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Store Name</label>
                            <p class="text-2xl font-bold text-gray-800">{{ $store->stores }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border-l-4 border-blue-500">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Owner Name</label>
                            <p class="text-xl font-bold text-gray-800">{{ $store->owner }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border-l-4 border-purple-500">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Email</label>
                            <p class="text-gray-800 font-semibold">{{ $store->email }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border-l-4 border-green-500">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Phone</label>
                            <p class="text-gray-800 font-semibold">{{ $store->phone }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border-l-4 border-orange-500">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Address</label>
                            <p class="text-gray-800 font-semibold">{{ $store->address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 mt-10">
                    <a href="{{ route('storedashboard') }}" class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 rounded-lg text-center transition shadow-md">
                        📊 Go to Store Dashboard
                    </a>
                    <a href="{{ route('store.show', $store->id) }}" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 rounded-lg text-center transition shadow-md">
                        👁️ View Store Details
                    </a>
                </div>
            </div>
        @elseif($store && $store->status === 'rejected')
            <!-- Rejected Store Info -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-800 border-l-4 border-red-500 text-center">
                    <p class="text-lg font-bold">❌ Your Store Application Was Rejected</p>
                    <p class="text-sm mt-2">Please review your information and try again.</p>
                </div>

                <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6 mb-8">
                    <h3 class="font-bold text-red-800 mb-4">Store Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-xs font-bold text-gray-600 uppercase">Store Name</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $store->stores }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-600 uppercase">Owner</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $store->owner }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-600 uppercase">Email</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $store->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-600 uppercase">Phone</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $store->phone }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('home') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 rounded-lg text-center transition shadow-md">
                        ← Back to Home
                    </a>
                    <button type="button" onclick="location.reload()" class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                        🔄 Try Again
                    </button>
                </div>
            </div>
        @elseif($store && $store->status === 'pending')
            <!-- Pending Approval -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <div class="mb-6 p-4 rounded-lg bg-yellow-50 text-yellow-800 border-l-4 border-yellow-500 text-center">
                    <p class="text-lg font-bold">⏳ Your Store Application is Pending</p>
                    <p class="text-sm mt-2">Your store information is under review. We'll notify you soon.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Store Image -->
                    <div class="flex flex-col items-center">
                        @if(!empty($store->image))
                            <img src="{{ asset('storage/' . $store->image) }}" alt="Store Logo" class="w-48 h-48 object-cover rounded-xl shadow-md border-4 border-yellow-500">
                        @else
                            <div class="w-48 h-48 bg-gradient-to-br from-yellow-100 to-yellow-200 flex items-center justify-center rounded-xl text-yellow-600 text-4xl font-bold shadow-md">🏪</div>
                        @endif
                        <p class="text-sm text-gray-600 mt-4 font-semibold text-center">Awaiting Review</p>
                    </div>

                    <!-- Store Info -->
                    <div class="space-y-3">
                        <div class="bg-gray-50 p-3 rounded-lg border-l-4 border-gray-400">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Store Name</label>
                            <p class="text-lg font-bold text-gray-800">{{ $store->stores }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg border-l-4 border-gray-400">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Owner</label>
                            <p class="text-gray-800 font-semibold">{{ $store->owner }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg border-l-4 border-gray-400">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Address</label>
                            <p class="text-gray-800 font-semibold">{{ $store->address }}</p>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded-lg border-l-4 border-yellow-500 mt-4">
                            <p class="text-sm text-yellow-700">Applications typically take 24-48 hours to review.</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('home') }}" class="block bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 rounded-lg text-center transition shadow-md">
                    ← Back to Home
                </a>
            </div>
        @else
            <!-- No Store - Show Registration Form -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">📝 Register Your Store</h2>

                @php
                    $isRider = \App\Models\Rider::where('user_id', auth()->id())->exists();
                @endphp

                @if($isRider)
                    <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6 mb-6">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">⛔</span>
                            <div>
                                <h3 class="text-red-800 font-bold text-lg mb-2">Access Restricted</h3>
                                <p class="text-red-700 text-sm mb-3">You cannot create a store because you are registered as a rider.</p>
                                <p class="text-red-700 text-sm mb-3 font-semibold">Each account can only have one role (Rider OR Store Owner)</p>
                                <p class="text-red-700 text-sm mb-4">To open a store, please:</p>
                                <ul class="text-red-700 text-sm list-disc pl-5 mb-4 space-y-1">
                                    <li>Log out from your current account</li>
                                    <li>Create a new account</li>
                                    <li>Register that new account as a store owner</li>
                                </ul>
                                <a href="{{ route('home') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition">← Back to Home</a>
                            </div>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label for="stores" class="block text-gray-700 font-bold mb-2">🏪 Store Name</label>
                            <input id="stores" name="stores" type="text"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                                placeholder="Enter your store name"
                                value="{{ old('stores') }}" required>
                            @error('stores')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="owner" class="block text-gray-700 font-bold mb-2">👤 Owner Name</label>
                            <input id="owner" name="owner" type="text"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                                placeholder="Your full name"
                                value="{{ old('owner') }}" required>
                            @error('owner')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-gray-700 font-bold mb-2">📧 Email Address</label>
                            <input id="email" name="email" type="email"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                                placeholder="your.email@example.com"
                                value="{{ old('email') }}" required>
                            @error('email')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-gray-700 font-bold mb-2">📞 Phone Number</label>
                            <input id="phone" name="phone" type="tel"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                                placeholder="Your phone number"
                                value="{{ old('phone') }}" required>
                            @error('phone')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-gray-700 font-bold mb-2">📍 Store Address</label>
                            <input id="address" name="address" type="text"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                                placeholder="Your store location"
                                value="{{ old('address') }}" required>
                            @error('address')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="image" class="block text-gray-700 font-bold mb-2">🖼️ Store Logo (Optional)</label>
                            <input id="image" name="image" type="file" accept="image/*"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 outline-none transition">
                            @error('image')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-gray-500 mt-2">Max size: 3MB. Formats: JPG, PNG, AVIF</p>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="submit" class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                                ✅ Submit Store Registration
                            </button>
                            <a href="{{ route('home') }}" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-3 rounded-lg text-center transition shadow-md">
                                ← Back
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
