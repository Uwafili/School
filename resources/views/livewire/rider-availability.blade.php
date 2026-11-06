<div class="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-white p-6 shadow">
    <div>
        <p class="text-sm font-semibold uppercase text-gray-500">Availability</p>
        <p class="mt-1 text-xl font-bold {{ $rider->is_online ? 'text-green-700' : 'text-gray-700' }}">{{ $rider->is_online ? 'Online and available' : 'Offline' }}</p>
        <p class="mt-1 text-sm text-gray-500">{{ $rider->is_online ? 'Go offline when you need a break.' : 'Go online to receive nearby delivery requests.' }}</p>
    </div>
    <button type="button" wire:click="toggle" wire:loading.attr="disabled" class="rounded-lg px-5 py-3 font-bold text-white transition disabled:opacity-60 {{ $rider->is_online ? 'bg-gray-800 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }}">
        <span wire:loading.remove>{{ $rider->is_online ? 'Go offline' : 'Go online' }}</span><span wire:loading>Updating…</span>
    </button>
</div>
