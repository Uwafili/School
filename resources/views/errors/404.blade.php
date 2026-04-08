@extends('layouts.navbar')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-white-100">
    <div class="text-center">
        <div class="mb-8">
            <img src="{{ asset('asset/Magnusson.gif') }}" alt="Pizza" class="w-45 h-45 mx-auto mb-4 ">
        </div>
        <h1 class="text-6xl font-bold text-yellow-500 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Oops! Page Not Found</h2>
        <p class="text-gray-600 mb-8">The page you're looking for doesn't exist. Maybe it's out of stock?</p>
        <a href="{{ route('home') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold transition">
            Go Back Home
        </a>
    </div>
</div>
@endsection
