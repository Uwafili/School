@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-2xl rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100 sm:p-8">
        <div class="mb-7 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Your account</p>
                <h1 class="mt-1 text-2xl font-black text-gray-900">Edit profile</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-yellow-600">Back</a>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="mb-2 block text-sm font-bold text-gray-700">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="mb-2 block text-sm font-bold text-gray-700">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="border-t border-gray-100 pt-5">
                <p class="mb-4 text-sm font-black text-gray-900">Change password <span class="font-normal text-gray-400">(optional)</span></p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <input name="password" type="password" placeholder="New password" class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300">
                    <input name="password_confirmation" type="password" placeholder="Confirm password" class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300">
                </div>
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-yellow-500 to-orange-500 py-3 font-black text-white shadow-sm transition hover:from-yellow-600 hover:to-orange-600">Save profile</button>
        </form>
    </div>
</div>
@endsection
