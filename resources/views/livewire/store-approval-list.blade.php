<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    @php
        $tabs = [
            'all' => ['label' => 'All stores', 'count' => $totalCount],
            'pending' => ['label' => 'Pending', 'count' => $pendingCount],
            'approved' => ['label' => 'Approved', 'count' => $approvedCount],
            'rejected' => ['label' => 'Rejected', 'count' => $rejectedCount],
        ];
        $statusStyles = ['pending' => 'bg-amber-50 text-amber-800 ring-amber-200', 'approved' => 'bg-green-50 text-green-800 ring-green-200', 'rejected' => 'bg-red-50 text-red-800 ring-red-200'];
    @endphp
    <div class="border-b border-slate-200 px-5 pt-5 sm:px-7">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <div><h2 class="text-xl font-black text-[#14213D]">Store directory</h2><p class="mt-1 text-sm text-slate-500">Search applications and manage marketplace access.</p></div>
            <p class="text-sm font-semibold text-slate-500"><span class="font-black text-slate-800">{{ $stores->total() }}</span> results · {{ $totalCount }} stores total</p>
        </div>
        <nav aria-label="Filter stores by approval status" class="-mb-px flex gap-5 overflow-x-auto">
            @foreach($tabs as $tabKey => $tab)
                <button type="button" wire:click="$set('status', '{{ $tabKey }}')" @if($status === $tabKey) aria-current="page" @endif class="inline-flex shrink-0 items-center gap-2 border-b-2 px-1 pb-3 text-sm font-bold transition {{ $status === $tabKey ? 'border-[#EAAA00] text-[#14213D]' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-800' }}">
                    {{ $tab['label'] }}<span class="rounded-full px-2 py-0.5 text-xs {{ $status === $tabKey ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600' }}">{{ $tab['count'] }}</span>
                </button>
            @endforeach
        </nav>
    </div>
    <div class="grid gap-3 border-b border-slate-100 bg-slate-50/70 p-4 sm:grid-cols-2 lg:grid-cols-[minmax(14rem,1fr)_auto_auto_auto] lg:items-end lg:px-7">
        <div>
            <label for="store-search-live" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Search stores</label>
            <input id="store-search-live" wire:model.live.debounce.300ms="search" placeholder="Name, owner or email" class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
        </div>
        <div><label for="filter-from-live" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Registered from</label><input id="filter-from-live" type="date" wire:model.live="dateFrom" class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"></div>
        <div><label for="filter-to-live" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Registered to</label><input id="filter-to-live" type="date" wire:model.live="dateTo" class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"></div>
        <button type="button" wire:click="clearFilters" class="h-10 rounded-lg border border-slate-300 bg-white px-4 text-sm font-bold text-slate-600 transition hover:bg-slate-100">Clear filters</button>
    </div>
    <div wire:loading.delay class="h-1 w-full animate-pulse bg-amber-300"></div>
    <div class="space-y-3 p-4 sm:p-6">
        @forelse($stores as $store)
            @php $storeStatus = $store->status; @endphp
            <article wire:key="store-{{ $store->id }}" class="group rounded-2xl border border-slate-200 bg-white p-4 transition duration-200 hover:border-amber-300 hover:shadow-md sm:p-5">
                <div class="grid gap-5 xl:grid-cols-[minmax(14rem,1.1fr)_minmax(0,2fr)_auto] xl:items-center">
                    <div class="flex min-w-0 items-center gap-4">
                        @if($store->image)<img src="{{ asset('storage/'.$store->image) }}" alt="{{ $store->stores }} logo" class="h-16 w-16 shrink-0 rounded-2xl border border-slate-200 object-cover">@else
                            <span class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-amber-50 text-amber-700 ring-1 ring-amber-100" aria-label="Store logo placeholder"><svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6M3 10.5h18"/></svg></span>
                        @endif
                        <div class="min-w-0">
                            <p class="truncate text-base font-black text-[#14213D]" title="{{ $store->stores }}">{{ $store->stores }}</p>
                            <p class="mt-1 truncate text-sm text-slate-500" title="{{ $store->owner }}">{{ $store->owner }}</p>
                            <span class="mt-2 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold ring-1 {{ $statusStyles[$storeStatus] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ ucfirst($storeStatus) }}</span>
                        </div>
                    </div>
                    <dl class="grid min-w-0 gap-x-5 gap-y-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="min-w-0"><dt class="text-[10px] font-black uppercase tracking-wider text-slate-400">Owner email</dt><dd class="mt-1 truncate text-sm font-semibold text-slate-700">{{ $store->email }}</dd></div>
                        <div class="min-w-0"><dt class="text-[10px] font-black uppercase tracking-wider text-slate-400">Phone</dt><dd class="mt-1 truncate text-sm font-semibold text-slate-700">{{ $store->phone }}</dd></div>
                        <div class="min-w-0"><dt class="text-[10px] font-black uppercase tracking-wider text-slate-400">Registered</dt><dd class="mt-1 text-sm font-semibold text-slate-700">{{ $store->created_at?->format('M j, Y') ?? '—' }}</dd></div>
                        <div class="min-w-0 sm:col-span-2 lg:col-span-3"><dt class="text-[10px] font-black uppercase tracking-wider text-slate-400">Store address</dt><dd class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $store->address }}</dd></div>
                    </dl>
                    <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-4 xl:max-w-56 xl:justify-end xl:border-0 xl:pt-0">
                        <a href="{{ route('stores.edit', $store) }}" class="inline-flex h-9 items-center rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50">Edit</a>
                        <a href="{{ route('admin.stores.show', $store) }}" class="inline-flex h-9 items-center rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50">Details</a>
                        @if($storeStatus === 'pending')
                            <button type="button" wire:click="approve({{ $store->id }})" wire:loading.attr="disabled" class="h-9 rounded-lg bg-green-600 px-3 text-xs font-black text-white transition hover:bg-green-700 disabled:opacity-60">Approve</button>
                            <button type="button" wire:click="reject({{ $store->id }})" wire:confirm="Reject {{ $store->stores }}?" wire:loading.attr="disabled" class="h-9 rounded-lg bg-red-600 px-3 text-xs font-black text-white transition hover:bg-red-700 disabled:opacity-60">Reject</button>
                        @elseif($storeStatus === 'approved')
                            <button type="button" wire:click="reject({{ $store->id }})" wire:confirm="Revoke approval for {{ $store->stores }}?" wire:loading.attr="disabled" class="h-9 rounded-lg bg-slate-100 px-3 text-xs font-black text-slate-700 transition hover:bg-slate-200">Revoke</button>
                        @else
                            <button type="button" wire:click="approve({{ $store->id }})" wire:loading.attr="disabled" class="h-9 rounded-lg bg-green-600 px-3 text-xs font-black text-white transition hover:bg-green-700">Reconsider</button>
                        @endif
                        <form method="POST" action="{{ route('admin.stores.destroy', $store) }}" onsubmit="return confirm('Delete {{ addslashes($store->stores) }} and its related records? This cannot be undone.');">@csrf @method('DELETE')<button type="submit" class="h-9 rounded-lg border border-red-200 px-3 text-xs font-bold text-red-700 transition hover:bg-red-50">Delete</button></form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center">
                <h3 class="text-base font-black text-[#14213D]">No stores match this view</h3>
                <p class="mt-1 text-sm text-slate-500">Try another status or adjust your search and date filters.</p>
            </div>
        @endforelse
        <div>{{ $stores->links() }}</div>
    </div>
</section>
