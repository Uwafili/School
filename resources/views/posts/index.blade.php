@extends('layouts.navbar')
@section('content')
<header class="relative h-72 md:h-96 flex items-center justify-center" style="background-image: url('{{ ('asset/body.jpg') }}'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="relative z-10 text-center">
        <h1 class="text-4xl md:text-6xl font-bold text-yellow-400 drop-shadow-lg">Welcome to FoodStore</h1>
        <p class="mt-4 text-lg md:text-2xl text-white drop-shadow">Delicious meals, delivered fresh to your door.</p>
        <a href="{{ route('register') }}" class="mt-6 inline-block bg-yellow-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition">Get Started</a>
    </div>
</header>

<section class="py-12 bg-white">
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Featured Meals</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-yellow-50 rounded-lg shadow p-6 flex flex-col items-center">
                <img src="{{ ('asset/generated.jpg') }}" alt="Pizza" class="w-32 h-32 object-cover rounded-full mb-4">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Classic Pizza</h3>
                <p class="text-gray-600 text-center">Cheesy, saucy, and topped with fresh ingredients. A timeless favorite!</p>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-6 flex flex-col items-center">
                <img src="{{ ('asset/front.avif') }}" alt="Burger" class="w-32 h-32 object-cover rounded-full mb-4">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Juicy Burger</h3>
                <p class="text-gray-600 text-center">Grilled to perfection and loaded with toppings. Satisfy your cravings!</p>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-6 flex flex-col items-center">
                <img src="{{ ('asset/brown.jpg') }}" alt="Salad" class="w-32 h-32 object-cover rounded-full mb-4">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Fresh Salad</h3>
                <p class="text-gray-600 text-center">A healthy mix of greens, veggies, and a light dressing for a fresh bite.</p>
            </div>
        </div>
    </div>
</section>



<section class="py-12 bg-yellow-50">
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">What Our Customers Say</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <img src="{{ asset('asset/speaker.jpg') }}" alt="Customer 1" class="w-16 h-16 object-cover rounded-full mb-4">
                <p class="text-gray-700 italic mb-2">"The pizza was amazing and delivery was super fast. Highly recommend FoodStore!"</p>
                <span class="font-semibold text-yellow-600">- Sarah K.</span>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <img src="{{ asset('asset/speaker2.jpg') }}" alt="Customer 2" class="w-16 h-16 object-cover rounded-full mb-4">
                <p class="text-gray-700 italic mb-2">"Fresh ingredients and great taste. My family loves the burgers!"</p>
                <span class="font-semibold text-yellow-600">- James L.</span>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <img src="{{ asset('asset/speaker3.jpg') }}" alt="Customer 3" class="w-16 h-16 object-cover rounded-full mb-4">
                <p class="text-gray-700 italic mb-2">"Healthy salads and friendly service. Will order again!"</p>
                <span class="font-semibold text-yellow-600">- Maria P.</span>
            </div>
        </div>
    </div>
</section>



<section class="py-12 bg-white">
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Our Services</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-yellow-50 rounded-lg shadow p-6 flex flex-col items-center">
                <svg class="w-12 h-12 text-yellow-500 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Fast Delivery</h3>
                <p class="text-gray-600 text-center">Get your food delivered hot and fresh, right to your doorstep, every time.</p>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-6 flex flex-col items-center">
                <svg class="w-12 h-12 text-yellow-500 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">24/7 Support</h3>
                <p class="text-gray-600 text-center">Our support team is always available to help you with your orders and questions.</p>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-6 flex flex-col items-center">
                <svg class="w-12 h-12 text-yellow-500 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 1.343-3 3 0 1.657 1.343 3 3 3s3-1.343 3-3c0-1.657-1.343-3-3-3zm0 10c-4.418 0-8-1.79-8-4V7c0-2.21 3.582-4 8-4s8 1.79 8 4v7c0 2.21-3.582 4-8 4z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Fresh Ingredients</h3>
                <p class="text-gray-600 text-center">We use only the freshest and highest quality ingredients in every meal.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-12 bg-yellow-50" style="background-image: url('{{ ('asset/open.avif') }}'); background-size: cover; background-repeat: no-repeat;">
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Become a Partner</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Delivery Man Column -->
            <div class="bg-white rounded-lg shadow p-8 flex flex-col items-center">
                <img src="{{ ('asset/location.avif') }}" alt="Delivery Man" class="w-24 h-24 object-cover rounded-full mb-4">
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">Become a Delivery Man</h3>
                <p class="text-gray-600 text-center mb-4">
                    Register as a delivery man and earn money by delivering delicious meals to customers. Flexible hours and great earnings!
                </p>
                <a href="{{ route('rider.store') }}" class="bg-yellow-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-yellow-600 transition">Register as Delivery Man</a>
            </div>
            <!-- Seller/Restaurant Column -->
            <div class="bg-white rounded-lg shadow p-8 flex flex-col items-center">
                <img src="{{ ('asset/supermarket.jpg') }}" alt="Restaurant" class="w-24 h-24 object-cover rounded-full mb-4">
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">Open Your Own Restaurant</h3>
                <p class="text-gray-600 text-center mb-4">
                    Register as a seller and open your shop to start your business. Reach more customers and grow your restaurant with us!
                </p>
                <a href="{{ route('Shop') }}" class="bg-yellow-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-yellow-600 transition">Register as Seller</a>
            </div>
        </div>
    </div>
    
</section>
@endsection