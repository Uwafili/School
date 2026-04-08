<!-- filepath: c:\Users\Bishop\School\resources\views\auth\register.blade.php -->
@extends('layouts.navbar')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="min-h-screen flex items-center justify-center bg-gray-100">
    
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center text-yellow-600 mb-6">Welcome back</h2>
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 mb-2">Email Address</label>
                <input id="email" type="text" name="email" value="{{ old('email') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4 relative">
                <label for="password" class="block text-gray-700 mb-2">Password</label>
                <input id="password" type="password" name="password"
                    class="w-full px-4 py-2 pr-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <button type="button" onclick="togglePassword()" class="absolute right-2 top-10 text-gray-500 hover:text-gray-700">
                    <i id="eyeIcon" class="fas fa-eye"></i>
                </button>
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
         @error('failed')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
         @enderror

            <button type="submit" id="loginBtn"
                class="w-full bg-yellow-500 text-white py-2 rounded-lg font-semibold hover:bg-yellow-600 transition flex items-center justify-center">
                <i class="fas fa-spinner fa-spin mr-2" id="loadingIcon" style="display:none;"></i>
                Register
            </button>
        </form>
        <div class="mt-4 text-center text-gray-600">
             Don't have an account?
            <a href="{{ route('register') }}" class="text-yellow-600 hover:underline">Register</a>
        </div>
    </div>
</div>
@endsection

<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');

        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Logging in...';
    });
</script>
