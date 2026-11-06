@extends('layouts.navbar')

@section('content')
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

        <livewire:store-approval-list />
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
