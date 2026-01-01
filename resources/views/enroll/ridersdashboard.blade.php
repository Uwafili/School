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

        <!-- Welcome Card -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold">Welcome, {{ auth()->user()->name }} 👋</h2>

        </div>

        <!-- Stats (Approved Only) -->
        @if($Rider->status === 'approved')
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Deliveries</p>
                <h3 class="text-2xl font-bold">24</h3>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Earnings</p>
                <h3 class="text-2xl font-bold">₦120,000</h3>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Today</p>
                <h3 class="text-2xl font-bold">3</h3>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Rating</p>
                <h3 class="text-2xl font-bold">⭐ 4.8</h3>
            </div>
        </div>
        @endif

        <!-- Rider Info -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h3 class="font-semibold mb-4">My Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <p><strong>Full Name:</strong> {{$Rider->name  }}</p>
                <p><strong>Phone:</strong> {{ $Rider->phone }}</p>
                <p><strong> License Number:</strong> {{ $Rider->license }}</p>
                <p><strong>Bike Number:</strong> {{ $Rider->vehicle }}</p>
            </div>
        </div>

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
            <h3 class="font-semibold mb-4">Notifications</h3>

            @forelse(auth()->user()->notifications as $note)
                <div class="border-b py-2 text-sm">
                    {{ $note->data['message'] }}
                    <span class="text-xs text-gray-400 block">
                        {{ $note->created_at->diffForHumans() }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-500">No notifications yet</p>
            @endforelse
        </div>

    </div>
</div>

@endsection 