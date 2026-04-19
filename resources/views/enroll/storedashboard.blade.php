<!-- filepath: c:\Users\HP\Documents\GitHub\School\resources\views\enroll\storedashboard.blade.php -->
@extends('layouts.navbar')

@section('content')
    <div class="min-h-screen bg-yellow-50 py-10 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-2">
                <h1 class="text-4xl font-bold text-yellow-600">🏪 Store Dashboard</h1>
                <a href="{{ route('store.info') }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition shadow-md">
                    📋 View Store Info
                </a>
            </div>
            <p class="text-center text-gray-600 mb-8">Manage your store, orders, and riders</p>
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
                        <p class="text-3xl font-bold text-yellow-600">120</p>
                        <p class="text-xs text-gray-500 mt-2">+12 this week</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-t-4 border-green-500">
                        <p class="text-gray-600 text-sm font-semibold mb-2">💰 TOTAL REVENUE</p>
                        <p class="text-3xl font-bold text-green-600">₦250,000</p>
                        <p class="text-xs text-gray-500 mt-2">+₦35,000 this week</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-t-4 border-blue-500">
                        <p class="text-gray-600 text-sm font-semibold mb-2">⭐ STORE RATING</p>
                        <p class="text-3xl font-bold text-blue-600">4.8/5</p>
                        <p class="text-xs text-gray-500 mt-2">Based on 234 reviews</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-t-4 border-purple-500">
                        <p class="text-gray-600 text-sm font-semibold mb-2">🚴 ACTIVE RIDERS</p>
                        <p class="text-3xl font-bold text-purple-600">8</p>
                        <p class="text-xs text-gray-500 mt-2">Available for delivery</p>
                    </div>
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
                                <option value="1">#1021 - John Doe (Lekki) - ₦5,000</option>
                                <option value="2">#1022 - Jane Smith (Ikoyi) - ₦3,500</option>
                                <option value="3">#1023 - Mike Johnson (VI) - ₦4,200</option>
                                <option value="4">#1024 - Sarah Williams (Abuja) - ₦6,100</option>
                            </select>
                            <select name="rider_id"
                                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-300 outline-none transition"
                                required>
                                <option disabled selected class="text-gray-500">🚴 Select an Approved Rider</option>
                                <option value="1">🏍️ Ahmed - Motorbike (★ 4.9) - Lekki area</option>
                                <option value="2">🚴 Tunde - Bicycle (★ 4.7) - Ikoyi area</option>
                                <option value="3">🚗 Chidi - Car (★ 4.8) - VI area</option>
                                <option value="4">🏍️ Blessing - Motorbike (★ 4.6) - Abuja area</option>
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
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-5 font-semibold text-gray-800">#1001</td>
                                <td class="py-4 px-5 text-gray-700">John Doe</td>
                                <td class="py-4 px-5 font-bold text-gray-800">₦5,000</td>
                                <td class="py-4 px-5 text-gray-700">🏍️ Ahmed (4.9★)</td>
                                <td class="py-4 px-5"><span
                                        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">✓
                                        Delivered</span></td>
                                <td class="py-4 px-5 text-gray-600">2025-06-18</td>
                                <td class="py-4 px-5"><button class="text-blue-600 hover:text-blue-800 font-semibold">👁️
                                        View</button></td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-5 font-semibold text-gray-800">#1002</td>
                                <td class="py-4 px-5 text-gray-700">Jane Smith</td>
                                <td class="py-4 px-5 font-bold text-gray-800">₦3,500</td>
                                <td class="py-4 px-5 text-gray-700">🚴 Tunde (4.7★)</td>
                                <td class="py-4 px-5"><span
                                        class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">🚚 In
                                        Transit</span></td>
                                <td class="py-4 px-5 text-gray-600">2025-06-17</td>
                                <td class="py-4 px-5"><button class="text-blue-600 hover:text-blue-800 font-semibold">👁️
                                        View</button></td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-5 font-semibold text-gray-800">#1003</td>
                                <td class="py-4 px-5 text-gray-700">Mike Johnson</td>
                                <td class="py-4 px-5 font-bold text-gray-800">₦4,200</td>
                                <td class="py-4 px-5 text-gray-700">🚗 Chidi (4.8★)</td>
                                <td class="py-4 px-5"><span
                                        class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">📍
                                        Assigned</span></td>
                                <td class="py-4 px-5 text-gray-600">2025-06-17</td>
                                <td class="py-4 px-5"><button class="text-blue-600 hover:text-blue-800 font-semibold">👁️
                                        View</button></td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-5 font-semibold text-gray-800">#1004</td>
                                <td class="py-4 px-5 text-gray-700">Sarah Williams</td>
                                <td class="py-4 px-5 font-bold text-gray-800">₦6,100</td>
                                <td class="py-4 px-5 text-gray-500">Not assigned</td>
                                <td class="py-4 px-5"><span
                                        class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold">⏳
                                        Pending</span></td>
                                <td class="py-4 px-5 text-gray-600">2025-06-17</td>
                                <td class="py-4 px-5"><button class="text-blue-600 hover:text-blue-800 font-semibold">🎯
                                        Assign</button></td>
                            </tr>
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
                            <span>✅</span> Accepted Orders (3)
                        </h3>
                        <div class="space-y-3">
                            <div class="bg-white p-3 rounded border-l-2 border-green-400">
                                <p class="font-semibold text-gray-800">#1002 - Jane Smith</p>
                                <p class="text-sm text-gray-600">🚴 Tunde accepted · In Transit</p>
                            </div>
                            <div class="bg-white p-3 rounded border-l-2 border-green-400">
                                <p class="font-semibold text-gray-800">#1003 - Mike Johnson</p>
                                <p class="text-sm text-gray-600">🚗 Chidi accepted · Ready for pickup</p>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected/Pending Response -->
                    <div class="border-l-4 border-orange-500 bg-orange-50 p-6 rounded-lg">
                        <h3 class="font-bold text-orange-700 mb-4 flex items-center gap-2">
                            <span>⏳</span> Pending Responses (2)
                        </h3>
                        <div class="space-y-3">
                            <div class="bg-white p-3 rounded border-l-2 border-orange-400">
                                <p class="font-semibold text-gray-800">#1004 - Sarah Williams</p>
                                <p class="text-sm text-gray-600">🏍️ Blessing - Awaiting response</p>
                            </div>
                            <div class="bg-white p-3 rounded border-l-2 border-orange-400">
                                <p class="font-semibold text-gray-800">#1005 - David Lee</p>
                                <p class="text-sm text-gray-600">🚴 Tony - Awaiting response</p>
                            </div>
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
@endsection