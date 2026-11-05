@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-gray-100 px-4 py-10">
    <main class="mx-auto max-w-4xl rounded-xl bg-white p-6 shadow" data-submit-feedback>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Store details</h1>
                <p class="mt-1 text-sm text-gray-600">Application #{{ $store->id }}</p>
            </div>
            <a href="{{ route('storeapprove') }}"
                class="rounded-lg border border-gray-300 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-100">
                Back to store approvals
            </a>
        </div>

        @if($store->image)
            <img src="{{ asset('storage/' . $store->image) }}" alt="{{ $store->stores }} logo"
                class="mb-6 h-48 w-48 rounded-lg object-cover">
        @endif

        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div><dt class="text-sm font-semibold text-gray-500">Store name</dt><dd class="mt-1 text-lg text-gray-900">{{ $store->stores }}</dd></div>
            <div><dt class="text-sm font-semibold text-gray-500">Status</dt><dd class="mt-1 text-lg capitalize text-gray-900">{{ $store->status }}</dd></div>
            <div><dt class="text-sm font-semibold text-gray-500">Owner</dt><dd class="mt-1 text-lg text-gray-900">{{ $store->owner }}</dd></div>
            <div><dt class="text-sm font-semibold text-gray-500">Application email</dt><dd class="mt-1 break-all text-lg text-gray-900">{{ $store->email }}</dd></div>
            <div><dt class="text-sm font-semibold text-gray-500">Phone</dt><dd class="mt-1 text-lg text-gray-900">{{ $store->phone }}</dd></div>
            <div><dt class="text-sm font-semibold text-gray-500">Address</dt><dd class="mt-1 text-lg text-gray-900">{{ $store->address }}</dd></div>
            <div><dt class="text-sm font-semibold text-gray-500">Account email</dt><dd class="mt-1 break-all text-lg text-gray-900">{{ $store->user?->email ?? 'Account not found' }}</dd></div>
            <div><dt class="text-sm font-semibold text-gray-500">Submitted</dt><dd class="mt-1 text-lg text-gray-900">{{ $store->created_at->format('M d, Y') }}</dd></div>
            @if($store->latitude !== null && $store->longitude !== null)
                <div><dt class="text-sm font-semibold text-gray-500">Coordinates</dt><dd class="mt-1 text-lg text-gray-900">{{ $store->latitude }}, {{ $store->longitude }}</dd></div>
            @endif
        </dl>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('stores.edit', $store) }}"
                class="rounded-lg bg-yellow-500 px-4 py-2 font-bold text-white hover:bg-yellow-600">Edit store</a>
            <form method="POST" action="{{ route('admin.stores.destroy', $store) }}"
                data-loading-text="Deleting store..."
                onsubmit="return confirm('Delete this store and its related orders and ratings? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 font-bold text-white hover:bg-red-700">
                    Delete store
                </button>
            </form>
        </div>
    </main>
</div>
@endsection
