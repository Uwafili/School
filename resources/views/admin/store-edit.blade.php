@extends('layouts.navbar')

@section('content')
<main class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('storeapprove') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 transition hover:text-[#14213D]">← Back to store approvals</a>

        <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 bg-slate-50/80 px-5 py-6 sm:px-8">
                <p class="text-xs font-bold uppercase tracking-[.15em] text-amber-700">Store management</p>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-[#14213D] sm:text-3xl">Edit store profile</h1>
                <p class="mt-2 text-sm text-slate-500">Update the store’s public information and contact details.</p>
            </header>

            @if ($errors->any())
                <div role="alert" class="mx-5 mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 sm:mx-8">
                    <p class="font-bold">Please review the highlighted information.</p>
                    <ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('stores.update', $store) }}" enctype="multipart/form-data" class="grid gap-x-5 gap-y-5 p-5 sm:grid-cols-2 sm:p-8">
                @csrf
                @method('PUT')

                @foreach ([
                    ['stores', 'Store name', 'text', $store->stores],
                    ['owner', 'Owner name', 'text', $store->owner],
                    ['email', 'Store contact email', 'email', $store->email],
                    ['phone', 'Phone number', 'tel', $store->phone],
                ] as [$field, $label, $type, $value])
                    <div>
                        <label for="{{ $field }}" class="mb-1.5 block text-sm font-bold text-slate-700">{{ $label }} <span class="text-red-600">*</span></label>
                        <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field, $value) }}" required maxlength="255" autocomplete="{{ $field === 'email' ? 'email' : 'off' }}" class="h-11 w-full rounded-xl border px-3.5 text-sm text-slate-900 outline-none transition focus:ring-4 focus:ring-amber-100 @error($field) border-red-400 focus:border-red-500 @else border-slate-300 focus:border-amber-500 @enderror" @error($field) aria-invalid="true" aria-describedby="error-{{ $field }}" @enderror>
                        @error($field)<p id="error-{{ $field }}" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>
                @endforeach

                <div class="sm:col-span-2">
                    <label for="address" class="mb-1.5 block text-sm font-bold text-slate-700">Store address <span class="text-red-600">*</span></label>
                    <textarea id="address" name="address" rows="3" required maxlength="255" class="w-full rounded-xl border px-3.5 py-3 text-sm text-slate-900 outline-none transition focus:ring-4 focus:ring-amber-100 @error('address') border-red-400 focus:border-red-500 @else border-slate-300 focus:border-amber-500 @enderror" @error('address') aria-invalid="true" aria-describedby="error-address" @enderror>{{ old('address', $store->address) }}</textarea>
                    @error('address')<p id="error-address" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="image" class="mb-1.5 block text-sm font-bold text-slate-700">Store logo <span class="font-normal text-slate-400">· optional, JPG/PNG/AVIF up to 3 MB</span></label>
                    <div class="flex flex-col gap-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 sm:flex-row sm:items-center">
                        @if ($store->image)
                            <img src="{{ asset('storage/'.$store->image) }}" alt="{{ $store->stores }} logo" class="h-16 w-16 rounded-xl border border-slate-200 bg-white object-cover">
                        @else
                            <span class="grid h-16 w-16 shrink-0 place-items-center rounded-xl bg-amber-50 text-amber-700 ring-1 ring-amber-100" aria-label="No current store logo">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6"/></svg>
                            </span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.avif,image/jpeg,image/png,image/avif" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-white file:px-3 file:py-2 file:text-sm file:font-bold file:text-slate-700">
                            <p class="mt-1 text-xs text-slate-500">Choose a new image to replace the current logo.</p>
                            @error('image')<p class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:col-span-2 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.stores.show', $store) }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#14213D] px-5 text-sm font-black text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2">Save changes</button>
                </div>
            </form>
        </section>
    </div>
</main>
@endsection
