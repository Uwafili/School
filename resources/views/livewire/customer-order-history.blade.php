<div wire:poll.15s>
    @if($orders->isNotEmpty())
        <section class="mb-8 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
            <div class="mb-5 flex items-end justify-between gap-4">
                <div><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Track your food</p><h2 class="mt-1 text-xl font-black text-gray-900">Order history</h2></div>
                <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-black text-yellow-800">{{ $orders->count() }} orders</span>
            </div>
            @if(session('success'))<p class="mb-4 rounded-xl bg-green-50 p-3 text-sm font-bold text-green-700">{{ session('success') }}</p>@endif
            @if(session('error'))<p class="mb-4 rounded-xl bg-red-50 p-3 text-sm font-bold text-red-700">{{ session('error') }}</p>@endif
            <div class="grid gap-4 lg:grid-cols-2">
                @foreach($orders as $order)
                    <article wire:key="customer-order-{{ $order->id }}" class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div><p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Order {{ $order->order_reference }}</p><h3 class="mt-1 text-sm font-black text-gray-900">{{ $order->store->stores ?? 'FoodStore order' }}</h3></div>
                            <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-black text-blue-700">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-600">Store approval: <strong>{{ ucfirst($order->store_approval_status) }}</strong> · Payment: <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</strong></p>
                        @if($order->store_rejection_reason)<p class="mt-2 rounded-lg bg-red-50 p-2 text-xs text-red-700">Store note: {{ $order->store_rejection_reason }}</p>@endif
                        @if($order->refund_status !== 'none')<p class="mt-2 rounded-lg bg-orange-50 p-2 text-xs text-orange-800">Refund: <strong>{{ ucfirst($order->refund_status) }}</strong>@if($order->refund_message) · {{ $order->refund_message }}@endif</p>@endif
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($order->store?->user_id)
                                <a href="{{ route('chat.show', [$order->id, $order->store->user_id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-black text-indigo-700">Message store
                                    @if(data_get($unreadMessagesByOrder, $order->id, 0) > 0)<span class="rounded-full bg-red-600 px-2 py-0.5 text-[10px] text-white">{{ data_get($unreadMessagesByOrder, $order->id) }} new</span>@endif
                                </a>
                            @endif
                            @if($order->rider?->user_id)
                                <a href="{{ route('chat.show', [$order->id, $order->rider->user_id]) }}" class="inline-flex rounded-lg bg-indigo-50 px-3 py-2 text-xs font-black text-indigo-700">Message rider</a>
                            @endif
                        </div>
                        @if($order->status === 'pending' && $order->store_approval_status === 'pending')
                            <form action="{{ route('customer.orders.cancel', $order) }}" method="POST" class="mt-3">@csrf<button type="submit" onclick="return confirm('Cancel this order? Any paid amount will be refunded.')" class="rounded-lg bg-red-50 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-100">Cancel order</button></form>
                        @endif
                        @if(in_array($order->status, ['rider_assigned', 'picked_up', 'out_for_delivery']) && $order->rider)
                            <div class="mt-4 flex items-center justify-between gap-3 border-t border-gray-200 pt-3"><div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Recipient code</p><p class="mt-1 text-2xl font-black tracking-[.3em] text-purple-700">{{ $order->recipient_code ?? '----' }}</p></div><p class="max-w-36 text-right text-[11px] leading-4 text-gray-500">Show this code to your rider to confirm delivery.</p></div>
                        @endif
                        <a href="{{ route('customer.orders.show', $order) }}" class="mt-3 inline-flex rounded-lg bg-gray-900 px-3 py-2 text-xs font-black text-white hover:bg-gray-700">View order details</a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if($orders->where('status', 'delivered')->isNotEmpty())
        <section class="mb-8 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
            <div class="mb-5"><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Share your experience</p><h2 class="mt-1 text-xl font-black text-gray-900">Rate your completed orders</h2></div>
            <div class="grid gap-4 lg:grid-cols-2">
                @foreach($orders->where('status', 'delivered') as $order)
                    @php $existingRating = $ratings->get($order->id); @endphp
                    <div wire:key="customer-order-rating-{{ $order->id }}" class="rounded-2xl border border-gray-100 bg-gray-50 p-4"><div class="flex items-center justify-between gap-3"><div><p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Order #{{ $order->id }}</p><p class="mt-1 text-sm font-black text-gray-800">{{ $order->store->stores ?? 'FoodStore store' }}</p></div><span class="text-xs font-bold text-green-600">Delivered</span></div>@if($existingRating)<p class="mt-3 text-sm font-black text-yellow-600">{{ str_repeat('★', $existingRating->rating) }}{{ str_repeat('☆', 5 - $existingRating->rating) }} <span class="ml-1 text-xs text-gray-500">Your rating</span></p>@else<form action="{{ route('order.rating', $order) }}" method="POST" class="mt-4">@csrf<div class="flex items-center gap-2"><select name="rating" required class="rounded-xl border-gray-200 bg-white px-3 py-2 text-sm font-bold text-yellow-600 focus:border-yellow-500 focus:ring-yellow-300"><option value="">Stars</option><option value="5">★★★★★</option><option value="4">★★★★☆</option><option value="3">★★★☆☆</option><option value="2">★★☆☆☆</option><option value="1">★☆☆☆☆</option></select><input name="review" maxlength="500" placeholder="Optional review" class="min-w-0 flex-1 rounded-xl border-gray-200 px-3 py-2 text-xs focus:border-yellow-500 focus:ring-yellow-300"><button type="submit" class="rounded-xl bg-gray-900 px-3 py-2 text-xs font-black text-white hover:bg-yellow-500 hover:text-gray-900">Rate</button></div></form>@endif</div>
                @endforeach
            </div>
        </section>
    @endif
</div>
