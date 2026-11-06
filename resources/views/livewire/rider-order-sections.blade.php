<div>
    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"><p class="text-xs font-black uppercase tracking-wide text-gray-500">Assigned</p><p class="mt-2 text-3xl font-black text-yellow-600">{{ $assignedOrders->count() }}</p><p class="mt-1 text-sm text-gray-500">Awaiting pickup</p></article>
        <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"><p class="text-xs font-black uppercase tracking-wide text-gray-500">In progress</p><p class="mt-2 text-3xl font-black text-blue-600">{{ $activeOrders->count() }}</p><p class="mt-1 text-sm text-gray-500">Picked up or on the way</p></article>
        <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"><p class="text-xs font-black uppercase tracking-wide text-gray-500">Completed</p><p class="mt-2 text-3xl font-black text-green-600">{{ $completedOrders->count() }}</p><p class="mt-1 text-sm text-gray-500">Delivery history</p></article>
    </div>

    <section class="mb-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
        <div class="mb-5"><p class="text-xs font-black uppercase tracking-wide text-yellow-600">Your queue</p><h2 class="mt-1 text-xl font-black text-gray-900">Orders assigned to you</h2></div>
        <div class="space-y-5">
            @forelse($assignedOrders as $order)
                <article wire:key="assigned-order-{{ $order->id }}" class="rounded-xl border border-yellow-200 bg-yellow-50 p-5">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div><h3 class="font-black text-gray-900">Order #{{ $order->id }} · {{ $order->customer_name }}</h3><dl class="mt-3 space-y-2 text-sm text-gray-700"><div><dt class="inline font-bold">Phone:</dt> <dd class="inline">{{ $order->customer_phone }}</dd></div><div><dt class="inline font-bold">Delivery address:</dt> <dd class="inline">{{ $order->customer_address }}</dd></div><div><dt class="inline font-bold">Pickup:</dt> <dd class="inline">{{ $order->store?->stores ?? 'Store' }} · {{ $order->store?->address ?? 'Pickup location' }}</dd></div></dl></div>
                        <div><p class="text-sm font-bold text-gray-600">Order details</p><p class="mt-2 rounded-lg bg-white p-3 text-sm text-gray-700">{{ $order->items_description }}</p><p class="mt-3 font-black text-gray-900">Total ₦{{ number_format($order->total_price, 2) }}</p><p class="font-bold text-green-700">Delivery pay ₦{{ number_format($order->delivery_fee, 2) }}</p></div>
                    </div>
                    <livewire:rider-delivery-actions :order="$order" :key="'rider-action-'.$order->id" />
                </article>
            @empty
                <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-500">No orders are waiting for your acknowledgment or pickup.</p>
            @endforelse
        </div>
    </section>

    <section class="mb-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
        <div class="mb-5"><p class="text-xs font-black uppercase tracking-wide text-blue-600">On the road</p><h2 class="mt-1 text-xl font-black text-gray-900">Active deliveries</h2></div>
        <div class="space-y-4">
            @forelse($activeOrders as $order)
                <article wire:key="active-order-{{ $order->id }}" class="rounded-xl border border-blue-100 bg-blue-50 p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3"><div><h3 class="font-black text-gray-900">Order #{{ $order->id }} · {{ $order->customer_name }}</h3><p class="mt-1 text-sm text-gray-600">{{ $order->customer_address }} · {{ $order->customer_phone }}</p><p class="mt-2 font-bold text-gray-800">₦{{ number_format($order->total_price, 2) }}</p></div><span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-blue-800">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></div>
                    <livewire:rider-delivery-actions :order="$order" :key="'rider-action-'.$order->id" />
                </article>
            @empty
                <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-500">No active deliveries right now.</p>
            @endforelse
        </div>
    </section>

    <section class="mb-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
        <h2 class="mb-4 text-xl font-black text-gray-900">Completed deliveries and history</h2>
        <div class="space-y-3">
            @forelse($completedOrders as $order)
                <article wire:key="completed-order-{{ $order->id }}" class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-green-50 p-4"><div><p class="font-bold text-gray-900">{{ $order->order_reference }} · {{ $order->customer_name }}</p><p class="text-sm text-gray-600">{{ $order->customer_address }}</p></div><p class="text-sm font-semibold text-green-700">Delivered {{ $order->recipient_verified_at?->format('M j, Y · g:i A') ?? $order->updated_at->format('M j, Y · g:i A') }}</p></article>
            @empty
                <p class="text-sm text-gray-500">No completed deliveries yet.</p>
            @endforelse
        </div>
    </section>

    @if($rejectedOrders->isNotEmpty())
        <section class="mb-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-7">
            <h2 class="mb-4 text-xl font-black text-gray-900">Rejected orders</h2>
            <div class="space-y-3">@foreach($rejectedOrders as $order)<article wire:key="rejected-order-{{ $order->id }}" class="rounded-xl bg-red-50 p-4"><p class="font-bold text-gray-900">#{{ $order->id }} · {{ $order->customer_name }}</p><p class="mt-1 text-sm text-gray-600">{{ $order->customer_address }}</p></article>@endforeach</div>
        </section>
    @endif

    @if($allOrders->isEmpty())
        <div class="rounded-2xl border border-dashed border-yellow-300 bg-yellow-50 p-8 text-center"><p class="text-lg font-black text-yellow-800">No orders assigned yet</p><p class="mt-1 text-sm text-yellow-700">Nearby paid orders and jobs assigned by stores will appear here.</p></div>
    @endif
</div>
