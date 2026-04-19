@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-gray-100 py-10 px-4">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold text-yellow-600 mb-2 text-center">🏪 Store Approval Management</h1>
        <p class="text-center text-gray-600 mb-8">Review and approve/reject store applications</p>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-800 border-l-4 border-green-500">
                <strong>✓ Success:</strong> {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-800 border-l-4 border-red-500">
                <strong>✗ Warning:</strong> {{ session('warning') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                <span class="text-sm font-semibold text-gray-600 uppercase mb-2">⏳ Pending Approval</span>
                <span class="text-4xl font-bold text-yellow-600">{{ $pendingCount }}</span>
                <p class="text-gray-600 text-sm mt-2">Waiting for review</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                <span class="text-sm font-semibold text-gray-600 uppercase mb-2">✅ Approved</span>
                <span class="text-4xl font-bold text-green-600">{{ $approvedCount }}</span>
                <p class="text-gray-600 text-sm mt-2">Active stores</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                <span class="text-sm font-semibold text-gray-600 uppercase mb-2">❌ Rejected</span>
                <span class="text-4xl font-bold text-red-600">{{ $rejectedCount }}</span>
                <p class="text-gray-600 text-sm mt-2">Denied applications</p>
            </div>
        </div>

        <!-- Tabs for filtering -->
        <div class="bg-white rounded-lg shadow mb-8 overflow-hidden">
            <div class="flex border-b border-gray-200">
                <a href="?status=all" class="flex-1 px-6 py-4 text-center font-semibold text-gray-700 hover:bg-gray-50 border-b-4 border-yellow-500">
                    All Stores ({{ count($stores) }})
                </a>
            </div>

            <!-- Stores Grid -->
            <div class="p-8">
                @forelse ($stores as $store)
                    <div class="bg-white rounded-xl shadow-md p-6 mb-6 border-l-4 @if($store->status === 'pending') border-yellow-500 @elseif($store->status === 'approved') border-green-500 @else border-red-500 @endif hover:shadow-lg transition">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-start">
                            <!-- Store Logo -->
                            <div class="flex flex-col items-center">
                                @if(!empty($store->image))
                                    <img src="{{ asset('storage/' . $store->image) }}" alt="Store Logo"
                                        class="w-32 h-32 object-cover rounded-lg shadow-md">
                                @else
                                    <div class="w-32 h-32 bg-gradient-to-br from-yellow-100 to-yellow-200 flex items-center justify-center rounded-lg text-yellow-600 text-4xl font-bold shadow-md">
                                        🏪
                                    </div>
                                @endif
                                <p class="text-sm text-gray-600 mt-3 font-semibold">Store Logo</p>
                            </div>

                            <!-- Store Information -->
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs font-bold text-gray-600 uppercase">Store Name</p>
                                    <p class="text-lg font-bold text-gray-800">{{ $store->stores }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-600 uppercase">Owner</p>
                                    <p class="text-gray-800 font-semibold">{{ $store->owner }}</p>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs font-bold text-gray-600 uppercase">Email</p>
                                    <p class="text-gray-800 font-semibold break-all">{{ $store->email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-600 uppercase">Phone</p>
                                    <p class="text-gray-800 font-semibold">{{ $store->phone }}</p>
                                </div>
                            </div>

                            <!-- Address & Status -->
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs font-bold text-gray-600 uppercase">Address</p>
                                    <p class="text-gray-800 font-semibold">{{ $store->address }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-600 uppercase">Status</p>
                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-bold @if($store->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($store->status === 'approved') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                        @if($store->status === 'pending')
                                            ⏳ Pending
                                        @elseif($store->status === 'approved')
                                            ✅ Approved
                                        @else
                                            ❌ Rejected
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col gap-2">
                                @if($store->status === 'pending')
                                    <form method="POST" action="{{ route('store.approve', $store->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-2 px-4 rounded-lg transition shadow-md">
                                            ✅ Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('store.reject', $store->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-2 px-4 rounded-lg transition shadow-md">
                                            ❌ Reject
                                        </button>
                                    </form>
                                @elseif($store->status === 'approved')
                                    <div class="bg-green-50 border-2 border-green-300 rounded-lg p-2 text-center">
                                        <p class="text-sm font-bold text-green-700">✅ Approved</p>
                                        <p class="text-xs text-green-600">{{ $store->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('store.reject', $store->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="w-full bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition" onclick="return confirm('Revoke approval?')">
                                            Revoke
                                        </button>
                                    </form>
                                @else
                                    <div class="bg-red-50 border-2 border-red-300 rounded-lg p-2 text-center">
                                        <p class="text-sm font-bold text-red-700">❌ Rejected</p>
                                        <p class="text-xs text-red-600">{{ $store->updated_at->format('M d, Y') }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('store.approve', $store->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="w-full bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition">
                                            Reconsider
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-8 text-center">
                        <p class="text-2xl font-bold text-yellow-700 mb-2">📭 No Stores Found</p>
                        <p class="text-yellow-600">There are currently no store applications to review.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
