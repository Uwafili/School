@extends('layouts.navbar')
@section('content')

<div class="min-h-screen bg-cover bg-center flex items-center justify-center" style="background-image: url('{{('asset/Delivery.jpg') }}');">
    <div class=""></div>

    <div class="relative w-full max-w-3xl p-8 mx-4">
        <div class="bg-white bg-opacity-95 rounded-2xl shadow-2xl overflow-hidden">
            <div class="md:flex">
                <!-- Left - Illustration / Title -->
                <div class="hidden md:block md:w-1/3 bg-gradient-to-b from-yellow-500 to-yellow-600 p-8 text-white">
                    <h2 class="text-2xl font-extrabold tracking-tight mb-2">Join as a Rider</h2>
                    <p class="text-sm opacity-90">Deliver smiles — register your details and start receiving orders in your area.</p>

                    <div class="mt-6 flex items-center">
                        <svg class="w-10 h-10 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14l1-6-3-6H5a2 2 0 00-2 2z"/>
                        </svg>
                        <div>
                            <div class="text-xs font-semibold">Fast payouts</div>
                            <div class="text-xs opacity-90">Weekly settlements</div>
                        </div>
                    </div>
                </div>

                <!-- Right - Form -->
                <div class="w-full md:w-2/3 p-6 md:p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 md:hidden">Rider Registration</h3>

                    @if(session('success'))
                        <div class="mb-4 p-3 rounded bg-green-50 text-green-800 border border-green-100">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('rider.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                          @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input name="name" value="{{ old('name') }}" type="text"
                                class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                placeholder="Jane Doe">
                            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input name="email" value="{{ old('email') }}" type="text"
                                    class="mt-1 block w-full rounded-lg border border-gray-200 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                    placeholder="you@example.com">
                                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <input name="phone" value="{{ old('phone') }}" type="tel"
                                    class="mt-1 block w-full rounded-lg border border-gray-200 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                    placeholder="+234 800 000 0000">
                                @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Vehicle Type</label>
                                <input name="vehicle" value="{{ old('vehicle') }}" type="text"
                                    class="mt-1 block w-full rounded-lg border border-gray-200 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                    placeholder="Motorbike / Bicycle / Car">
                                @error('vehicle') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Vehicle Number</label>
                                <input name="vehicle_number" value="{{ old('vehicle_number') }}" type="text"
                                    class="mt-1 block w-full rounded-lg border border-gray-200 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                    placeholder="ABC-1234">
                                @error('vehicle_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">License Number</label>
                            <input name="license" value="{{ old('license') }}" type="text"
                                class="mt-1 block w-full rounded-lg border border-gray-200 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                placeholder="DL-000000">
                            @error('license') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Profile Photo (optional)</label>

                            <div class="mt-1 flex items-center gap-4">
                                <div class="relative">
                                    <img id="preview" src="{{ asset('images/default-avatar.png') }}" alt="Preview"
                                         class="w-20 h-20 rounded-full object-cover border border-gray-200 bg-gray-50">
                                </div>

                                <div class="flex-1">
                                    <label class="flex items-center justify-center px-3 py-2 rounded-lg bg-white border border-gray-200 cursor-pointer hover:bg-gray-50">
                                        <input id="image" name="image" type="file" accept="image/*" class="sr-only" onchange="previewImage(event)">
                                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14l1-6-3-6H5a2 2 0 00-2 2z"/>
                                        </svg>
                                        <span class="text-sm text-gray-700">Choose image</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 3MB</p>
                                </div>
                            </div>

                            @error('image') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-semibold shadow hover:from-yellow-600 hover:to-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-3.866 3.582-7 8-7v14c-4.418 0-8-3.134-8-7z"/>
                                </svg>
                                Register Rider
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function previewImage(event){
                const input = event.target;
                if (!input.files || !input.files[0]) return;
                const reader = new FileReader();
                reader.onload = e => document.getElementById('preview').src = e.target.result;
                reader.readAsDataURL(input.files[0]);
            }
        </script>
    </div>
</div>

@endsection
