@extends('layouts.navbar')
@section('content')
      <div class="max-w-xl mx-auto mt-10 bg-white shadow-lg rounded-xl p-6">

    <h1 class="text-2xl font-bold text-gray-800 mb-4">Rider Details</h1>

    <p class="mb-3">
        <span class="font-semibold">Name:</span>
        {{ $Riders->name }}
    </p>

    <p class="mb-3">
        <span class="font-semibold">Phone:</span>
        {{ $Riders->email }}
    </p>

    <p class="mb-3">
        <span class="font-semibold">Phone:</span>
        {{ $Riders->phone }}
    </p>

    <p class="mb-3">
        <span class="font-semibold">License:</span>
        {{ $Riders->license }}
    </p>

    <p class="mb-3">
        <span class="font-semibold">Vehicle Number:</span>
        {{ $Riders->vehicle_number }}
    </p>

    <p class="mb-3">
        <span class="font-semibold">Vehicle:</span>
        {{ $Riders->vehicle }}
    </p>

    <p class="mb-3">
        <span class="font-semibold">Image:</span>
          <img src="{{ asset('storage/'.$Riders->image) }}" 
             class="w-40 h-40 object-cover rounded-full mx-auto mb-4 shadow">
    </p>

    <p class="mb-3">
        <span class="font-semibold">Status:</span>
        @if($Riders->approved)
            <span class="text-green-600 font-medium">Approved</span>
        @else
            <span class="text-yellow-600 font-medium">Pending</span>
        @endif
    </p>

    <a href="{{route('riders') }}"
       class="mt-6 inline-block bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg">
       Back
    </a>

             {{-- @if(!$Riders->approved)
                            <form action="{{ route('Approve', $Riders->id) }}" method="POST">
                                @csrf
                                <button 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm shadow transition">
                                    Approve
                                </button>
                            </form>

            
                        @else
                            <span class="text-gray-500 text-sm">—</span>
                        @endif --}}






     @if($Riders->status == 'pending')
    <!-- Approve Button -->
    <form action="{{ route('Approve', $Riders->id) }}" method="POST">
        @csrf
        <button class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Approve
                </button>
            </form>

                
            <!-- Reject Button -->
            <form action="{{ route('Reject', $Riders->id) }}" method="POST">
                @csrf
                <button class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Reject
                </button>
            </form>
            @endif

</div>

@endsection