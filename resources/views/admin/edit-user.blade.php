@extends('layouts.navbar')

@section('content')
<main class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('manage.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 transition hover:text-[#14213D]">← Back to user management</a>

        <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 bg-slate-50/80 px-5 py-6 sm:px-8">
                <p class="text-xs font-bold uppercase tracking-[.15em] text-amber-700">Administrator tools</p>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-[#14213D] sm:text-3xl">Edit user</h1>
                <p class="mt-2 text-sm text-slate-500">Update account details for {{ $user->name }}. Leave the password blank to keep it unchanged.</p>
            </header>

            @if ($errors->any())
                <div role="alert" class="mx-5 mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 sm:mx-8">
                    <p class="font-bold">Please review the highlighted information.</p>
                    <ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @if (session('error'))
                <div role="alert" class="mx-5 mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 sm:mx-8">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('user.update', $user) }}" class="grid gap-5 p-5 sm:grid-cols-2 sm:p-8" data-submit-feedback data-loading-text="Saving user...">
                @csrf
                @method('PUT')
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1.5 block text-sm font-bold text-slate-700">Name <span class="text-red-600">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name" class="h-11 w-full rounded-xl border px-3.5 text-sm text-slate-900 outline-none transition focus:ring-4 focus:ring-amber-100 @error('name') border-red-400 focus:border-red-500 @else border-slate-300 focus:border-amber-500 @enderror" @error('name') aria-invalid="true" aria-describedby="error-name" @enderror>
                    @error('name')<p id="error-name" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="email" class="mb-1.5 block text-sm font-bold text-slate-700">Email address <span class="text-red-600">*</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="email" class="h-11 w-full rounded-xl border px-3.5 text-sm text-slate-900 outline-none transition focus:ring-4 focus:ring-amber-100 @error('email') border-red-400 focus:border-red-500 @else border-slate-300 focus:border-amber-500 @enderror" @error('email') aria-invalid="true" aria-describedby="error-email" @enderror>
                    @error('email')<p id="error-email" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-bold text-slate-700">New password</label>
                    <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" class="h-11 w-full rounded-xl border px-3.5 text-sm text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100 @error('password') border-red-400 @else border-slate-300 @enderror" @error('password') aria-invalid="true" aria-describedby="error-password" @enderror>
                    @error('password')<p id="error-password" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-bold text-slate-700">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" class="h-11 w-full rounded-xl border border-slate-300 px-3.5 text-sm text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                </div>
                <p class="text-xs leading-5 text-slate-500 sm:col-span-2">Account role: <span class="font-bold capitalize text-slate-700">{{ $user->usertype }}</span>. Change roles from the user management list.</p>
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:col-span-2 sm:flex-row sm:justify-end">
                    <a href="{{ route('manage.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#14213D] px-5 text-sm font-black text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2">Save changes</button>
                </div>
            </form>
        </section>
    </div>
</main>
@endsection
