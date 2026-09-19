@extends('layouts.navbar')

@section('content')
@php
    $payment = $payment ?? session('pending_payment', []);
    $amount = (float) ($payment['amount'] ?? 0);
    $walletBalance = (float) (Auth::user()->wallet_balance ?? 0);
@endphp
<div class="min-h-screen bg-yellow-50 px-3 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-2xl">
        @if(session('success'))<div class="mb-5 rounded-2xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="mb-5 rounded-2xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>@endif
        <div class="mb-6"><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">FoodStore payment</p><h1 class="mt-1 text-2xl font-black text-gray-900 sm:text-3xl">Choose how to pay</h1><p class="mt-2 text-sm text-gray-500">Use your FoodStore wallet or complete a bank transfer.</p></div>
        <section class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-yellow-100">
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 p-6 text-gray-900 sm:p-8"><p class="text-xs font-black uppercase tracking-widest text-orange-950/60">Amount to pay</p><p class="mt-2 text-4xl font-black">₦{{ number_format($amount, 2) }}</p><p class="mt-2 text-sm font-semibold text-orange-950/70">Your order total is ready for payment.</p></div>
            <div class="p-5 sm:p-8">
                <div class="mb-6 grid grid-cols-2 gap-2 rounded-2xl bg-gray-100 p-1" role="tablist"><button type="button" id="bankTab" class="payment-tab rounded-xl bg-white px-3 py-3 text-sm font-black text-gray-900 shadow-sm" data-method="bank">Bank transfer</button><button type="button" id="walletTab" class="payment-tab rounded-xl px-3 py-3 text-sm font-black text-gray-500" data-method="wallet">FoodStore wallet</button></div>
                <form id="paymentForm" action="{{ route('bank.confirm') }}" method="POST">@csrf<input type="hidden" name="payment_method" id="paymentMethod" value="bank">
                    <div id="bankPanel"><div class="mb-6 rounded-2xl bg-yellow-50 p-5"><div class="mb-4 flex items-center justify-between"><h2 class="text-base font-black text-gray-900">Transfer to FoodStore</h2><span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-orange-600">Exact amount</span></div><dl class="space-y-3 text-sm"><div class="flex justify-between gap-4"><dt class="text-gray-500">Bank name</dt><dd class="font-black text-gray-800">Access Bank</dd></div><div class="flex justify-between gap-4"><dt class="text-gray-500">Account name</dt><dd class="font-black text-gray-800">Food Express Ltd</dd></div><div class="flex justify-between gap-4"><dt class="text-gray-500">Account number</dt><dd class="font-black tracking-wider text-gray-800">1234567890</dd></div></dl></div><h2 class="mb-4 text-lg font-black text-gray-900">Confirm your transfer</h2><div class="space-y-4"><div><label for="name" class="mb-1.5 block text-sm font-bold text-gray-700">Your name</label><input id="name" name="name" type="text" value="{{ old('name', Auth::user()->name ?? '') }}" class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300"></div><div><label for="email" class="mb-1.5 block text-sm font-bold text-gray-700">Email address</label><input id="email" name="email" type="email" value="{{ old('email', Auth::user()->email ?? '') }}" class="w-full rounded-xl border-gray-200 px-4 py-3 focus:border-yellow-500 focus:ring-yellow-300"></div></div></div>
                    <div id="walletPanel" class="hidden"><div class="rounded-2xl bg-purple-600 p-5 text-white"><p class="text-xs font-black uppercase tracking-widest text-purple-100">Available wallet balance</p><p class="mt-2 text-3xl font-black">₦{{ number_format($walletBalance, 2) }}</p><div class="mt-5 flex items-center justify-between border-t border-white/20 pt-4 text-sm"><span>Order total</span><strong>₦{{ number_format($amount, 2) }}</strong></div></div><div class="mt-4 rounded-2xl {{ $walletBalance >= $amount ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }} p-4 text-sm font-semibold">{{ $walletBalance >= $amount ? 'Your wallet can cover this order.' : 'Your wallet balance is not enough for this order.' }}</div></div>
                    <button id="payButton" type="submit" class="mt-7 w-full rounded-2xl bg-gray-900 py-3.5 text-sm font-black text-white transition hover:bg-yellow-500 hover:text-gray-900">I have made payment</button>
                </form>
                <a href="{{ route('cart') }}" class="mt-4 block text-center text-sm font-bold text-gray-500 transition hover:text-yellow-700">Return to cart</a>
            </div>
        </section>
    </div>
</div>
<script>
    const tabs = document.querySelectorAll('.payment-tab');
    const methodInput = document.getElementById('paymentMethod');
    const bankPanel = document.getElementById('bankPanel');
    const walletPanel = document.getElementById('walletPanel');
    const payButton = document.getElementById('payButton');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const walletAvailable = {{ $walletBalance >= $amount ? 'true' : 'false' }};
    tabs.forEach(tab => tab.addEventListener('click', () => {
        const wallet = tab.dataset.method === 'wallet';
        tabs.forEach(item => item.classList.toggle('bg-white', item === tab));
        tabs.forEach(item => item.classList.toggle('text-gray-900', item === tab));
        tabs.forEach(item => item.classList.toggle('text-gray-500', item !== tab));
        tabs.forEach(item => item.classList.toggle('shadow-sm', item === tab));
        methodInput.value = wallet ? 'wallet' : 'bank';
        bankPanel.classList.toggle('hidden', wallet);
        walletPanel.classList.toggle('hidden', !wallet);
        nameInput.required = !wallet;
        emailInput.required = !wallet;
        payButton.disabled = wallet && !walletAvailable;
        payButton.textContent = wallet ? 'Pay with wallet' : 'I have made payment';
    }));
</script>
@endsection
