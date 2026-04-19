@extends('layouts.navbar')
@section('content')
    <div class="min-h-screen bg-gray-100">

        <div class="bg-white shadow px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Rider Dashboard</h1>

            <div class="flex items-center gap-4">
                <!-- Notifications -->
                <a href="#" class="relative">
                    🔔
                    {{-- @if(auth()->user()->unreadNotifications->count())
                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs px-2 rounded-full">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                    @endif --}}
                </a>

                <!-- User -->
                <span class="font-medium">{{ auth()->user()->name }}</span>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-6 space-y-6">

            <!-- Welcome Card with Approval Status -->
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-6 rounded-xl shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Welcome, {{ auth()->user()->name }} 👋</h2>
                        <p class="text-sm opacity-90 mt-1">Your account has been approved! You're ready to start accepting
                            deliveries.</p>
                    </div>
                    <div class="text-4xl">✅</div>
                </div>
            </div>

            <!-- Profile Card with Photo -->
            <div class="bg-white p-6 rounded-xl shadow">
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <!-- Profile Photo -->
                    <div class="flex flex-col items-center">
                        <img src="{{ $Rider->image ? asset('storage/' . $Rider->image) : asset('images/default-avatar.png') }}"
                            alt="{{ $Rider->name }}"
                            class="w-40 h-40 rounded-full object-cover border-4 border-yellow-500 shadow-lg">
                        <div class="mt-4 text-center">
                            <h3 class="text-xl font-bold text-gray-800">{{ $Rider->name }}</h3>
                            <p class="text-sm text-green-600 font-semibold">✓ Approved Rider</p>
                        </div>
                    </div>

                    <!-- Contact & Vehicle Info -->
                    <div class="flex-1 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Email</label>
                                <p class="text-gray-800">{{ $Rider->email }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Phone</label>
                                <p class="text-gray-800">{{ $Rider->phone }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">License
                                    Number</label>
                                <p class="text-gray-800">{{ $Rider->license }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Vehicle Type</label>
                                <p class="text-gray-800">{{ $Rider->vehicle }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Vehicle
                                    Number</label>
                                <p class="text-gray-800">{{ $Rider->vehicle_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats (Approved Only) -->
            @if($Rider->status === 'approved')
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition">
                        <p class="text-sm text-gray-500">Total Deliveries</p>
                        <h3 class="text-2xl font-bold text-yellow-600">24</h3>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition">
                        <p class="text-sm text-gray-500">Total Earnings</p>
                        <h3 class="text-2xl font-bold text-green-600">₦120,000</h3>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition">
                        <p class="text-sm text-gray-500">Today's Deliveries</p>
                        <h3 class="text-2xl font-bold text-blue-600">3</h3>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition">
                        <p class="text-sm text-gray-500">Rating</p>
                        <h3 class="text-2xl font-bold text-orange-600">⭐ 4.8</h3>
                    </div>
                </div>
            @endif

            <!-- Availability -->
            @if($Rider->status === 'approved')
                <div class="bg-white p-6 rounded-xl shadow flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold">Availability</h3>
                        <p class="text-sm text-gray-500">Go online to receive jobs</p>
                    </div>

                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" class="hidden">
                        <div class="w-12 h-6 bg-gray-300 rounded-full p-1">
                            <div class="w-4 h-4 bg-white rounded-full"></div>
                        </div>
                    </label>
                </div>
            @endif

            <!-- Delivery Requests -->
            @if($Rider->status === 'approved')
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="font-semibold mb-4">Incoming Delivery Requests</h3>

                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th>Order</th>
                                <th>Pickup</th>
                                <th>Drop-off</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t">
                                <td>#1021</td>
                                <td>Ikeja</td>
                                <td>Lekki</td>
                                <td class="space-x-2">
                                    <button class="bg-green-600 text-white px-3 py-1 rounded">Accept</button>
                                    <button class="bg-red-600 text-white px-3 py-1 rounded">Reject</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Notifications -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="font-semibold mb-4">Recent Activity</h3>

                <div class="space-y-3">
                    <div class="border-l-4 border-green-500 pl-4 py-2">
                        <p class="text-sm text-gray-800"><strong>✓ Account Approved</strong></p>
                        <span class="text-xs text-gray-400">Your rider account has been verified and approved</span>
                    </div>
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <p class="text-sm text-gray-800"><strong>📋 Registration Complete</strong></p>
                        <span class="text-xs text-gray-400">Your profile information has been saved</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection