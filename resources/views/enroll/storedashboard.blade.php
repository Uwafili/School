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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="mb-2"><span class="font-semibold text-gray-700">Store Name:</span> {{ $store->stores ?? 'Your Store' }}</p>
                    <p class="mb-2"><span class="font-semibold text-gray-700">Owner:</span> {{ $store->owner ?? 'Owner Name' }}</p>
                    <p class="mb-2"><span class="font-semibold text-gray-700">Email:</span> {{ $store->email ?? 'store@email.com' }}</p>
                    <p class="mb-2"><span class="font-semibold text-gray-700">Phone:</span> {{ $store->phone ?? '000-000-0000' }}</p>
                    <p class="mb-2"><span class="font-semibold text-gray-700">Address:</span> {{ $store->address ?? 'Store Address' }}</p>
                </div>
                <div class="flex items-center justify-center">
                    @if(!empty($store->image))
                        <img src="{{storage('post_image/' . $store->image) }}" alt="Store Logo" class="w-32 h-32 object-cover rounded-full shadow">
                    @else
                        <div class="w-32 h-32 bg-yellow-100 flex items-center justify-center rounded-full text-yellow-600 text-2xl font-bold">No Logo</div>
                    @endif
                </div>
            </div>
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
@endsection