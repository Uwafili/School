{{-- @extends('layouts.navbar')
@section('content')
    
<!-- Full Page Wrapper -->
<div class="relative min-h-screen flex items-center justify-center overflow-hidden">

    <!-- Background Image + Gradient Animation -->
    <div class="absolute inset-0">
        <!-- Background image -->
         @php
        $foodIcons = [
            'https://cdn-icons-png.flaticon.com/512/3075/3075977.png', // Pizza
            'https://cdn-icons-png.flaticon.com/512/1046/1046784.png', // Burger
            'https://cdn-icons-png.flaticon.com/512/135/135763.png',   // Soda
        ];
    @endphp
        <!-- Animated gradient overlay -->
        <div class="absolute inset-0 animate-gradientBackground"></div>

        <!-- Floating Money Icons -->
        <div class="absolute inset-0 pointer-events-none">
       @for ($i = 0; $i < 12; $i++)
        @php
            $icon = $foodIcons[array_rand($foodIcons)];
        @endphp
        <img src="{{ $icon }}" 
             class="absolute w-16 h-16 animate-float" 
             style="left: {{ rand(0, 85) }}%; animation-delay: {{ rand(0, 10) }}s;">
    @endfor
    </div>
    </div>

    <!-- Checkout Card -->
    <div class="relative w-full max-w-md bg-white rounded-lg shadow-lg p-6 z-10 text-center">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Checkout</h2>

        <!-- Order Summary -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6 border">
            <p class="font-semibold mb-3 text-gray-700">Order Summary:</p>

            @php $grandTotal = 0; @endphp

            @if(session('cart') && count(session('cart')) > 0)
                @foreach(session('cart') as $id => $item)
                    @php
                        $lineTotal = $item['price'] * $item['quantity'];
                        $grandTotal += $lineTotal;
                    @endphp

                    <div class="flex justify-between mb-2 border-b pb-2 text-left">
                        <div>
                            <p class="font-medium text-gray-700">{{ $item['title'] }} (x{{ $item['quantity'] }})</p>
                            <p class="text-sm text-gray-500">₦{{ number_format($item['price']) }} each</p>
                        </div>
                        <div class="font-semibold text-gray-800">
                            ₦{{ number_format($lineTotal) }}
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-between mt-4 pt-2 border-t font-bold text-green-600 text-lg">
                    <span>Total:</span>
                    <span>₦{{ number_format($grandTotal) }}</span>
                </div>
            @else
                <p class="text-gray-500">Your cart is empty.</p>
            @endif
        </div>

        <!-- Proceed to Payment Form -->
        <form action="{{ route('payment.pay') }}" method="POST" class="text-center">
            @csrf
            <input type="hidden" name="amount" value="{{ $grandTotal }}">

            <!-- Delivery Method -->
            <div class="mb-6">
                <p class="font-semibold mb-2 text-gray-700">Choose Delivery Method</p>
                <label class="flex items-center justify-center gap-2 mb-2 cursor-pointer">
                    <input type="radio" name="delivery_method" value="location" required class="form-radio text-green-600">
                    <span>Deliver to my location</span>
                </label>
                <label class="flex items-center justify-center gap-2 cursor-pointer">
                    <input type="radio" name="delivery_method" value="pickup" class="form-radio text-green-600">
                    <span>Pickup Station</span>
                </label>
            </div>

            <!-- Delivery Address -->
             <p id="autoLocationText" class="mt-2 text-gray-700 font-medium hidden"></p>

            <!-- Pickup Station -->
            <div id="pickupBox" class="mb-6 hidden text-left">
                <label class="block mb-1 font-medium text-gray-700">Select Pickup Station</label>
                <select name="pickup_station"
                        class="w-full border border-gray-300 rounded p-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">-- Select Station --</option>
                    <option value="ikeja">Ikeja Hub</option>
                    <option value="lekki">Lekki Pickup</option>
                    <option value="abuja">Abuja Central</option>
                </select>
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <p class="font-semibold mb-2 text-gray-700">Choose Payment Method</p>
                <label class="flex items-center justify-center gap-2 mb-2 cursor-pointer">
                    <input type="radio" name="payment_method" value="paystack" required class="form-radio text-green-600">
                    <span>Paystack</span>
                </label>
                <label class="flex items-center justify-center gap-2 mb-2 cursor-pointer">
                    <input type="radio" name="payment_method" value="flutterwave" class="form-radio text-green-600">
                    <span>Flutterwave</span>
                </label>
                <label class="flex items-center justify-center gap-2 cursor-pointer">
                    <input type="radio" name="payment_method" value="cod" class="form-radio text-green-600">
                    <span>Cash on Delivery</span>
                </label>
            </div>

            <!-- Proceed Button -->
            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition-colors duration-200">
                Proceed to Payment
            </button>
        </form>
    </div>
</div>

<!-- Toggle Script -->
<script>
    const locationBox = document.getElementById('locationBox');
    const pickupBox = document.getElementById('pickupBox');

    document.querySelectorAll('input[name="delivery_method"]').forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.value === 'location') {
                locationBox.classList.remove('hidden');
                pickupBox.classList.add('hidden');
            } else {
                pickupBox.classList.remove('hidden');
                locationBox.classList.add('hidden');
            }
        });
    });
</script>

<!-- CSS Animations -->
<style>
@keyframes gradientBackground {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.animate-gradientBackground {
    background: linear-gradient(-45deg, #22c55e, #3b82f6, #10b981, #2563eb);
    background-size: 400% 400%;
    animation: gradientBackground 15s ease infinite;
    position: absolute;
    inset: 0;
    z-index: 0;
}

/* Floating money icon animation */
@keyframes floatUp {
    0% { transform: translateY(100vh) scale(0.5); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateY(-50px) scale(1); opacity: 0; }
}

.animate-float {
    animation: floatUp 8s linear infinite;
}



   const locationRadio = document.getElementById('locationRadio');
    const pickupRadio = document.getElementById('pickupRadio');
    const pickupBox = document.getElementById('pickupBox');
    const autoLocationText = document.getElementById('autoLocationText');

    locationRadio.addEventListener('change', function() {
        if (this.checked) {
            pickupBox.classList.add('hidden');
            autoLocationText.classList.remove('hidden');
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    autoLocationText.textContent = `Your location: Latitude ${lat}, Longitude ${lng}`;
                }, function(error) {
                    autoLocationText.textContent = 'Unable to fetch your location. Please allow location access.';
                });
            } else {
                autoLocationText.textContent = 'Geolocation is not supported by your browser.';
            }
        }
    });

    pickupRadio.addEventListener('change', function() {
        if (this.checked) {
            pickupBox.classList.remove('hidden');
            autoLocationText.classList.add('hidden');
        }
    });
</style>



    @endsection --}}








    @extends('layouts.navbar')
@section('content')
    
<!-- Full Page Wrapper -->
<div class="relative min-h-screen flex items-center justify-center overflow-hidden">

    <!-- Background Image + Gradient Animation -->
    <div class="absolute inset-0">
        <!-- Background image -->
         @php
        $foodIcons = [
            'https://cdn-icons-png.flaticon.com/512/3075/3075977.png', // Pizza
            'https://cdn-icons-png.flaticon.com/512/1046/1046784.png',   // Burger
            'https://cdn-icons-png.flaticon.com/512/135/135763.png',   // Soda
        ];
    @endphp
        <!-- Animated gradient overlay -->
        <div class="absolute inset-0 animate-gradientBackground"></div>

        <!-- Floating Money Icons -->
        <div class="absolute inset-0 pointer-events-none">
       @for ($i = 0; $i < 12; $i++)
        @php
            $icon = $foodIcons[array_rand($foodIcons)];
        @endphp
        <img src="{{ $icon }}" 
             class="absolute w-16 h-16 animate-float" 
             style="left: {{ rand(0, 85) }}%; animation-delay: {{ rand(0, 10) }}s;">
    @endfor
    </div>
    </div>

    <!-- Checkout Card -->
    <div class="relative w-full max-w-md bg-white rounded-lg shadow-lg p-6 z-10 text-center">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Checkout</h2>

        <!-- Order Summary -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6 border">
            <p class="font-semibold mb-3 text-gray-700">Order Summary:</p>

            @php $grandTotal = 0; @endphp

            @if(session('cart') && count(session('cart')) > 0)
                @foreach(session('cart') as $id => $item)
                    @php
                        $lineTotal = $item['price'] * $item['quantity'];
                        $grandTotal += $lineTotal;
                    @endphp

                    <div class="flex justify-between mb-2 border-b pb-2 text-left">
                        <div>
                            <p class="font-medium text-gray-700">{{ $item['title'] }} (x{{ $item['quantity'] }})</p>
                            <p class="text-sm text-gray-500">₦{{ number_format($item['price']) }} each</p>
                        </div>
                        <div class="font-semibold text-gray-800">
                            ₦{{ number_format($lineTotal) }}
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-between mt-4 pt-2 border-t font-bold text-green-600 text-lg">
                    <span>Total:</span>
                    <span>₦{{ number_format($grandTotal) }}</span>
                </div>
            @else
                <p class="text-gray-500">Your cart is empty.</p>
            @endif
        </div>

        <!-- Proceed to Payment Form -->
        <form action="{{ route('payment.pay') }}" method="POST" class="text-center">
            @csrf
            <input type="hidden" name="amount" value="{{ $grandTotal }}">

            <!-- Delivery Method -->
            <div class="mb-6">
                <p class="font-semibold mb-2 text-gray-700">Choose Delivery Method</p>
                <label class="flex items-center justify-center gap-2 mb-2 cursor-pointer">
                    <input type="radio" id="locationRadio" name="delivery_method" value="location" required class="form-radio text-green-600">
                    <span>Deliver to my location</span>
                </label>
                <label class="flex items-center justify-center gap-2 cursor-pointer">
                    <input type="radio" id="pickupRadio" name="delivery_method" value="pickup" class="form-radio text-green-600">
                    <span>Pickup Station</span>
                </label>
            </div>

            <!-- Delivery Address -->
            <p id="autoLocationText" class="mt-2 text-gray-700 font-medium hidden"></p>
<input type="hidden" name="delivery_address" id="deliveryAddress">
            <!-- Pickup Station -->
            <div id="pickupBox" class="mb-6 hidden text-left">
                <label class="block mb-1 font-medium text-gray-700">Select Pickup Station</label>
                <select name="pickup_station"
                        class="w-full border border-gray-300 rounded p-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">-- Select Station --</option>
                    <option value="ikeja">Ikeja Hub</option>
                    <option value="lekki">Lekki Pickup</option>
                    <option value="abuja">Abuja Central</option>
                </select>
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <p class="font-semibold mb-2 text-gray-700">Choose Payment Method</p>
                <label class="flex items-center justify-center gap-2 mb-2 cursor-pointer">
                    <input type="radio" name="payment_method" value="paystack" required class="form-radio text-green-600">
                    <span>Paystack</span>
                </label>
                <label class="flex items-center justify-center gap-2 mb-2 cursor-pointer">
                    <input type="radio" name="payment_method" value="flutterwave" class="form-radio text-green-600">
                    <span>Flutterwave</span>
                </label>
                <label class="flex items-center justify-center gap-2 cursor-pointer">
                    <input type="radio" name="payment_method" value="cod" class="form-radio text-green-600">
                    <span>Cash on Delivery</span>
                </label>
            </div>

            <!-- Proceed Button -->
            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition-colors duration-200">
                Proceed to Payment
            </button>
        </form>
        @if (session('success'))
            <div class="mb-6">
        <x-flashMsg msg="{{session('success')}}" />
       </div>
        @endif
    </div>
</div>

<!-- Toggle Script -->
<!-- Toggle Script -->
<script>
    const locationRadio = document.getElementById('locationRadio');
    const pickupRadio = document.getElementById('pickupRadio');
    const pickupBox = document.getElementById('pickupBox');
    const autoLocationText = document.getElementById('autoLocationText');
    const deliveryAddressInput = document.getElementById('deliveryAddress');

    locationRadio.addEventListener('change', function() {
        if (this.checked) {
            pickupBox.classList.add('hidden');
            autoLocationText.classList.remove('hidden');
            autoLocationText.textContent = 'Fetching your address...';
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    try {
                        // Reverse geocode using Nominatim (free, no API key needed)
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
                        const data = await response.json();
                        
                        if (data && data.display_name) {
                            const address = data.display_name;
                            autoLocationText.textContent = `Your address: ${address}`;
                            deliveryAddressInput.value = address; // Store in hidden input
                        } else {
                            autoLocationText.textContent = 'Unable to fetch address. Please try again.';
                        }
                    } catch (error) {
                        autoLocationText.textContent = 'Error fetching address. Please check your connection.';
                    }
                }, function(error) {
                    autoLocationText.textContent = 'Unable to fetch your location. Please allow location access.';
                });
            } else {
                autoLocationText.textContent = 'Geolocation is not supported by your browser.';
            }
        }
    });

    pickupRadio.addEventListener('change', function() {
        if (this.checked) {
            pickupBox.classList.remove('hidden');
            autoLocationText.classList.add('hidden');
            deliveryAddressInput.value = ''; // Clear address for pickup
        }
    });
</script>

<!-- CSS Animations -->
<style>
@keyframes gradientBackground {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.animate-gradientBackground {
    background: linear-gradient(-45deg, #22c55e, #3b82f6, #10b981, #2563eb);
    background-size: 400% 400%;
    animation: gradientBackground 15s ease infinite;
    position: absolute;
    inset: 0;
    z-index: 0;
}

/* Floating money icon animation */
@keyframes floatUp {
    0% { transform: translateY(100vh) scale(0.5); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateY(-50px) scale(1); opacity: 0; }
}

.animate-float {
    animation: floatUp 8s linear infinite;
}
</style>

@endsection