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
</td>




                </tr>

                
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
