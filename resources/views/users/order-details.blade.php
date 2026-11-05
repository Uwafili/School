@extends('layouts.navbar')

@section('content')
@php
    $workflowSteps = [
        'pending' => 'Placed',
        'approved' => 'Approved',
        'preparing' => 'Preparing',
        'ready_for_pickup' => 'Ready for pickup',
        'rider_assigned' => 'Rider assigned',
        'picked_up' => 'Picked up',
        'out_for_delivery' => 'Out for delivery',
        'delivered' => 'Delivered',
    ];
    $currentStep = array_search($order->status, array_keys($workflowSteps), true);
@endphp
<main class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl space-y-5">
        <a href="{{ route('dashboard') }}" class="inline-flex font-bold text-orange-700 hover:text-orange-900">← Back to orders</a>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 sm:p-8">
            <p class="text-xs font-black uppercase tracking-widest text-orange-600">Order details</p>
            <h1 class="mt-2 text-2xl font-black text-gray-900">{{ $order->order_reference }}</h1>
            <p class="mt-1 text-sm text-gray-500">Placed {{ $order->created_at->format('M j, Y · g:i A') }}</p>

            <dl class="mt-6 grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl bg-gray-50 p-4"><dt class="text-xs font-bold uppercase text-gray-500">Store</dt><dd class="mt-1 font-bold text-gray-900">{{ $order->store->stores }}</dd></div>
                <div class="rounded-xl bg-gray-50 p-4"><dt class="text-xs font-bold uppercase text-gray-500">Order status</dt><dd class="mt-1 font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</dd></div>
                <div class="rounded-xl bg-gray-50 p-4"><dt class="text-xs font-bold uppercase text-gray-500">Store approval</dt><dd class="mt-1 font-bold text-gray-900">{{ ucfirst($order->store_approval_status) }}</dd></div>
                <div class="rounded-xl bg-gray-50 p-4"><dt class="text-xs font-bold uppercase text-gray-500">Payment</dt><dd class="mt-1 font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }} · {{ ucfirst($order->payment_method ?? 'Not specified') }}</dd></div>
                <div class="rounded-xl bg-gray-50 p-4 sm:col-span-2"><dt class="text-xs font-bold uppercase text-gray-500">Delivery address</dt><dd class="mt-1 font-bold text-gray-900">{{ $order->customer_address }}</dd></div>
                <div class="rounded-xl bg-gray-50 p-4 sm:col-span-2"><dt class="text-xs font-bold uppercase text-gray-500">Contact phone</dt><dd class="mt-1 font-bold text-gray-900">{{ $order->customer_phone }}</dd></div>
            </dl>
            @if($currentStep !== false)
                <div class="mt-6">
                    <h2 class="text-sm font-black text-gray-900">Order progress</h2>
                    <ol class="mt-3 grid gap-2 sm:grid-cols-4">
                        @foreach($workflowSteps as $status => $label)
                            @php $step = array_search($status, array_keys($workflowSteps), true); @endphp
                            <li class="rounded-xl px-3 py-2 text-xs font-bold {{ $step <= $currentStep ? 'bg-green-50 text-green-800 ring-1 ring-green-100' : 'bg-gray-50 text-gray-400' }}">
                                <span class="mr-1">{{ $step < $currentStep ? '✓' : ($step === $currentStep ? '•' : '○') }}</span>{{ $label }}
                            </li>
                        @endforeach
                    </ol>
                </div>
            @else
                <p class="mt-6 rounded-xl bg-red-50 p-4 text-sm font-bold text-red-800">Order {{ ucfirst($order->status) }}.</p>
            @endif
            @if(in_array($order->status, ['rider_assigned', 'picked_up', 'out_for_delivery']) && $order->rider)
                <div class="mt-4 rounded-xl bg-purple-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-purple-700">Delivery confirmation code</p>
                    <p class="mt-1 text-2xl font-black tracking-[.3em] text-purple-900">{{ $order->recipient_code ?? '----' }}</p>
                    <p class="mt-1 text-xs text-purple-700">Show this code to your assigned rider when your order arrives.</p>
                </div>
            @endif
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 sm:p-8">
            <h2 class="text-lg font-black text-gray-900">Items and total</h2>
            <p class="mt-3 whitespace-pre-wrap text-sm text-gray-700">{{ $order->items_description }}</p>
            <p class="mt-4 border-t border-gray-100 pt-4 text-xl font-black text-gray-900">₦{{ number_format($order->total_price, 2) }}</p>
            @if($order->refund_status !== 'none')
                <p class="mt-3 rounded-xl bg-orange-50 p-4 text-sm text-orange-900">Refund: <strong>{{ ucfirst($order->refund_status) }}</strong>@if($order->refund_message) · {{ $order->refund_message }}@endif</p>
            @endif
            @if($order->store_rejection_reason)
                <p class="mt-3 rounded-xl bg-red-50 p-4 text-sm text-red-800">Store note: {{ $order->store_rejection_reason }}</p>
            @endif
        </section>

        <section class="flex flex-wrap gap-3 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 sm:p-8">
            @if($order->store?->user_id)
                <a href="{{ route('chat.show', [$order->id, $order->store->user_id]) }}" class="rounded-xl bg-indigo-50 px-4 py-3 text-sm font-black text-indigo-700 hover:bg-indigo-100">Message store</a>
            @endif
            @if($order->rider?->user_id)
                <a href="{{ route('chat.show', [$order->id, $order->rider->user_id]) }}" class="rounded-xl bg-indigo-50 px-4 py-3 text-sm font-black text-indigo-700 hover:bg-indigo-100">Message rider</a>
            @endif
            @if($order->status === 'pending' && $order->store_approval_status === 'pending')
                <form action="{{ route('customer.orders.cancel', $order) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Cancel this order? Any paid amount will be refunded.')" class="rounded-xl bg-red-50 px-4 py-3 text-sm font-black text-red-700 hover:bg-red-100">Cancel order</button>
                </form>
            @endif
        </section>
    </div>
</main>
@endsection
