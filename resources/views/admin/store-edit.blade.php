@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-gray-100 px-4 py-10">
    <div class="mx-auto max-w-3xl rounded-xl bg-white p-6 shadow-md sm:p-8">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-yellow-600">Store management</p>
                <h1 class="mt-1 text-3xl font-bold text-gray-800">Edit store</h1>
            </div>
            <a href="{{ route('storeapprove') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Back to stores</a>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('stores.update', $store) }}" enctype="multipart/form-data" class="grid gap-5 sm:grid-cols-2">
            @csrf
            @method('PUT')
            <div>
                <label for="stores" class="mb-2 block text-sm font-semibold text-gray-700">Store name</label>
                <input id="stores" name="stores" value="{{ old('stores', $store->stores) }}" required class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-100">
            </div>
            <div>
                <label for="owner" class="mb-2 block text-sm font-semibold text-gray-700">Owner</label>
                <input id="owner" name="owner" value="{{ old('owner', $store->owner) }}" required class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-100">
            </div>
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $store->email) }}" required class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-100">
            </div>
            <div>
                <label for="phone" class="mb-2 block text-sm font-semibold text-gray-700">Phone</label>
                <input id="phone" name="phone" value="{{ old('phone', $store->phone) }}" required class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-100">
            </div>
            <div class="sm:col-span-2">
                <label for="address" class="mb-2 block text-sm font-semibold text-gray-700">Address</label>
                <input id="address" name="address" value="{{ old('address', $store->address) }}" required class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-100">
            </div>
            <div class="sm:col-span-2">
                <label for="image" class="mb-2 block text-sm font-semibold text-gray-700">Store image</label>
                <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.avif" class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-600">
                @if ($store->image)
                    <p class="mt-2 text-xs text-gray-500">Current image: {{ basename($store->image) }}</p>
                @endif
            </div>
            <div class="flex justify-end gap-3 sm:col-span-2">
                <a href="{{ route('storeapprove') }}" class="rounded-lg border border-gray-300 px-5 py-3 font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-yellow-500 px-5 py-3 font-bold text-white transition hover:bg-yellow-600">Save changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
