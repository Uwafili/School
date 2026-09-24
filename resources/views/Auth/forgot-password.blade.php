@extends('layouts.navbar')

@section('content')
<main class="min-h-[calc(100vh-4rem)] bg-[#fffaf0] px-4 py-12 sm:px-6">
    <div class="mx-auto max-w-md rounded-3xl bg-white p-6 shadow-xl sm:p-10">
        <p class="text-sm font-bold uppercase tracking-[.22em] text-yellow-600">Account recovery</p>
        <h1 class="mt-2 text-3xl font-black text-stone-900">Forgot your password?</h1>
        <p class="mt-3 text-sm leading-6 text-stone-500">Enter your email address and we will send you a secure reset link.</p>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-stone-700">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="w-full rounded-xl border border-stone-200 px-4 py-3 text-stone-900 outline-none transition focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100" placeholder="you@example.com">
            </div>
            <button type="submit" class="w-full rounded-xl bg-yellow-500 px-4 py-3 font-bold text-white shadow-lg shadow-yellow-500/20 transition hover:bg-yellow-600 focus:outline-none focus:ring-4 focus:ring-yellow-200">Email reset link</button>
        </form>

        <p class="mt-8 text-center text-sm text-stone-600"><a href="{{ route('login') }}" class="font-bold text-yellow-600 hover:text-yellow-700">Back to sign in</a></p>
    </div>
</main>
@endsection
