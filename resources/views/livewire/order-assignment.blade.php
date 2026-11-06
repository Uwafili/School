<form wire:submit="assign" class="space-y-4">
    <label class="block text-sm font-semibold text-gray-700">Ready order
        <select wire:model="orderId" required class="mt-1 w-full rounded-lg border-gray-300">
            <option value="">Select an order</option>
            @foreach($orders as $order)
                <option value="{{ $order->id }}">#{{ $order->id }} · {{ $order->order_reference }} · {{ $order->store->stores }} · {{ $order->customer_name }}</option>
            @endforeach
        </select>
        @error('orderId')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold text-gray-700">Available rider
        <select wire:model="riderId" required class="mt-1 w-full rounded-lg border-gray-300">
            <option value="">Select a rider</option>
            @foreach($riders as $rider)
                <option value="{{ $rider->id }}">{{ $rider->name }} · {{ $rider->phone }}</option>
            @endforeach
        </select>
        @error('riderId')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
    </label>
    @if($orders->isEmpty() || $riders->isEmpty())
        <p class="text-xs text-gray-500">Assignment requires a ready paid order and an available rider{{ $adminMode ? '.' : ' within 25 km of your store.' }}</p>
    @endif
    <button type="submit" wire:loading.attr="disabled" class="rounded-lg bg-purple-600 px-5 py-2 font-bold text-white transition hover:bg-purple-700 disabled:opacity-60">
        <span wire:loading.remove>Assign order</span><span wire:loading>Assigning…</span>
    </button>
</form>
