@extends('layouts.navbar')

@section('content')
<main class="min-h-[calc(100vh-4rem)] bg-[#fffaf0] px-4 py-12 sm:px-6">
    <div class="mx-auto max-w-md rounded-3xl bg-white p-6 shadow-xl sm:p-10">
        <p class="text-sm font-bold uppercase tracking-[.22em] text-yellow-600">Account recovery</p>
        <h1 class="mt-2 text-3xl font-black text-stone-900">Reset your password</h1>
        <p class="mt-3 text-sm leading-6 text-stone-500">Choose a new password for your FoodStore account.</p>

        @if ($errors->any())
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-stone-700">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="email" class="w-full rounded-xl border border-stone-200 px-4 py-3 text-stone-900 outline-none transition focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100">
            </div>
            <div>
                <label for="password" class="mb-2 block text-sm font-semibold text-stone-700">New password</label>
                <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-stone-200 px-4 py-3 text-stone-900 outline-none transition focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100" placeholder="At least 8 characters">
            </div>
            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-stone-700">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-stone-200 px-4 py-3 text-stone-900 outline-none transition focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100">
            </div>
            <button type="submit" class="w-full rounded-xl bg-yellow-500 px-4 py-3 font-bold text-white shadow-lg shadow-yellow-500/20 transition hover:bg-yellow-600 focus:outline-none focus:ring-4 focus:ring-yellow-200">Reset password</button>
        </form>
    </div>
</main>
@endsection
