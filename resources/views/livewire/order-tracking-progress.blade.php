<div wire:poll.15s wire:key="order-tracking-progress-{{ $order->id }}" class="space-y-4">
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[.15em] text-blue-700">Delivery progress</p>
                <h2 class="mt-1 text-lg font-black capitalize tracking-tight text-[#14213D]" data-order-status>{{ str_replace('_', ' ', $order->status) }}</h2>
                <p class="mt-1 text-xs text-slate-500">Placed {{ $order->created_at->format('M j, Y · g:i A') }}</p>
            </div>
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-blue-50 text-blue-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 1 0 3-6.7L3 8m0-5v5h5m4-1v5l3 2"/></svg>
            </span>
        </div>
        @if($currentStep !== false)
            <ol class="mt-5 space-y-0">
                @foreach($workflowSteps as $status => $label)
                    @php
                        $step = array_search($status, array_keys($workflowSteps), true);
                        $statusUpdate = $order->statusHistories->where('status', $status)->last();
                    @endphp
                    <li class="relative flex min-h-11 gap-3 {{ $step < count($workflowSteps) - 1 ? 'pb-3' : '' }}">
                        @if($step < count($workflowSteps) - 1)
                            <span class="absolute left-[9px] top-5 h-[calc(100%-12px)] w-px {{ $step < $currentStep ? 'bg-blue-500' : 'bg-slate-200' }}"></span>
                        @endif
                        <span class="relative z-10 mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full {{ $step < $currentStep ? 'bg-blue-600 text-white' : ($step === $currentStep ? 'bg-white text-blue-700 ring-2 ring-blue-600' : 'bg-slate-100 text-slate-400') }}">
                            @if($step < $currentStep)
                                <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m3 8 3.2 3.2L13 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @else
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                            @endif
                        </span>
                        <div class="flex min-w-0 flex-1 items-baseline justify-between gap-2">
                            <span class="text-xs {{ $step <= $currentStep ? 'font-bold text-slate-800' : 'font-medium text-slate-400' }}">{{ $label }}</span>
                            @if($statusUpdate)
                                <time class="shrink-0 text-[10px] font-medium text-slate-400">{{ $statusUpdate->created_at->format('g:i A') }}</time>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        @else
            <p class="mt-4 rounded-xl bg-red-50 p-3 text-sm font-bold text-red-800">Order {{ str_replace('_', ' ', $order->status) }}.</p>
        @endif
        <div class="mt-4 grid grid-cols-2 gap-2 border-t border-slate-100 pt-4">
            <div class="rounded-xl bg-slate-50 p-3">
                <p class="text-[9px] font-black uppercase tracking-wider text-slate-400">Pickup</p>
                <p class="mt-1 truncate text-xs font-bold text-slate-800">{{ $order->store?->stores ?? 'Store' }}</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-3">
                <p class="text-[9px] font-black uppercase tracking-wider text-slate-400">Drop-off</p>
                <p class="mt-1 line-clamp-2 text-xs font-bold text-slate-800">{{ $order->customer_address }}</p>
            </div>
        </div>
    </section>

    @if($riderIsAssigned)
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[.15em] text-slate-400">Delivery partner</p>
                    <p class="mt-1 text-sm font-black text-[#14213D]">{{ $order->rider->user?->name ?? $order->rider->name }}</p>
                </div>
                <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-black text-green-700">ON YOUR ORDER</span>
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-slate-100 pt-3 text-xs text-slate-600">
                @if($order->rider->phone)<a href="tel:{{ $order->rider->phone }}" class="font-bold text-blue-700 hover:text-blue-900">{{ $order->rider->phone }}</a>@endif
                <span>{{ $order->rider->vehicle ?: 'Delivery rider' }}@if($order->rider->vehicle_number) · {{ $order->rider->vehicle_number }}@endif</span>
                <span class="text-slate-500">FoodStore partner</span>
            </div>
            @if($order->recipient_code && in_array($order->status, ['rider_assigned', 'picked_up', 'out_for_delivery'], true))
                <div class="mt-3 flex items-center justify-between rounded-xl bg-blue-50 px-3 py-2.5">
                    <span class="text-[10px] font-bold text-blue-800">Delivery confirmation code</span>
                    <span class="font-mono text-sm font-black tracking-[.2em] text-blue-900">{{ $order->recipient_code }}</span>
                </div>
            @endif
        </section>
    @else
        <section class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-blue-50 text-blue-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 1.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </span>
            <div><p class="text-xs font-black text-[#14213D]">We’re preparing your delivery</p><p class="mt-1 text-xs leading-5 text-slate-500">Rider details and live location will appear here once a rider is assigned.</p></div>
        </section>
    @endif
</div>
