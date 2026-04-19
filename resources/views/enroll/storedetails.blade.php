@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-yellow-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-yellow-600">🏪 Store Details</h1>
            <a href="{{ route('storedashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition shadow-md">
                ← Back to Dashboard
            </a>
        </div>

        <!-- Success/Error Messages -->
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

        <!-- Store Header Card -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <div class="flex flex-col md:flex-row gap-10 items-start">
                <!-- Store Logo -->
                <div class="flex flex-col items-center">
                    @if(!empty($store->image))
                        <img src="{{ asset('storage/' . $store->image) }}" alt="Store Logo" class="w-64 h-64 object-cover rounded-xl shadow-md border-4 border-yellow-500">
                    @else
                        <div class="w-64 h-64 bg-gradient-to-br from-yellow-100 to-yellow-200 flex items-center justify-center rounded-xl text-yellow-600 text-6xl font-bold shadow-md">🏪</div>
                    @endif
                    <div class="mt-4 text-center">
                        <span class="inline-block px-4 py-2 bg-green-100 text-green-700 rounded-full font-bold text-sm">✅ {{ ucfirst($store->status) }}</span>
                    </div>
                </div>

                <!-- Store Information Grid -->
                <div class="flex-1">
                    <!-- Store Name - Large -->
                    <div class="mb-6 bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-lg border-l-4 border-yellow-500">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Store Name</label>
                        <p class="text-4xl font-bold text-gray-800">{{ $store->stores }}</p>
                    </div>

                    <!-- Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-lg border-l-4 border-blue-500">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Owner Name</label>
                            <p class="text-2xl font-bold text-gray-800">{{ $store->owner }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-lg border-l-4 border-purple-500">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Email Address</label>
                            <p class="text-lg font-semibold text-gray-800 break-all">{{ $store->email }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-lg border-l-4 border-green-500">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Phone Number</label>
                            <p class="text-lg font-semibold text-gray-800">{{ $store->phone }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-red-50 to-red-100 p-5 rounded-lg border-l-4 border-red-500">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Registration Date</label>
                            <p class="text-lg font-semibold text-gray-800">{{ $store->created_at->format('M d, Y') }}</p>
                        </div>

                        <div class="md:col-span-2 bg-gradient-to-br from-orange-50 to-orange-100 p-5 rounded-lg border-l-4 border-orange-500">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Store Address</label>
                            <p class="text-lg font-semibold text-gray-800">{{ $store->address }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-t-4 border-yellow-500">
                <p class="text-gray-600 text-sm font-semibold mb-2">📦 TOTAL ORDERS</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $store->orders_count ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-2">All time</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-t-4 border-green-500">
                <p class="text-gray-600 text-sm font-semibold mb-2">💰 TOTAL REVENUE</p>
                <p class="text-3xl font-bold text-green-600">₦{{ number_format($store->total_revenue ?? 0, 0) }}</p>
                <p class="text-xs text-gray-500 mt-2">Cumulative</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-t-4 border-blue-500">
                <p class="text-gray-600 text-sm font-semibold mb-2">⭐ STORE RATING</p>
                <p class="text-3xl font-bold text-blue-600">{{ $store->rating ?? '4.8' }}/5</p>
                <p class="text-xs text-gray-500 mt-2">From customers</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-t-4 border-purple-500">
                <p class="text-gray-600 text-sm font-semibold mb-2">🚴 ACTIVE RIDERS</p>
                <p class="text-3xl font-bold text-purple-600">{{ $store->active_riders ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-2">Available</p>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Store Status -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span>📋</span> Store Status
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="font-semibold text-gray-700">Status</span>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-bold text-sm">✅ {{ ucfirst($store->status) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="font-semibold text-gray-700">Since</span>
                        <span class="text-gray-800">{{ $store->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="font-semibold text-gray-700">ID</span>
                        <span class="text-gray-800 font-mono text-sm">#{{ $store->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span>📞</span> Contact Information
                </h2>
                <div class="space-y-3">
                    <div class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                        <p class="text-xs font-bold text-gray-600 uppercase mb-1">Email</p>
                        <p class="text-gray-800 font-semibold break-all">{{ $store->email }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg border-l-4 border-green-500">
                        <p class="text-xs font-bold text-gray-600 uppercase mb-1">Phone</p>
                        <p class="text-gray-800 font-semibold">{{ $store->phone }}</p>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-lg border-l-4 border-orange-500">
                        <p class="text-xs font-bold text-gray-600 uppercase mb-1">Address</p>
                        <p class="text-gray-800 font-semibold">{{ $store->address }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl shadow-lg p-6 text-center">
            <h2 class="text-xl font-bold text-gray-800 mb-4">⚙️ Store Management</h2>
            <div class="flex flex-col md:flex-row gap-4">
                <a href="{{ route('storedashboard') }}" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                    📊 Go to Dashboard
                </a>
                <a href="{{ route('home') }}" class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                    🏠 Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
