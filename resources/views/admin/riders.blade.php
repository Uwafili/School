@extends('layouts.navbar')
@section('content')

<div class="max-w-7xl mx-auto p-6">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Riders Management</h1>

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-4 bg-amber-100 text-amber-900 px-4 py-3 rounded-lg">
            {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <section class="mb-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="mb-1 text-xl font-bold text-gray-800">Quick sign up: Rider</h2>
        <p class="mb-4 text-sm text-gray-600">The rider application will be pending approval. A password setup link will be sent to the rider.</p>
        <form action="{{ route('admin.riders.store') }}" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @csrf
            <div><label for="rider-name" class="mb-1 block text-sm font-semibold">Name</label><input id="rider-name" name="name" value="{{ old('name') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2">@error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label for="rider-email" class="mb-1 block text-sm font-semibold">Email</label><input id="rider-email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2">@error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label for="rider-phone" class="mb-1 block text-sm font-semibold">Phone</label><input id="rider-phone" name="phone" value="{{ old('phone') }}" required maxlength="15" class="w-full rounded-lg border border-gray-300 px-3 py-2">@error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label for="rider-license" class="mb-1 block text-sm font-semibold">License</label><input id="rider-license" name="license" value="{{ old('license') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2">@error('license')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label for="rider-vehicle-number" class="mb-1 block text-sm font-semibold">Vehicle number</label><input id="rider-vehicle-number" name="vehicle_number" value="{{ old('vehicle_number') }}" required maxlength="15" class="w-full rounded-lg border border-gray-300 px-3 py-2">@error('vehicle_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label for="rider-vehicle" class="mb-1 block text-sm font-semibold">Vehicle</label><input id="rider-vehicle" name="vehicle" value="{{ old('vehicle') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2">@error('vehicle')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div class="md:col-span-2 xl:col-span-3"><button type="submit" class="rounded-lg bg-yellow-500 px-5 py-2 font-bold text-white hover:bg-yellow-600">Create rider</button></div>
        </form>
    </section>

    <section class="mb-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="mb-1 text-xl font-bold text-gray-800">Assign a ready order</h2>
            <p class="mb-4 text-sm text-gray-600">Choose a paid order and an approved rider who is active and online.</p>
            <livewire:order-assignment :admin-mode="true" />
            @if($readyOrders->isEmpty() || $availableRiders->isEmpty())
                <p class="mt-3 text-xs text-gray-500">Assignment requires at least one ready paid order and one available rider.</p>
            @endif
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-xl font-bold text-gray-800">Riders handling deliveries</h2>
            <div class="space-y-3">
                @forelse($activeOrders as $order)
                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg bg-gray-50 p-3">
                        <div>
                            <p class="font-bold text-gray-800">#{{ $order->id }} · {{ $order->customer_name }}</p>
                            <p class="text-sm text-gray-600">{{ $order->rider?->name ?? 'Rider unavailable' }} · {{ $order->rider?->phone }}</p>
                        </div>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No active rider deliveries.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-xl font-bold text-gray-800">Recent completed deliveries</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-gray-700">
                <thead class="border-b bg-gray-50"><tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Rider</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Delivered</th></tr></thead>
                <tbody>
                    @forelse($deliveryHistory as $order)
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold">{{ $order->order_reference }}</td>
                            <td class="px-4 py-3">{{ $order->rider?->name ?? 'Rider unavailable' }}</td>
                            <td class="px-4 py-3">{{ $order->customer_name }}</td>
                            <td class="px-4 py-3">{{ $order->recipient_verified_at?->format('M j, Y · g:i A') ?? $order->updated_at->format('M j, Y · g:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-3 text-gray-500">No completed deliveries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="overflow-x-auto bg-white shadow-md rounded-xl">
        <table class="min-w-full text-left text-gray-700">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-sm font-bold uppercase">Name</th>
                    <th class="px-6 py-3 text-sm font-bold uppercase">Email</th>
                    <th class="px-6 py-3 text-sm font-bold uppercase">Phone</th>
                    <th class="px-6 py-3 text-sm font-bold uppercase">License</th>
                    <th class="px-6 py-3 text-sm font-bold uppercase">Vehicle Number</th>
                    <th class="px-6 py-3 text-sm font-bold uppercase">vehicle</th>
                    <th class="px-6 py-3 text-sm font-bold uppercase">image</th>
                    <th class="px-6 py-3 text-sm font-bold uppercase">View details</th>
                    <th class="px-6 py-3 text-sm font-bold uppercase">Availability</th>
                    <th class="px-6 py-3 text-sm font-bold uppercase text-center">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($Riders as $Rider)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium">{{ $Rider->name }}</td>
                    <td class="px-6 py-4 font-medium">{{ $Rider->email }}</td>
                    <td class="px-6 py-4 font-medium">{{ $Rider->phone }}</td>
                    <td class="px-6 py-4 font-medium">{{ $Rider->license }}</td>
                    <td class="px-6 py-4">{{ $Rider->vehicle_number }}</td>
                    <td class="px-6 py-4">{{ $Rider->vehicle }}</td>
                    <td class="px-6 py-4">{{ $Rider->image }}</td>
                    <td class="px-6 py-4"><a href="{{ route('viewdetail', $Rider->id)}}">View details</a></td>
                    <td class="px-6 py-4">
                        <span class="rounded-full px-3 py-1 text-sm {{ $Rider->is_online && $Rider->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $Rider->is_active ? ($Rider->is_online ? 'Online' : 'Offline') : 'Deactivated' }}</span>
                    </td>
                    <td class="px-6 py-4">
    @if($Rider->status === 'approved')
        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
            Approved
        </span>
    @elseif($Rider->status === 'rejected')
        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">
            Rejected
        </span>
    @else
        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">
            Pending
        </span>
    @endif

    <a href="{{ route('riders.edit', $Rider) }}" class="mt-2 inline-block rounded-lg bg-yellow-500 px-3 py-2 text-sm font-bold text-white transition hover:bg-yellow-600">
        Edit rider
    </a>
    @if($Rider->status === 'approved')
        <form action="{{ route('admin.riders.toggle-active', $Rider) }}" method="POST" class="mt-2">
            @csrf
            <button type="submit" class="rounded-lg px-3 py-2 text-sm font-bold text-white {{ $Rider->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                {{ $Rider->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
    @endif
</td>




                </tr>

                
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
