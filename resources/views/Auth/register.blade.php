@extends('layouts.navbar')

@section('content')
<style>
    .auth-page { min-height: calc(100vh - 4rem); background: #fffaf0; }
    .auth-visual { background-image: linear-gradient(105deg, rgba(35, 28, 16, .88), rgba(35, 28, 16, .2)), url('{{ asset('asset/open.avif') }}'); background-position: center; background-size: cover; }
    .auth-input:focus { border-color: #eab308; box-shadow: 0 0 0 3px rgba(234, 179, 8, .18); outline: none; }
    .password-toggle:focus { border-radius: .5rem; outline: 2px solid #eab308; outline-offset: 2px; }
</style>

<main class="auth-page flex items-center justify-center px-4 py-10 sm:px-6">
    <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-2xl lg:grid-cols-2">
        <section class="auth-visual relative hidden min-h-[620px] flex-col justify-end p-10 text-white lg:flex">
            <div class="relative z-10 max-w-sm">
                <p class="mb-3 text-sm font-bold uppercase tracking-[.28em] text-yellow-300">Made for food lovers</p>
                <h1 class="text-4xl font-black leading-tight">Bring more flavour to your day.</h1>
                <p class="mt-4 text-base leading-7 text-white/80">Create your free account and discover your new go-to meals.</p>
            </div>
        </section>

        <section class="p-6 sm:p-10 lg:p-12">
            <div class="mx-auto max-w-md">
                <div class="mb-8">
                    <p class="text-sm font-bold uppercase tracking-[.22em] text-yellow-600">Join FoodStore</p>
                    <h2 class="mt-2 text-3xl font-black text-stone-900">Create your account</h2>
                    <p class="mt-2 text-sm text-stone-500">Your table for better meals starts here.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('register.store') }}" id="registerForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-stone-700">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" class="auth-input w-full rounded-xl border border-stone-200 px-4 py-3 text-stone-900 placeholder-stone-400" placeholder="Your name">
                    </div>
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-stone-700">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="auth-input w-full rounded-xl border border-stone-200 px-4 py-3 text-stone-900 placeholder-stone-400" placeholder="you@example.com">
                    </div>
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-stone-700">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" class="auth-input w-full rounded-xl border border-stone-200 px-4 py-3 pr-12 text-stone-900 placeholder-stone-400" placeholder="At least 8 characters">
                            <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-400 transition hover:text-yellow-600" onclick="togglePasswordVisibility('password', this)" aria-label="Show password">
                                <svg data-eye class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6S2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                <svg data-eye-off class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.58 10.58a2 2 0 0 0 2.83 2.83M9.88 5.08A10.7 10.7 0 0 1 12 4.88c6.25 0 9.75 7.12 9.75 7.12a17.2 17.2 0 0 1-3.14 3.9M6.23 6.23C3.7 7.93 2.25 12 2.25 12s3.5 7.12 9.75 7.12c1.25 0 2.4-.28 3.43-.73"/></svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-stone-700">Confirm password</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="auth-input w-full rounded-xl border border-stone-200 px-4 py-3 pr-12 text-stone-900 placeholder-stone-400" placeholder="Repeat your password">
                            <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-400 transition hover:text-yellow-600" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Show password confirmation">
                                <svg data-eye class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6S2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                <svg data-eye-off class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.58 10.58a2 2 0 0 0 2.83 2.83M9.88 5.08A10.7 10.7 0 0 1 12 4.88c6.25 0 9.75 7.12 9.75 7.12a17.2 17.2 0 0 1-3.14 3.9M6.23 6.23C3.7 7.93 2.25 12 2.25 12s3.5 7.12 9.75 7.12c1.25 0 2.4-.28 3.43-.73"/></svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" id="registerBtn" class="w-full rounded-xl bg-yellow-500 px-4 py-3 font-bold text-white shadow-lg shadow-yellow-500/20 transition hover:bg-yellow-600 focus:outline-none focus:ring-4 focus:ring-yellow-200">Create account</button>
                </form>

                <div class="my-7 flex items-center gap-3 text-xs uppercase tracking-widest text-stone-400"><span class="h-px flex-1 bg-stone-200"></span><span>or join with</span><span class="h-px flex-1 bg-stone-200"></span></div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('google.redirect') }}" class="flex items-center justify-center gap-2 rounded-xl border border-stone-200 px-3 py-3 text-sm font-semibold text-stone-700 transition hover:border-yellow-500 hover:bg-yellow-50" aria-label="Continue with Google"><span class="text-lg font-black text-[#4285f4]">G</span> Google</a>
                    <a href="{{ route('facebook.redirect') }}" class="flex items-center justify-center gap-2 rounded-xl border border-stone-200 px-3 py-3 text-sm font-semibold text-stone-700 transition hover:border-yellow-500 hover:bg-yellow-50" aria-label="Continue with Facebook"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-[#1877f2] text-sm font-black text-white">f</span> Facebook</a>
                </div>
                <p class="mt-8 text-center text-sm text-stone-600">Already have an account? <a href="{{ route('login') }}" class="font-bold text-yellow-600 hover:text-yellow-700">Sign in</a></p>
            </div>
        </section>
    </div>
</main>

<script>
    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        button.querySelector('[data-eye]').classList.toggle('hidden', isPassword);
        button.querySelector('[data-eye-off]').classList.toggle('hidden', !isPassword);
    }

    document.getElementById('registerForm').addEventListener('submit', function () {
        const button = document.getElementById('registerBtn');
        button.disabled = true;
        button.textContent = 'Creating account...';
    });
</script>
@endsection
