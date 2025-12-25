@extends('layouts.navbar')
@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md text-center">

        <h2 class="text-2xl font-bold mb-4">Bank Transfer</h2>

        <p class="mb-4 text-gray-700">
            Please transfer the exact amount below:
        </p>

        <p class="text-3xl font-bold text-green-600 mb-6">
            {{-- ₦{{ number_format($amount) }} --}}
        </p>

        <div class="border rounded p-4 mb-6 text-left bg-gray-50">
            <p><strong>Bank Name:</strong> Access Bank</p>
            <p><strong>Account Name:</strong> Food Express Ltd</p>
            <p><strong>Account Number:</strong> 1234567890</p>
        </div>

        <form action="" method="POST">
            @csrf
            <input type="hidden" name="amount" value="">

            <input type="text" name="name" placeholder="Your Name"
                class="w-full border p-2 rounded mb-3" required>

            <input type="email" name="email" placeholder="Your Email"
                class="w-full border p-2 rounded mb-4" required>

            <button class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold">
                I Have Made Payment
            </button>
        </form>

        @if(session('success'))
            <p class="mt-4 text-green-600 font-semibold">
                {{ session('success') }}
            </p>
        @endif
    </div>
</div>

@endsection