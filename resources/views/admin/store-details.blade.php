@extends('layouts.navbar')

@section('content')
@php
    $statusClass = match ($store->status) {
        'approved' => 'bg-green-50 text-green-800 ring-green-200',
        'rejected' => 'bg-red-50 text-red-800 ring-red-200',
        default => 'bg-amber-50 text-amber-800 ring-amber-200',
    };
@endphp
<main class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <a href="{{ route('storeapprove') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 transition hover:text-[#14213D]">← Back to store approvals</a>

        <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50/80 p-5 sm:p-7">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-4">
                        @if($store->image)
                            <img src="{{ asset('storage/'.$store->image) }}" alt="{{ $store->stores }} logo" class="h-20 w-20 shrink-0 rounded-2xl border border-slate-200 bg-white object-cover">
                        @else
                            <span class="grid h-20 w-20 shrink-0 place-items-center rounded-2xl bg-amber-50 text-amber-700 ring-1 ring-amber-100"><svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6"/></svg></span>
                        @endif
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-[.15em] text-amber-700">Store application #{{ $store->id }}</p>
                            <h1 class="mt-1 truncate text-2xl font-black tracking-tight text-[#14213D] sm:text-3xl">{{ $store->stores }}</h1>
                            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-bold capitalize ring-1 {{ $statusClass }}">{{ $store->status }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('stores.edit', $store) }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Edit store</a>
                        @if($store->status === 'pending')
                            <form method="POST" action="{{ route('store.approve', $store) }}">@csrf<button class="h-10 rounded-lg bg-green-600 px-4 text-sm font-bold text-white transition hover:bg-green-700" type="submit">Approve</button></form>
                        @elseif($store->status === 'rejected')
                            <form method="POST" action="{{ route('store.approve', $store) }}">@csrf<button class="h-10 rounded-lg bg-green-600 px-4 text-sm font-bold text-white transition hover:bg-green-700" type="submit">Reconsider</button></form>
                        @else
                            <form method="POST" action="{{ route('store.reject', $store) }}">@csrf<button class="h-10 rounded-lg border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50" type="submit" onclick="return confirm('Revoke store approval?')">Revoke approval</button></form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid gap-6 p-5 sm:p-7 lg:grid-cols-[1.4fr_.8fr]">
                <section>
                    <h2 class="text-base font-black text-[#14213D]">Store and owner information</h2>
                    <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Store name</dt><dd class="mt-1 break-words font-bold text-slate-900">{{ $store->stores }}</dd></div>
                        <div class="rounded-xl border border-slate-200 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Owner name</dt><dd class="mt-1 break-words font-bold text-slate-900">{{ $store->owner }}</dd></div>
                        <div class="rounded-xl border border-slate-200 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Store contact email</dt><dd class="mt-1 break-all font-semibold text-slate-800">{{ $store->email }}</dd></div>
                        <div class="rounded-xl border border-slate-200 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Owner account email</dt><dd class="mt-1 break-all font-semibold text-slate-800">{{ $store->user?->email ?? 'Account not found' }}</dd></div>
                        <div class="rounded-xl border border-slate-200 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Phone number</dt><dd class="mt-1 font-semibold text-slate-800">{{ $store->phone }}</dd></div>
                        <div class="rounded-xl border border-slate-200 p-4 sm:col-span-2"><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Store address</dt><dd class="mt-1 break-words font-semibold text-slate-800">{{ $store->address }}</dd></div>
                        @if($store->latitude !== null && $store->longitude !== null)
                            <div class="rounded-xl border border-slate-200 p-4 sm:col-span-2"><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Coordinates</dt><dd class="mt-1 font-mono text-sm text-slate-700">{{ $store->latitude }}, {{ $store->longitude }}</dd></div>
                        @endif
                    </dl>
                </section>

                <aside class="space-y-5">
                    <section class="rounded-xl border border-slate-200 p-4">
                        <h2 class="text-sm font-black text-[#14213D]">Application timeline</h2>
                        <ol class="mt-4 space-y-4">
                            <li class="flex gap-3">
                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#EAAA00]"></span>
                                <div><p class="text-sm font-bold text-slate-800">Application registered</p><time class="mt-0.5 block text-xs text-slate-500">{{ $store->created_at?->format('M j, Y · g:i A') ?? '—' }}</time></div>
                            </li>
                            @forelse($approvalHistory as $history)
                                <li class="flex gap-3">
                                    <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $history->status === 'approved' ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                    <div><p class="text-sm font-bold text-slate-800">{{ $history->title }}</p><time class="mt-0.5 block text-xs text-slate-500">{{ $history->created_at->format('M j, Y · g:i A') }}</time><p class="mt-1 text-xs leading-5 text-slate-500">{{ $history->message }}</p></div>
                                </li>
                            @empty
                                <li class="text-xs leading-5 text-slate-500">No prior approval updates are recorded for this application.</li>
                            @endforelse
                        </ol>
                    </section>
                    <section class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Current status</p>
                        <p class="mt-1 text-base font-black capitalize text-[#14213D]">{{ $store->status }}</p>
                        <p class="mt-1 text-xs text-slate-500">Profile last updated {{ $store->updated_at?->format('M j, Y · g:i A') ?? '—' }}</p>
                    </section>
                </aside>
            </div>
        </section>
    </div>
</main>
@endsection
