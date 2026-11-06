<button type="button" wire:click="add" wire:loading.attr="disabled" class="w-full rounded-xl bg-gray-900 py-2 text-xs font-black text-white transition hover:bg-yellow-500 hover:text-gray-900 disabled:opacity-60">
    <span wire:loading.remove wire:target="add">{{ $added ? 'Added' : 'Add to cart' }}</span><span wire:loading wire:target="add">Adding…</span>
</button>
