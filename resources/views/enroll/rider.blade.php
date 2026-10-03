@extends('layouts.navbar')
@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<div class="min-h-screen bg-cover bg-center flex items-center justify-center" style="background-image: url('{{('asset/Delivery.jpg') }}');">
    <div class=""></div>

    <div class="relative w-full max-w-3xl p-8 mx-4">
        <div class="bg-white bg-opacity-95 rounded-2xl shadow-2xl overflow-hidden">
            <div class="md:flex">
                <!-- Left - Illustration / Title -->
                <div class="hidden md:block md:w-1/3 bg-gradient-to-b from-yellow-500 to-yellow-600 p-8 text-white">
                    <h2 class="text-2xl font-extrabold tracking-tight mb-2">
                        @if($rider && $rider->status === 'approved')
                            Your Profile
                        @else
                            Join as a Rider
                        @endif
                    </h2>
                    <p class="text-sm opacity-90">
                        @if($rider && $rider->status === 'approved')
                            Your rider account is active and ready to deliver orders.
                        @else
                            Deliver smiles — register your details and start receiving orders in your area.
                        @endif
                    </p>

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

                <!-- Right - Form or Approved Info -->
                <div class="w-full md:w-2/3 p-6 md:p-8">
                    <!-- APPROVED STATUS - Show Rider Information -->
                    @if($rider && $rider->status === 'approved')
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Your Rider Information</h3>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center text-green-800">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-semibold">Status: Approved ✅</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <!-- Profile Photo -->
                            <div class="flex justify-center mb-6">
                                <img src="{{ $rider->image ? asset('storage/' . $rider->image) : asset('images/default-avatar.png') }}" 
                                     alt="{{ $rider->name }}"
                                     class="w-32 h-32 rounded-full object-cover border-4 border-yellow-500 shadow-lg">
                            </div>

                            <!-- Information Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase">Full Name</label>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ $rider->name }}</p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase">Email</label>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ $rider->email }}</p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase">Phone</label>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ $rider->phone }}</p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase">Vehicle Type</label>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ $rider->vehicle }}</p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase">Vehicle Number</label>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ $rider->vehicle_number }}</p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase">License Number</label>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ $rider->license }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-8">
                                <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:underline">← Back to Home</a>
                                <div class="flex gap-2">
                                    <a href="{{ route('rider.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold shadow hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        📊 Go to Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>

                    <!-- PENDING/REJECTED - Show Registration Form -->
                    @else
                        <h3 class="text-xl font-bold text-gray-800 mb-4 md:hidden">Rider Registration</h3>

                        @if(session('success'))
                            <div class="mb-4 p-3 rounded bg-green-50 text-green-800 border border-green-100">{{ session('success') }}</div>
                        @endif
                        
                        @if($rider && $rider->status === 'rejected')
                            <div class="mb-4 p-3 rounded bg-red-50 text-red-800 border border-red-100">
                                <strong>Application Rejected</strong> - Your documents did not meet our requirements. Please resubmit with accurate information.
                            </div>
                        @elseif($rider && $rider->status === 'pending')
                            <div class="mb-4 p-3 rounded bg-blue-50 text-blue-800 border border-blue-100">
                                <strong>Pending Review</strong> - Your application is under review by our admin team. You will be notified once approved.
                            </div>
                        @endif

                        @php
                            $isStoreOwner = \App\Models\Store::where('user_id', auth()->id())->exists();
                        @endphp

                        @if($isStoreOwner)
                            <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6 mb-6">
                                <div class="flex items-start gap-3">
                                    <span class="text-2xl">⛔</span>
                                    <div>
                                        <h3 class="text-red-800 font-bold text-lg mb-2">Access Restricted</h3>
                                        <p class="text-red-700 text-sm mb-3">You cannot register as a rider because you already own a store.</p>
                                        <p class="text-red-700 text-sm mb-3 font-semibold">Each account can only have one role (Rider OR Store Owner)</p>
                                        <p class="text-red-700 text-sm mb-4">To become a rider, please:</p>
                                        <ul class="text-red-700 text-sm list-disc pl-5 mb-4 space-y-1">
                                            <li>Log out from your current account</li>
                                            <li>Create a new account</li>
                                            <li>Register that new account as a rider</li>
                                        </ul>
                                        <a href="{{ route('home') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition">← Back to Home</a>
                                    </div>
                                </div>
                            </div>
                        @else
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

                            <div class="rounded-xl border border-gray-200 p-4">
                                <label class="mb-2 block text-sm font-bold text-gray-700">Your delivery area</label>
                                <div id="riderLocationMap" class="h-56 overflow-hidden rounded-lg bg-gray-100" aria-label="Choose rider location on map"></div>
                                <div class="mt-3 flex flex-wrap items-center gap-3">
                                    <button id="useRiderLocation" type="button" class="rounded-lg bg-gray-900 px-3 py-2 text-xs font-bold text-white">Use my location</button>
                                    <span id="riderLocationStatus" class="text-xs text-gray-500" aria-live="polite">Select your starting location on the map.</span>
                                </div>
                                <input id="riderLatitude" name="latitude" type="hidden" value="{{ old('latitude', $rider->latitude ?? '') }}">
                                <input id="riderLongitude" name="longitude" type="hidden" value="{{ old('longitude', $rider->longitude ?? '') }}">
                                @error('latitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                @error('longitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
                        @endif
                    @endif
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const riderLocationMapElement = document.getElementById('riderLocationMap');
        if (riderLocationMapElement) {
            const riderLatitudeInput = document.getElementById('riderLatitude');
            const riderLongitudeInput = document.getElementById('riderLongitude');
            const riderLocationStatus = document.getElementById('riderLocationStatus');
            const savedLatitude = Number(riderLatitudeInput.value);
            const savedLongitude = Number(riderLongitudeInput.value);
            const riderMap = L.map(riderLocationMapElement).setView(
                savedLatitude && savedLongitude ? [savedLatitude, savedLongitude] : [6.5244, 3.3792],
                savedLatitude && savedLongitude ? 13 : 6
            );
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(riderMap);

            let riderMarker;
            function setRiderLocation(latitude, longitude) {
                riderLatitudeInput.value = latitude;
                riderLongitudeInput.value = longitude;
                if (riderMarker) riderMarker.setLatLng([latitude, longitude]);
                else riderMarker = L.marker([latitude, longitude]).addTo(riderMap);
                riderMap.setView([latitude, longitude], 14);
                riderLocationStatus.textContent = `${latitude.toFixed(5)}, ${longitude.toFixed(5)}`;
            }

            if (savedLatitude && savedLongitude) setRiderLocation(savedLatitude, savedLongitude);
            riderMap.on('click', event => setRiderLocation(event.latlng.lat, event.latlng.lng));
            document.getElementById('useRiderLocation').addEventListener('click', () => {
                if (!navigator.geolocation) {
                    riderLocationStatus.textContent = 'Location sharing is not available in this browser.';
                    return;
                }
                riderLocationStatus.textContent = 'Finding your location...';
                navigator.geolocation.getCurrentPosition(
                    position => setRiderLocation(position.coords.latitude, position.coords.longitude),
                    () => { riderLocationStatus.textContent = 'Could not access your location. Choose a point on the map.'; }
                );
            });
        }

    </script>
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


