<div class="mt-4 space-y-3 rounded-lg border-t border-gray-200 pt-4">
    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
    @if($order->status === 'rider_assigned')
        @if(!$order->rider_accepted_at)
            <button type="button" wire:click="acceptAssignment" wire:loading.attr="disabled" class="w-full rounded-lg bg-blue-600 py-3 font-bold text-white hover:bg-blue-700 disabled:opacity-60">Accept / acknowledge delivery</button>
        @elseif(!$order->rider_arrived_at)
            <button type="button" wire:click="confirmArrival" wire:loading.attr="disabled" class="w-full rounded-lg bg-blue-600 py-3 font-bold text-white hover:bg-blue-700 disabled:opacity-60">Confirm arrival at store</button>
        @else
            <button type="button" wire:click="confirmPickup" wire:loading.attr="disabled" class="w-full rounded-lg bg-green-600 py-3 font-bold text-white hover:bg-green-700 disabled:opacity-60">Confirm order picked up</button>
        @endif
        <label class="block text-sm font-semibold text-gray-700">Decline reason (optional)<textarea wire:model="declineReason" maxlength="500" rows="2" class="mt-1 w-full rounded-lg border-gray-200 px-3 py-2"></textarea></label>
        @error('declineReason')<span class="block text-xs text-red-600">{{ $message }}</span>@enderror
        <button type="button" wire:click="declineAssignment" wire:confirm="Return this delivery to the store for reassignment?" wire:loading.attr="disabled" class="w-full rounded-lg bg-red-100 py-3 font-bold text-red-700 hover:bg-red-200 disabled:opacity-60">Decline assignment</button>
    @elseif($order->status === 'picked_up')
        <button type="button" wire:click="markOutForDelivery" wire:loading.attr="disabled" class="rounded-lg bg-blue-600 px-4 py-2 font-bold text-white hover:bg-blue-700 disabled:opacity-60">Start delivery</button>
    @elseif($order->status === 'out_for_delivery')
        <form wire:submit="confirmDelivery" class="flex flex-wrap gap-2">
            <div class="min-w-0 flex-1"><input wire:model="recipientCode" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required placeholder="4-digit customer code" class="w-full rounded-lg border-gray-200 px-3 py-2">@error('recipientCode')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror</div>
            <button type="submit" wire:loading.attr="disabled" class="rounded-lg bg-gray-900 px-4 py-2 font-bold text-white hover:bg-green-700 disabled:opacity-60">Confirm delivery</button>
        </form>
        <form wire:submit="reportFailedDelivery" class="space-y-2">
            <textarea wire:model="failureReason" required maxlength="1000" rows="2" placeholder="Reason delivery could not be completed" class="w-full rounded-lg border-gray-200 px-3 py-2"></textarea>
            @error('failureReason')<span class="block text-xs text-red-600">{{ $message }}</span>@enderror
            <button type="submit" wire:loading.attr="disabled" class="rounded-lg bg-red-100 px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-200 disabled:opacity-60">Report failed delivery attempt</button>
        </form>
        @foreach($order->deliveryAttempts as $attempt)
            <p class="rounded-lg bg-red-50 p-3 text-sm text-red-800">Attempt {{ $attempt->attempted_at->format('M j, g:i A') }}: {{ $attempt->reason }}</p>
        @endforeach
    @elseif($order->status === 'delivered')
        <p class="text-sm font-semibold text-green-700">Delivery completed {{ $order->recipient_verified_at?->format('M j, Y · g:i A') }}</p>
    @endif
</div>
