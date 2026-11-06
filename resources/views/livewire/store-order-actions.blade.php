<div class="space-y-2" wire:key="store-order-actions-{{ $order->id }}">
    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
    @if($order->status === 'pending' && $order->store_approval_status === 'pending')
        <div class="space-y-2">
            <button type="button" wire:click="approve" wire:loading.attr="disabled" class="rounded bg-green-50 px-2 py-1 text-sm font-semibold text-green-700 hover:bg-green-100 disabled:opacity-50">Approve order</button>
            <label class="block"><span class="sr-only">Rejection reason</span><input wire:model="reason" maxlength="500" placeholder="Reason (optional)" class="w-full rounded border-gray-300 px-2 py-1 text-xs"></label>
            @error('reason')<span class="block text-xs text-red-600">{{ $message }}</span>@enderror
            <button type="button" wire:click="reject" wire:confirm="Reject this order and start a refund if payment was received?" wire:loading.attr="disabled" class="rounded bg-red-50 px-2 py-1 text-sm font-semibold text-red-700 hover:bg-red-100 disabled:opacity-50">Reject order</button>
        </div>
    @elseif($order->status === 'approved')
        <button type="button" wire:click="startPreparing" wire:loading.attr="disabled" class="rounded bg-orange-50 px-2 py-1 text-sm font-semibold text-orange-700 hover:bg-orange-100 disabled:opacity-50">Start preparation</button>
    @elseif($order->status === 'preparing')
        <button type="button" wire:click="markReady" wire:loading.attr="disabled" class="rounded bg-green-50 px-2 py-1 text-sm font-semibold text-green-700 hover:bg-green-100 disabled:opacity-50">Mark ready</button>
    @elseif($order->status === 'rider_assigned' && !$order->picked_up_at)
        <p class="text-xs font-medium text-slate-500">{{ $order->rider_accepted_at ? 'Rider acknowledged the assignment.' : 'Waiting for rider acknowledgment.' }}</p>
        <button type="button" wire:click="cancelDelivery" wire:confirm="Cancel this delivery and start a refund if the order was paid?" wire:loading.attr="disabled" class="rounded bg-red-50 px-2 py-1 text-sm font-semibold text-red-700 hover:bg-red-100 disabled:opacity-50">Cancel delivery</button>
    @elseif(in_array($order->status, ['rejected', 'cancelled']) && $order->refund_status === 'pending' && $order->payment_reference && !$order->refund_reference)
        <button type="button" wire:click="retryRefund" wire:loading.attr="disabled" class="text-sm font-semibold text-orange-700">Retry refund</button>
    @endif
</div>
