@extends('layouts.navbar')

@section('content')
@php
    $tabs = [
        'all' => ['label' => 'All stores', 'count' => $totalCount],
        'pending' => ['label' => 'Pending', 'count' => $pendingCount],
        'approved' => ['label' => 'Approved', 'count' => $approvedCount],
        'rejected' => ['label' => 'Rejected', 'count' => $rejectedCount],
    ];
    $statusStyles = [
        'pending' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'approved' => 'bg-green-50 text-green-800 ring-green-200',
        'rejected' => 'bg-red-50 text-red-800 ring-red-200',
    ];
    $statusLabels = [
        'pending' => 'Pending review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];
@endphp

<main class="min-h-screen bg-slate-50 px-4 py-8 text-slate-900 sm:px-6 lg:px-8" data-submit-feedback>
    <div class="mx-auto max-w-7xl">
        <header class="mb-8 flex flex-col gap-5 border-b border-slate-200 pb-7 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-4">
                <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-amber-100 text-amber-700 ring-1 ring-amber-200" aria-hidden="true">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6M3 10.5h18" />
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.18em] text-amber-700">FoodStore administration</p>
                    <h1 class="mt-1 text-2xl font-black tracking-tight text-[#14213D] sm:text-3xl">Store Approval Management</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-500">Review applications, manage store profiles, and keep the marketplace up to date.</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 sm:justify-end">
                <a href="{{ route('storeapprove', request()->query()) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-amber-400" aria-label="Refresh store list">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7v5h-5M4 17v-5h5m10.5-2A7.5 7.5 0 0 0 6.7 6.7L4 9m16 6-2.7 2.3A7.5 7.5 0 0 1 4.5 14" /></svg>
                    Refresh
                </a>
                <a href="#quick-store-signup" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#14213D] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2">
                    <span aria-hidden="true" class="text-lg leading-none">+</span> Add new store
                </a>
            </div>
        </header>

        @if (session('success'))
            <div role="status" class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                <span class="font-black">✓</span><p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('warning') || session('error'))
            <div role="alert" class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <span class="font-black">!</span><p>{{ session('warning') ?? session('error') }}</p>
            </div>
        @endif

        <section aria-label="Store approval statistics" class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <article class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div><p class="text-sm font-bold text-slate-600">Pending approval</p><p class="mt-3 text-3xl font-black tracking-tight text-[#14213D]">{{ $pendingCount }}</p><p class="mt-1 text-xs text-slate-500">Applications waiting for review</p></div>
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-amber-50 text-amber-700" aria-hidden="true"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8.5"/><path stroke-linecap="round" d="M12 7v5l3 2"/></svg></span>
                </div>
                <div class="mt-5 h-1 rounded-full bg-amber-100"><div class="h-1 rounded-full bg-[#EAAA00]" style="width: {{ $totalCount ? min(100, round($pendingCount / $totalCount * 100)) : 0 }}%"></div></div>
            </article>
            <article class="rounded-2xl border border-green-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div><p class="text-sm font-bold text-slate-600">Approved stores</p><p class="mt-3 text-3xl font-black tracking-tight text-[#14213D]">{{ $approvedCount }}</p><p class="mt-1 text-xs text-slate-500">Active marketplace partners</p></div>
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-green-50 text-green-700" aria-hidden="true"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/><circle cx="12" cy="12" r="9"/></svg></span>
                </div>
                <div class="mt-5 h-1 rounded-full bg-green-100"><div class="h-1 rounded-full bg-green-600" style="width: {{ $totalCount ? min(100, round($approvedCount / $totalCount * 100)) : 0 }}%"></div></div>
            </article>
            <article class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:col-span-2 xl:col-span-1">
                <div class="flex items-start justify-between gap-4">
                    <div><p class="text-sm font-bold text-slate-600">Rejected applications</p><p class="mt-3 text-3xl font-black tracking-tight text-[#14213D]">{{ $rejectedCount }}</p><p class="mt-1 text-xs text-slate-500">Applications that need attention</p></div>
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-red-50 text-red-700" aria-hidden="true"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="m9 9 6 6m0-6-6 6"/></svg></span>
                </div>
                <div class="mt-5 h-1 rounded-full bg-red-100"><div class="h-1 rounded-full bg-red-600" style="width: {{ $totalCount ? min(100, round($rejectedCount / $totalCount * 100)) : 0 }}%"></div></div>
            </article>
        </section>

        <section id="quick-store-signup" class="mb-8 scroll-mt-24 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-5 sm:px-7">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-orange-50 text-orange-600" aria-hidden="true"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/></svg></span>
                    <div><h2 class="text-lg font-black text-[#14213D]">Quick sign up: Store owner</h2><p class="mt-1 text-sm text-slate-500">Create a pending store profile. The owner will receive a password setup link by email.</p></div>
                </div>
            </div>
            <form action="{{ route('admin.stores.store') }}" method="POST" enctype="multipart/form-data" data-loading-text="Creating store owner..." class="grid gap-x-5 gap-y-4 p-5 sm:grid-cols-2 sm:p-7 xl:grid-cols-3">
                @csrf
                @foreach ([
                    ['stores', 'Store name', 'text', 'Enter store name'],
                    ['owner', 'Owner name', 'text', 'Enter owner name'],
                    ['email', 'Owner email', 'email', 'owner@example.com'],
                    ['phone', 'Phone number', 'tel', '+234'],
                    ['address', 'Store address', 'text', 'Street, city, region'],
                ] as [$field, $label, $type, $placeholder])
                    <div class="{{ $field === 'address' ? 'sm:col-span-2' : '' }}">
                        <label for="store-{{ $field }}" class="mb-1.5 block text-sm font-bold text-slate-700">{{ $label }} <span class="text-red-600">*</span></label>
                        <input id="store-{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field) }}" placeholder="{{ $placeholder }}" maxlength="255" required autocomplete="{{ $field === 'email' ? 'email' : ($field === 'owner' ? 'name' : 'off') }}" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-100" @error($field) aria-invalid="true" aria-describedby="error-{{ $field }}" @enderror>
                        @error($field)<p id="error-{{ $field }}" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                <div class="sm:col-span-2 xl:col-span-3">
                    <label for="store-image" class="mb-1.5 block text-sm font-bold text-slate-700">Store logo <span class="font-normal text-slate-400">· optional, JPG/PNG/AVIF up to 3 MB</span></label>
                    <input id="store-image" name="image" type="file" accept=".jpg,.jpeg,.png,.avif,image/jpeg,image/png,image/avif" class="block w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-2.5 text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-white file:px-3 file:py-1.5 file:text-sm file:font-bold file:text-slate-700 hover:border-amber-400">
                    @error('image')<p class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4 sm:col-span-2 xl:col-span-3">
                    <p class="text-xs text-slate-500">New applications start with pending approval.</p>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#EAAA00] px-5 py-2.5 text-sm font-black text-[#14213D] transition hover:bg-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7 7 7-7 7"/></svg>
                        Create store owner
                    </button>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 pt-5 sm:px-7">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div><h2 class="text-xl font-black text-[#14213D]">Store directory</h2><p class="mt-1 text-sm text-slate-500">Search applications and manage marketplace access.</p></div>
                    <p class="text-sm font-semibold text-slate-500"><span class="font-black text-slate-800">{{ $resultCount }}</span> of {{ $totalCount }} stores</p>
                </div>
                <nav aria-label="Filter stores by approval status" class="-mb-px flex gap-5 overflow-x-auto">
                    @foreach ($tabs as $tabKey => $tab)
                        <a href="{{ route('storeapprove', array_merge(request()->query(), ['status' => $tabKey, 'page' => null])) }}" @if($activeStatus === $tabKey) aria-current="page" @endif class="inline-flex shrink-0 items-center gap-2 border-b-2 px-1 pb-3 text-sm font-bold transition {{ $activeStatus === $tabKey ? 'border-[#EAAA00] text-[#14213D]' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-800' }}">
                            {{ $tab['label'] }}<span class="rounded-full px-2 py-0.5 text-xs {{ $activeStatus === $tabKey ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600' }}">{{ $tab['count'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <form method="GET" action="{{ route('storeapprove') }}" class="grid gap-3 border-b border-slate-100 bg-slate-50/70 p-4 sm:grid-cols-2 lg:grid-cols-[minmax(14rem,1fr)_auto_auto_auto_auto] lg:items-end lg:px-7">
                <input type="hidden" name="status" value="{{ $activeStatus }}">
                <div>
                    <label for="store-search" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Search stores</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path stroke-linecap="round" d="m16 16 4 4"/></svg>
                        <input id="store-search" name="search" value="{{ $search }}" placeholder="Name, owner or email" class="h-10 w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    </div>
                </div>
                <div><label for="filter-from" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Registered from</label><input id="filter-from" name="from" type="date" value="{{ $dateFrom }}" class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"></div>
                <div><label for="filter-to" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Registered to</label><input id="filter-to" name="to" type="date" value="{{ $dateTo }}" class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"></div>
                <button type="submit" class="h-10 rounded-lg bg-[#14213D] px-4 text-sm font-bold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400">Apply filters</button>
                <a href="{{ route('storeapprove', ['status' => $activeStatus]) }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-bold text-slate-600 transition hover:bg-slate-100">Clear</a>
            </form>

            <div class="space-y-3 p-4 sm:p-6">
                @forelse ($stores as $store)
                    @php
                        $storeStatus = $store->status;
                        $badgeStyle = $statusStyles[$storeStatus] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
                        $badgeLabel = $statusLabels[$storeStatus] ?? ucfirst($storeStatus);
                    @endphp
                    <article class="group rounded-2xl border border-slate-200 bg-white p-4 transition duration-200 hover:border-amber-300 hover:shadow-md sm:p-5">
                        <div class="grid gap-5 xl:grid-cols-[minmax(14rem,1.1fr)_minmax(0,2fr)_auto] xl:items-center">
                            <div class="flex min-w-0 items-center gap-4">
                                @if($store->image)
                                    <img src="{{ asset('storage/'.$store->image) }}" alt="{{ $store->stores }} logo" class="h-16 w-16 shrink-0 rounded-2xl border border-slate-200 object-cover">
                                @else
                                    <span class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-amber-50 text-amber-700 ring-1 ring-amber-100" aria-label="Store logo placeholder">
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6M3 10.5h18"/></svg>
                                    </span>
                                @endif
                                <div class="min-w-0">
                                    <p class="truncate text-base font-black text-[#14213D]" title="{{ $store->stores }}">{{ $store->stores }}</p>
                                    <p class="mt-1 truncate text-sm text-slate-500" title="{{ $store->owner }}">{{ $store->owner }}</p>
                                    <span class="mt-2 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold ring-1 {{ $badgeStyle }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $storeStatus === 'approved' ? 'bg-green-600' : ($storeStatus === 'rejected' ? 'bg-red-600' : 'bg-amber-500') }}"></span>
                                        {{ $badgeLabel }}
                                    </span>
                                </div>
                            </div>

                            <dl class="grid min-w-0 gap-x-5 gap-y-3 sm:grid-cols-2 lg:grid-cols-3">
                                <div class="min-w-0"><dt class="text-[10px] font-black uppercase tracking-wider text-slate-400">Owner email</dt><dd class="mt-1 truncate text-sm font-semibold text-slate-700" title="{{ $store->email }}">{{ $store->email }}</dd></div>
                                <div class="min-w-0"><dt class="text-[10px] font-black uppercase tracking-wider text-slate-400">Phone</dt><dd class="mt-1 truncate text-sm font-semibold text-slate-700">{{ $store->phone }}</dd></div>
                                <div class="min-w-0"><dt class="text-[10px] font-black uppercase tracking-wider text-slate-400">Registered</dt><dd class="mt-1 text-sm font-semibold text-slate-700">{{ $store->created_at?->format('M j, Y') ?? '—' }}</dd></div>
                                <div class="min-w-0 sm:col-span-2 lg:col-span-3"><dt class="text-[10px] font-black uppercase tracking-wider text-slate-400">Store address</dt><dd class="mt-1 line-clamp-2 text-sm text-slate-600" title="{{ $store->address }}">{{ $store->address }}</dd></div>
                            </dl>

                            <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-4 xl:max-w-56 xl:justify-end xl:border-0 xl:pt-0">
                                <a href="{{ route('stores.edit', $store) }}" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50" aria-label="Edit {{ $store->stores }}">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 5 4 4M4 20l4.2-.8L19 8.4 15.6 5 4.8 15.8 4 20Z"/></svg>Edit
                                </a>
                                <a href="{{ route('admin.stores.show', $store) }}" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50" aria-label="View {{ $store->stores }} details">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>Details
                                </a>
                                <button type="button" data-delete-store data-store-name="{{ $store->stores }}" data-delete-url="{{ route('admin.stores.destroy', $store) }}" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-red-200 px-3 text-xs font-bold text-red-700 transition hover:border-red-300 hover:bg-red-50" aria-label="Delete {{ $store->stores }}">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M6 7l1 14h10l1-14M9 7V4h6v3"/></svg>Delete
                                </button>
                                @if($storeStatus === 'pending')
                                    <form method="POST" action="{{ route('store.approve', $store) }}" data-loading-text="Approving...">@csrf<button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-green-600 px-3 text-xs font-black text-white transition hover:bg-green-700">Approve</button></form>
                                    <form method="POST" action="{{ route('store.reject', $store) }}" data-loading-text="Rejecting...">@csrf<button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-red-600 px-3 text-xs font-black text-white transition hover:bg-red-700">Reject</button></form>
                                @elseif($storeStatus === 'approved')
                                    <form method="POST" action="{{ route('store.reject', $store) }}" data-loading-text="Revoking approval...">@csrf<button type="submit" onclick="return confirm('Revoke this store approval?')" class="inline-flex h-9 items-center justify-center rounded-lg bg-slate-100 px-3 text-xs font-black text-slate-700 transition hover:bg-slate-200">Revoke</button></form>
                                @else
                                    <form method="POST" action="{{ route('store.approve', $store) }}" data-loading-text="Reconsidering...">@csrf<button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-green-600 px-3 text-xs font-black text-white transition hover:bg-green-700">Reconsider</button></form>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-white text-slate-400 ring-1 ring-slate-200"><svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6"/></svg></span>
                        <h3 class="mt-4 text-base font-black text-[#14213D]">No stores match this view</h3>
                        <p class="mt-1 text-sm text-slate-500">Try another status or adjust your search and date filters.</p>
                        <a href="{{ route('storeapprove') }}" class="mt-4 inline-flex rounded-lg bg-white px-4 py-2 text-sm font-bold text-slate-700 ring-1 ring-slate-300 transition hover:bg-slate-100">Clear all filters</a>
                    </div>
                @endforelse
            </div>
            @if($stores->hasPages())
                <div class="border-t border-slate-200 px-5 py-4 sm:px-7">{{ $stores->links() }}</div>
            @endif
        </section>
    </div>
</main>

<dialog data-store-delete-dialog class="w-[calc(100%-2rem)] max-w-md rounded-2xl p-0 shadow-2xl backdrop:bg-slate-950/50">
    <form method="POST" data-store-delete-form data-loading-text="Deleting store..." class="p-6">
        @csrf
        @method('DELETE')
        <div class="flex items-start gap-4">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-red-50 text-red-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M6 7l1 14h10l1-14M9 7V4h6v3"/></svg></span>
            <div><h2 class="text-lg font-black text-[#14213D]">Delete this store?</h2><p class="mt-2 text-sm leading-6 text-slate-600">You’re about to delete <strong data-delete-store-name class="text-slate-900"></strong>. Related records may also be affected. This action cannot be undone.</p></div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" data-close-delete-dialog class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Cancel</button>
            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-black text-white transition hover:bg-red-700">Delete store</button>
        </div>
    </form>
</dialog>
@endsection
