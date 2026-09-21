<!-- filepath: c:\Users\HP\Documents\GitHub\School\resources\views\enroll\storedashboard.blade.php -->
@extends('layouts.navbar')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <div class="min-h-screen bg-slate-50 py-6 px-3 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-2">
                <div><p class="text-xs font-black uppercase tracking-[.18em] text-orange-500">FoodStore partner center</p><h1 class="mt-1 text-3xl font-black text-gray-900">Store dashboard</h1></div>
                <a href="{{ route('store.info') }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition shadow-md">
                    📋 View Store Info
                </a>
            </div>
            <p class="text-left text-gray-500 mb-8">Manage your store, orders, and delivery handoffs from one connected workspace.</p>
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

            @forelse ($stores as $store)
                <!-- Store Owner Profile Card -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <!-- Store Logo -->
                        <div class="flex flex-col items-center">
                            @if(!empty($store->image))
                                <img src="{{asset('storage/' . $store->image) }}" alt="Store Logo"
                                    class="w-48 h-48 object-cover rounded-xl shadow-md border-4 border-yellow-500">
                            @else
                                <div
                                    class="w-48 h-48 bg-gradient-to-br from-yellow-100 to-yellow-200 flex items-center justify-center rounded-xl text-yellow-600 text-4xl font-bold shadow-md">
                                    🏪</div>
                            @endif
                            <p class="text-sm text-gray-600 mt-4 font-semibold">Store Owner</p>
                        </div>

                        <!-- Store Information Grid -->
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-5 rounded-lg border-l-4 border-yellow-500">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Store
                                    Name</label>
                                <p class="text-2xl font-bold text-gray-800">{{ $store->stores }}</p>
                            </div>

                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-lg border-l-4 border-blue-500">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Owner
                                    Name</label>
                                <p class="text-2xl font-bold text-gray-800">{{ $store->owner }}</p>
                            </div>

                            <div
                                class="bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-lg border-l-4 border-purple-500">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Email
                                    Address</label>
                                <p class="text-gray-800 font-semibold">{{ $store->email }}</p>
                            </div>

                            <div
                                class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-lg border-l-4 border-green-500">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Phone
                                    Number</label>
                                <p class="text-gray-800 font-semibold">{{ $store->phone }}</p>
                            </div>

                            <div
                                class="bg-gradient-to-br from-orange-50 to-orange-100 p-5 rounded-lg border-l-4 border-orange-500 md:col-span-2">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Store
                                    Address</label>
                                <p class="text-gray-800 font-semibold">{{ $store->address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-10">
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-t-4 border-yellow-500">
                        <p class="text-gray-600 text-sm font-semibold mb-2">📦 TOTAL ORDERS</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $totalOrders }}</p>
                        <p class="text-xs text-gray-500 mt-2">+{{ $weekOrders }} this week</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-t-4 border-green-500">
                        <p class="text-gray-600 text-sm font-semibold mb-2">💰 TOTAL REVENUE</p>
                        <p class="text-3xl font-bold text-green-600">₦{{ number_format($totalRevenue, 0) }}</p>
                        <p class="text-xs text-gray-500 mt-2">+₦{{ number_format($weekRevenue, 0) }} this week</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-t-4 border-blue-500">
                        <p class="text-gray-600 text-sm font-semibold mb-2">⭐ STORE RATING</p>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($averageRating, 1) }}/5</p>
                        <p class="mt-1 text-lg tracking-wide text-yellow-500">{{ str_repeat('★', (int) round($averageRating)) }}{{ str_repeat('☆', 5 - (int) round($averageRating)) }}</p>
                        <p class="text-xs text-gray-500 mt-2">Based on {{ $ratingCount }} customer ratings</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition border-t-4 border-purple-500 ring-1 ring-gray-100">
                        <p class="text-gray-600 text-sm font-semibold mb-2">🚴 ACTIVE RIDERS</p>
                        <p class="text-3xl font-bold text-purple-600">{{ count($riders) }}</p>
                        <p class="text-xs text-gray-500 mt-2">Online and ready now</p>
                    </div>
                </div>

                <!-- Manage Orders Section -->
                <div class="bg-white rounded-3xl shadow-sm ring-1 ring-gray-100 p-6 mb-10">
                    <div class="mb-4"><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Delivery network</p><h2 class="mt-1 text-xl font-black text-gray-900">Riders near your store</h2><p class="mt-1 text-sm text-gray-500">Online riders with shared locations can be assigned from the order form below.</p></div>
                    <div id="storeRiderMap" class="h-64 overflow-hidden rounded-2xl bg-yellow-50"></div>
                </div>

                <!-- Manage Orders Section -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
                    <h2 class="text-2xl font-bold text-gray-800 mb-8 flex items-center gap-3">
                        <span class="text-3xl">📋</span> Order Management
                    </h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Create New Order -->
                    <div
                        class="border-2 border-dashed border-yellow-400 rounded-xl p-7 bg-yellow-50 hover:bg-yellow-100 transition">
                        <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                            <span>➕</span> Create New Order
                        </h3>
                        <form action="{{ route('order.create') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="text" name="customer_name" placeholder="Customer Name"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                                required>
                            <input type="tel" name="customer_phone" placeholder="Customer Phone"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                                required>
                            <input type="text" name="customer_address" placeholder="Delivery Address"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                                required>
                            <input type="number" name="total_price" placeholder="Order Total (₦)"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                                required>
                            <textarea name="items_description" placeholder="Order Items Description" rows="3"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition resize-none"
                                required></textarea>
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                                ➕ Create Order
                            </button>
                        </form>
                    </div>

                    <!-- Assign Rider to Order -->
                    <div
                        class="border-2 border-dashed border-blue-400 rounded-xl p-7 bg-blue-50 hover:bg-blue-100 transition">
                        <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                            <span>🎯</span> Assign Order to Rider
                        </h3>
                        <form action="{{ route('order.assign') }}" method="POST" class="space-y-4">
                            @csrf
                            <select name="order_id"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-300 outline-none transition"
                                required>
                                <option disabled selected class="text-gray-500">📦 Select a Pending Order</option>
                                @forelse ($orders as $order)
                                    <option value="{{ $order->id }}">
                                        #{{ $order->id }} - {{ $order->customer_name }} ({{ $order->customer_address }}) - ₦{{ $order->total_price }}
                                    </option>
                                @empty
                                    <option disabled>No pending orders available</option>
                                @endforelse
                            </select>

                            <select name="rider_id"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-300 outline-none transition"
                                required>
                                <option disabled selected class="text-gray-500">🚴 Select an Approved Rider</option>
                                @forelse ($riders as $rider)
                                    <option value="{{ $rider->id }}">
                                        👤 {{ $rider->user->name }} - {{ $rider->phone }}
                                    </option>
                                @empty
                                    <option disabled>No approved riders available</option>
                                @endforelse
                            </select>

                            <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                                🎯 Assign to Rider
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Recent Orders Table -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                        <span class="text-3xl">📦</span> Recent Orders & Assignments
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-yellow-50 to-orange-50 border-b-2 border-yellow-300">
                                <th class="py-4 px-5 text-left font-bold text-gray-700 text-sm uppercase">Order ID</th>
                                <th class="py-4 px-5 text-left font-bold text-gray-700 text-sm uppercase">Customer</th>
                                <th class="py-4 px-5 text-left font-bold text-gray-700 text-sm uppercase">Amount</th>
                                <th class="py-4 px-5 text-left font-bold text-gray-700 text-sm uppercase">Assigned Rider
                                </th>
                                <th class="py-4 px-5 text-left font-bold text-gray-700 text-sm uppercase">Status</th>
                                <th class="py-4 px-5 text-left font-bold text-gray-700 text-sm uppercase">Date</th>
                                <th class="py-4 px-5 text-left font-bold text-gray-700 text-sm uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($recentOrders as $order)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-5 font-semibold text-gray-800">#{{ $order->id }}</td>
                                    <td class="py-4 px-5 text-gray-700">{{ $order->customer_name }}</td>
                                    <td class="py-4 px-5 font-bold text-gray-800">₦{{ number_format($order->total_price, 0) }}</td>
                                    <td class="py-4 px-5 text-gray-700">
                                        @if ($order->rider)
                                            👤 {{ $order->rider->user->name }}
                                        @else
                                            <span class="text-gray-500">Not assigned</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5">
                                        @if ($order->status === 'pending')
                                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold">⏳ Pending</span>
                                        @elseif ($order->status === 'assigned')
                                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">📍 Assigned</span>
                                        @elseif ($order->status === 'accepted')
                                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">🚚 In Transit</span>
                                        @elseif ($order->status === 'completed')
                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">✓ Delivered</span>
                                        @elseif ($order->status === 'rejected')
                                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">❌ Rejected</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 text-gray-600">{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td class="py-4 px-5">
                                        <div class="flex gap-2">
                                            <a href="{{ route('order.view', $order->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm px-2 py-1 bg-blue-50 rounded hover:bg-blue-100 transition">
                                                👁️ View
                                            </a>
                                            @if ($order->status === 'pending')
                                                <button onclick="openAssignModal({{ $order->id }})" class="text-yellow-600 hover:text-yellow-800 font-semibold text-sm px-2 py-1 bg-yellow-50 rounded hover:bg-yellow-100 transition">
                                                    🎯 Assign
                                                </button>
                                            @elseif ($order->status === 'accepted' || $order->status === 'assigned')
                                                <form method="POST" action="{{ route('order.complete', $order->id) }}" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-800 font-semibold text-sm px-2 py-1 bg-green-50 rounded hover:bg-green-100 transition" onclick="return confirm('Mark this order as completed?')">
                                                        ✅ Complete
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($order->status !== 'completed' && $order->status !== 'cancelled')
                                                <form method="POST" action="{{ route('order.cancel', $order->id) }}" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm px-2 py-1 bg-red-50 rounded hover:bg-red-100 transition" onclick="return confirm('Cancel this order?')">
                                                        ✕ Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 px-5 text-center text-gray-500">
                                        📭 No orders found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                </div>

                <!-- Rider Response Management -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                        <span class="text-3xl">🚴</span> Rider Responses
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Accepted Orders -->
                    <div class="border-l-4 border-green-500 bg-green-50 p-6 rounded-lg">
                        <h3 class="font-bold text-green-700 mb-4 flex items-center gap-2">
                            <span>✅</span> Accepted Orders ({{ $riderResponses->where('status', 'accepted')->count() }})
                        </h3>
                        <div class="space-y-3">
                            @forelse ($riderResponses->where('status', 'accepted') as $order)
                                <div class="bg-white p-3 rounded border-l-2 border-green-400">
                                    <p class="font-semibold text-gray-800">#{{ $order->id }} - {{ $order->customer_name }}</p>
                                    <p class="text-sm text-gray-600">
                                        @if ($order->rider)
                                            👤 {{ $order->rider->user->name }} accepted · In Transit
                                        @else
                                            Not assigned
                                        @endif
                                    </p>
                                </div>
                            @empty
                                <p class="text-gray-600 text-sm">No accepted orders yet</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pending/Assigned Orders -->
                    <div class="border-l-4 border-orange-500 bg-orange-50 p-6 rounded-lg">
                        <h3 class="font-bold text-orange-700 mb-4 flex items-center gap-2">
                            <span>⏳</span> Pending Responses ({{ $riderResponses->where('status', 'assigned')->count() }})
                        </h3>
                        <div class="space-y-3">
                            @forelse ($riderResponses->where('status', 'assigned') as $order)
                                <div class="bg-white p-3 rounded border-l-2 border-orange-400">
                                    <p class="font-semibold text-gray-800">#{{ $order->id }} - {{ $order->customer_name }}</p>
                                    <p class="text-sm text-gray-600">
                                        @if ($order->rider)
                                            👤 {{ $order->rider->user->name }} - Awaiting response
                                        @else
                                            Not yet assigned
                                        @endif
                                    </p>
                                </div>
                            @empty
                                <p class="text-gray-600 text-sm">No pending responses</p>
                            @endforelse
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Create New Post Item / Add Food to Categories -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-8 flex items-center gap-3">
                        <span class="text-3xl">🍕</span> Add Food Items to Categories
                    </h2>

                    @if (session('success'))
                        <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-800 border-l-4 border-green-500">
                            ✓ {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data"
                        class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf

                        <div>
                            <label for="title" class="block text-gray-700 font-bold mb-2">🍴 Food Item Name</label>
                        <input id="title" name="title" type="text"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                            placeholder="e.g., Pepperoni Pizza" value="{{ old('title') }}" required>
                        @error('title')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-gray-700 font-bold mb-2">💰 Price</label>
                        <input id="price" name="price" type="number" step="0.01"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                            placeholder="e.g., 5000" value="{{ old('price') }}" required>
                        @error('price')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-gray-700 font-bold mb-2">📝 Description</label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition resize-none"
                            placeholder="Describe your food item..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-gray-700 font-bold mb-2">🏷️ Select Food Category</label>
                        <select id="category" name="category"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-300 outline-none transition"
                            required>
                            <option value="" disabled selected>Choose a category...</option>
                            <option value="pizza">🍕 Pizza</option>
                            <option value="burger">🍔 Burger</option>
                            <option value="salad">🥗 Salad</option>
                            <option value="drinks">🥤 Drinks</option>
                        </select>
                        @error('category')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        </div>

                        <div>
                            <label for="image" class="block text-gray-700 font-bold mb-2">📸 Food Image</label>
                        <input id="image" name="image" type="file" accept="image/*"
                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-yellow-500 outline-none transition"
                            required>
                        @error('image')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        </div>

                        <div class="md:col-span-2 flex gap-4">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                            ➕ Add Food Item
                        </button>
                        <button type="reset"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 rounded-lg transition">
                            🔄 Clear
                        </button>
                        </div>
                    </form>
                </div>

            @empty
                <div class="bg-white rounded-xl shadow-lg p-8 text-center mb-8">
                    <p class="text-2xl font-bold text-gray-800 mb-4">📭 No Store Found</p>
                    <p class="text-gray-600 mb-6">You don't have a store registered yet.</p>
                    <a href="{{ route('store.info') }}"
                        class="inline-block bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-8 rounded-lg transition shadow-md">
                        ➕ Register Store
                    </a>
                </div>
            @endempty
        </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const storeLatitude = @json($stores->first()->latitude ?? null);
        const storeLongitude = @json($stores->first()->longitude ?? null);
        const storeRiderMap = L.map('storeRiderMap').setView([storeLatitude || 6.5244, storeLongitude || 3.3792], storeLatitude ? 12 : 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(storeRiderMap);

        function yellowMarker(color = '#facc15') {
            return L.divIcon({
                className: 'custom-pin',
                html: `<span style="display:block;width:18px;height:18px;border-radius:50%;background:${color};border:3px solid #f59e0b;box-shadow:0 0 0 2px rgba(255,255,255,0.8);"></span>`,
                iconSize: [18, 18],
                iconAnchor: [9, 9],
                popupAnchor: [0, -10]
            });
        }

        if (storeLatitude && storeLongitude) {
            L.marker([storeLatitude, storeLongitude], { icon: yellowMarker('#facc15') }).addTo(storeRiderMap).bindPopup('Your store');
        }

        @foreach($allRiders as $rider)
            @if($rider->is_online && $rider->latitude && $rider->longitude)
                L.marker([{{ $rider->latitude }}, {{ $rider->longitude }}], { icon: yellowMarker('#fbbf24') }).addTo(storeRiderMap).bindPopup('Online rider: {{ addslashes($rider->user->name ?? $rider->name) }}');
            @endif
        @endforeach
    </script>
@endsection