@extends('layouts.navbar')
@section('content')

<div class="max-w-7xl mx-auto p-6">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Riders Management</h1>

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

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
</td>




                </tr>

                
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
