<!-- filepath: c:\Users\Bishop\School\resources\views\users\dashboard.blade.php -->
@extends('layouts.navbar')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-yellow-50">
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-lg p-8 mt-10">
        <h1 class="text-3xl font-bold text-yellow-600 mb-4">Welcome, {{ Auth::user()->name ?? 'user' }}!</h1>
        <p class="text-gray-700 mb-6">
            This is your dashboard. Here you can view your recent orders, update your profile, and manage your account.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="#" class="bg-yellow-100 hover:bg-yellow-200 rounded-lg p-6 flex flex-col items-center transition">
                <svg class="w-10 h-10 text-yellow-500 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v4a1 1 0 001 1h3v6a1 1 0 001 1h6a1 1 0 001-1v-6h3a1 1 0 001-1V7a1 1 0 00-1-1H4a1 1 0 00-1 1z"/>
                </svg>
                <span class="font-semibold text-gray-800">My Orders</span>
            </a>
            <a href="#" class="bg-yellow-100 hover:bg-yellow-200 rounded-lg p-6 flex flex-col items-center transition">
                <svg class="w-10 h-10 text-yellow-500 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.847.607 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="font-semibold text-gray-800">My Profile</span>
            </a>
        </div>
        <div class="mt-8 text-center">
            <a href="{{route('home') }}" class="text-yellow-600 hover:underline">← Back to Home</a>
        </div>
    </div>
</div>
@endsection